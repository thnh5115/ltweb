<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php'; // Reuse helper functions

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// --- Auth guard ---
// Đăng nhập admin dùng chung /api/auth.php (action=login, role ADMIN)
if ($action === 'admin_login') {
    jsonResponse(false, 'Vui lòng đăng nhập qua /api/auth.php');
}

// Đồng bộ session admin từ user role ADMIN
if (!isset($_SESSION['admin_id']) && isset($_SESSION['user_id'], $_SESSION['user_role']) && $_SESSION['user_role'] === 'ADMIN') {
    $_SESSION['admin_id'] = $_SESSION['user_id'];
    $_SESSION['admin_name'] = $_SESSION['user_name'] ?? 'Admin';
    $_SESSION['admin_email'] = $_SESSION['user_email'] ?? null;
}

if (!isset($_SESSION['admin_id'])) {
    jsonResponse(false, 'Unauthorized');
}

$currentAdminRole = $_SESSION['admin_role'] ?? 'ADMIN';

function ensurePermission(array $allowedRoles, $currentRole)
{
    if (!in_array($currentRole, $allowedRoles, true)) {
        jsonResponse(false, 'Bạn không có quyền thực hiện thao tác này');
    }
}

// --- Dashboard Summary ---
if ($action === 'get_dashboard_summary') {
    ensurePermission(['SUPER_ADMIN','ADMIN','STAFF'], $currentAdminRole);
    $totalUsers = (int) $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $totalCategories = (int) $pdo->query("SELECT COUNT(*) FROM categories WHERE status != 'DELETED'")->fetchColumn();
    $totalTransactions = (int) $pdo->query("SELECT COUNT(*) FROM transactions")->fetchColumn();

    $stmtExpense = $pdo->prepare("
        SELECT COALESCE(SUM(amount), 0)
        FROM transactions
        WHERE type = 'EXPENSE'
          AND YEAR(transaction_date) = YEAR(CURDATE())
          AND MONTH(transaction_date) = MONTH(CURDATE())
    ");
    $stmtExpense->execute();
    $totalExpenseThisMonth = (float) $stmtExpense->fetchColumn();

    $stmtPending = $pdo->prepare("SELECT COUNT(*) FROM bills WHERE status = 'PENDING'");
    $stmtPending->execute();
    $pendingTransactions = (int) $stmtPending->fetchColumn();

    jsonResponse(true, 'Success', [
        'total_users' => $totalUsers,
        'total_categories' => $totalCategories,
        'total_transactions' => $totalTransactions,
        'total_expense_this_month' => $totalExpenseThisMonth,
        'pending_transactions' => $pendingTransactions
    ]);
}

// --- Dashboard Chart (monthly expense current year) ---
elseif ($action === 'get_dashboard_chart') {
    ensurePermission(['SUPER_ADMIN','ADMIN','STAFF'], $currentAdminRole);
    $stmt = $pdo->prepare("
        SELECT MONTH(transaction_date) AS m, COALESCE(SUM(amount),0) AS total
        FROM transactions
        WHERE type = 'EXPENSE' AND YEAR(transaction_date) = YEAR(CURDATE())
        GROUP BY MONTH(transaction_date)
        ORDER BY MONTH(transaction_date)
    ");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $values = [];
    for ($i = 1; $i <= 12; $i++) {
        $labels[] = 'T' . $i;
        $values[] = 0;
    }
    foreach ($rows as $row) {
        $idx = (int) $row['m'] - 1;
        if ($idx >= 0 && $idx < 12) {
            $values[$idx] = (float) $row['total'];
        }
    }

    jsonResponse(true, 'Success', [
        'labels' => $labels,
        'values' => $values
    ]);
}

// --- Recent Transactions ---
elseif ($action === 'get_recent_transactions') {
    ensurePermission(['SUPER_ADMIN','ADMIN','STAFF'], $currentAdminRole);
    $stmt = $pdo->prepare("
        SELECT 
            t.id,
            t.amount,
            t.transaction_date,
            t.type,
            t.note,
            u.fullname AS user_name,
            c.name AS category_name
        FROM transactions t
        INNER JOIN users u ON u.id = t.user_id
        LEFT JOIN categories c ON c.id = t.category_id
        ORDER BY t.transaction_date DESC, t.id DESC
        LIMIT 10
    ");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = array_map(function ($row) {
        return [
            'id' => (int) $row['id'],
            'user' => $row['user_name'] ?? 'N/A',
            'type' => strtolower($row['type'] ?? ''),
            'category' => $row['category_name'] ?? '-',
            'amount' => (float) $row['amount'],
            'date' => $row['transaction_date'],
            'status' => 'completed',
            'note' => $row['note'] ?? ''
        ];
    }, $rows);

    jsonResponse(true, 'Success', $data);
}

// --- Recent Users ---
elseif ($action === 'admin_get_logs') {
    ensurePermission(['SUPER_ADMIN','ADMIN'], $currentAdminRole);

    $page = max(1, (int) ($_GET['page'] ?? $_POST['page'] ?? 1));
    $limit = max(1, min(100, (int) ($_GET['limit'] ?? $_POST['limit'] ?? 20)));
    $offset = ($page - 1) * $limit;

    $search = trim($_GET['search'] ?? $_POST['search'] ?? '');
    $actionFilter = trim($_GET['log_action'] ?? $_POST['log_action'] ?? '');
    $dateFrom = trim($_GET['date_from'] ?? $_POST['date_from'] ?? '');
    $dateTo = trim($_GET['date_to'] ?? $_POST['date_to'] ?? '');

    $where = [];
    $params = [];

    if ($search !== '') {
        $where[] = "(LOWER(u.fullname) LIKE :search OR LOWER(al.description) LIKE :search OR al.ip_address LIKE :search)";
        $params[':search'] = '%' . strtolower($search) . '%';
    }

    if ($actionFilter !== '') {
        $where[] = 'al.action = :action_filter';
        $params[':action_filter'] = $actionFilter;
    }

    if ($dateFrom !== '') {
        $where[] = 'al.created_at >= :date_from';
        $params[':date_from'] = $dateFrom . ' 00:00:00';
    }

    if ($dateTo !== '') {
        $where[] = 'al.created_at <= :date_to';
        $params[':date_to'] = $dateTo . ' 23:59:59';
    }

    $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM admin_logs al LEFT JOIN users u ON u.id = al.admin_id $whereSql");
    $stmtCount->execute($params);
    $total = (int) $stmtCount->fetchColumn();

    $stmt = $pdo->prepare("
        SELECT al.*, u.fullname AS admin_name, u.email AS admin_email
        FROM admin_logs al
        LEFT JOIN users u ON u.id = al.admin_id
        $whereSql
        ORDER BY al.created_at DESC
        LIMIT :limit OFFSET :offset
    ");
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $logs = array_map(function ($row) {
        return [
            'time' => $row['created_at'],
            'user' => $row['admin_name'] ?: 'Hệ thống',
            'email' => $row['admin_email'] ?? null,
            'action' => $row['action'],
            'ip' => $row['ip_address'] ?? '',
            'note' => $row['description'] ?? '',
            'target_type' => $row['target_type'],
            'target_id' => $row['target_id'],
            'meta' => $row['meta'] ? json_decode($row['meta'], true) : null
        ];
    }, $rows);

    jsonResponse(true, 'Success', [
        'items' => $logs,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total
        ]
    ]);
}

// --- Fallback ---
else {
    jsonResponse(false, 'Invalid action');
}
        $params[':status'] = strtoupper($status);
    }

    $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM users u $whereSql");
    $stmtCount->execute($params);
    $total = (int) $stmtCount->fetchColumn();

    $sql = "
        SELECT 
            u.id,
            u.fullname,
            u.email,
            u.role,
            u.status,
            u.created_at,
            COALESCE(tx.transactions_count, 0) AS transactions,
            COALESCE(tx.total_expense, 0) AS expense
        FROM users u
        LEFT JOIN (
            SELECT user_id,
                   COUNT(*) AS transactions_count,
                   SUM(CASE WHEN type = 'EXPENSE' THEN amount ELSE 0 END) AS total_expense
            FROM transactions
            GROUP BY user_id
        ) tx ON tx.user_id = u.id
        $whereSql
        ORDER BY u.created_at DESC, u.id DESC
        LIMIT :limit OFFSET :offset
    ";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $summary = [
        'total' => $total,
        'active' => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'ACTIVE'")->fetchColumn(),
        'banned' => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE status = 'BANNED'")->fetchColumn(),
        'new' => (int) $pdo->query("SELECT COUNT(*) FROM users WHERE YEAR(created_at)=YEAR(CURDATE()) AND MONTH(created_at)=MONTH(CURDATE())")->fetchColumn(),
    ];

    jsonResponse(true, 'Success', [
        'items' => $items,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total
        ],
        'summary' => $summary
    ]);
} elseif ($action === 'admin_get_user_detail') {
    ensurePermission(['SUPER_ADMIN','ADMIN'], $currentAdminRole);
    $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
    if ($id <= 0) {
        jsonResponse(false, 'Thiếu id người dùng');
    }

    $stmt = $pdo->prepare("
        SELECT 
            u.id,
            u.fullname,
            u.email,
            u.role,
            u.status,
            u.phone,
            u.avatar_url,
            u.created_at,
            COALESCE(tx.transactions_count, 0) AS transactions,
            COALESCE(tx.total_expense, 0) AS expense
        FROM users u
        LEFT JOIN (
            SELECT user_id,
                   COUNT(*) AS transactions_count,
                   SUM(CASE WHEN type = 'EXPENSE' THEN amount ELSE 0 END) AS total_expense
            FROM transactions
            GROUP BY user_id
        ) tx ON tx.user_id = u.id
        WHERE u.id = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        jsonResponse(false, 'Không tìm thấy người dùng');
    }
    jsonResponse(true, 'Success', $user);
} elseif ($action === 'admin_create_user') {
    ensurePermission(['SUPER_ADMIN'], $currentAdminRole);
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = 'USER'; // Default role

    if ($name === '' || $email === '' || $password === '') {
        jsonResponse(false, 'Vui lòng điền đầy đủ thông tin');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(false, 'Email không hợp lệ');
    }

    if (strlen($password) < 6) {
        jsonResponse(false, 'Mật khẩu phải có ít nhất 6 ký tự');
    }

    // Check email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        jsonResponse(false, 'Email đã tồn tại trong hệ thống');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password_hash, role, status) VALUES (?, ?, ?, ?, 'ACTIVE')");
    $stmt->execute([$name, $email, $hash, $role]);

    $newId = $pdo->lastInsertId();

    logAdminAction($pdo, $_SESSION['admin_id'], 'CREATE_USER', "Tạo người dùng #$newId ($email)", 'user', $newId);

    jsonResponse(true, 'Tạo người dùng thành công', [
        'id' => $newId,
        'email' => $email,
        'role' => $role
    ]);
} elseif ($action === 'admin_update_user_status') {
    ensurePermission(['SUPER_ADMIN','ADMIN'], $currentAdminRole);
    $id = (int) ($_POST['id'] ?? 0);
    $status = strtoupper(trim($_POST['status'] ?? ''));
    if ($id <= 0 || $status === '') {
        jsonResponse(false, 'Thiếu id hoặc status');
    }
    $allowed = ['ACTIVE', 'BANNED', 'INACTIVE'];
    if (!in_array($status, $allowed, true)) {
        jsonResponse(false, 'Trạng thái không hợp lệ');
    }

    $stmt = $pdo->prepare("UPDATE users SET status = :status WHERE id = :id");
    $stmt->execute([':status' => $status, ':id' => $id]);

    logAdminAction($pdo, $_SESSION['admin_id'], 'UPDATE_USER_STATUS', "Đổi trạng thái người dùng #$id sang $status", 'user', $id, ['status' => $status]);

    jsonResponse(true, 'Cập nhật trạng thái thành công');
} elseif ($action === 'admin_update_user_role') {
    ensurePermission(['SUPER_ADMIN'], $currentAdminRole);
    $id = (int) ($_POST['id'] ?? 0);
    $role = strtoupper(trim($_POST['role'] ?? ''));
    if ($id <= 0 || $role === '') {
        jsonResponse(false, 'Thiếu id hoặc role');
    }
    $allowed = ['SUPER_ADMIN','ADMIN','STAFF','USER'];
    if (!in_array($role, $allowed, true)) {
        jsonResponse(false, 'Role không hợp lệ');
    }

    $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
    $stmt->execute([':role' => $role, ':id' => $id]);
    logAdminAction($pdo, $_SESSION['admin_id'], 'UPDATE_USER_ROLE', "Đổi quyền user #$id sang $role", 'user', $id, ['role' => $role]);
    jsonResponse(true, 'Cập nhật quyền thành công');
} elseif ($action === 'admin_send_notification') {
    ensurePermission(['SUPER_ADMIN','ADMIN'], $currentAdminRole);

    $title = trim($_POST['title'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $type = strtolower(trim($_POST['type'] ?? 'info'));
    $linkUrl = trim($_POST['link_url'] ?? '');
    $scope = strtolower(trim($_POST['scope'] ?? 'all'));
    $targetUserId = (int) ($_POST['user_id'] ?? 0);

    if ($title === '' || $message === '') {
        jsonResponse(false, 'Vui lòng nhập tiêu đề và nội dung thông báo');
    }

    $allowedTypes = ['info','success','warning','error','reminder'];
    if (!in_array($type, $allowedTypes, true)) {
        $type = 'info';
    }

    $linkUrl = $linkUrl !== '' ? $linkUrl : null;

    $recipients = 0;

    try {
        $pdo->beginTransaction();

        if ($scope === 'single') {
            if ($targetUserId <= 0) {
                throw new RuntimeException('Thiếu người nhận thông báo');
            }

            $stmtUser = $pdo->prepare("SELECT id FROM users WHERE id = :id LIMIT 1");
            $stmtUser->execute([':id' => $targetUserId]);
            if (!$stmtUser->fetchColumn()) {
                throw new RuntimeException('Không tìm thấy người dùng');
            }

            createNotification($pdo, $targetUserId, $type, $title, $message, $linkUrl);
            $recipients = 1;

        } else {
            $userStmt = $pdo->prepare("SELECT id FROM users WHERE status = 'ACTIVE'");
            $userStmt->execute();

            $insertStmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, link_url) VALUES (:user_id, :type, :title, :message, :link)");

            while (($userId = $userStmt->fetchColumn()) !== false) {
                $insertStmt->execute([
                    ':user_id' => (int) $userId,
                    ':type' => $type,
                    ':title' => $title,
                    ':message' => $message,
                    ':link' => $linkUrl
                ]);
                $recipients++;
            }

            if ($recipients === 0) {
                throw new RuntimeException('Không có người dùng nào để gửi thông báo');
            }
        }

        $pdo->commit();

    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Admin Send Notification Error: ' . $e->getMessage());
        jsonResponse(false, $e->getMessage() ?: 'Không thể gửi thông báo');
    }

    logAdminAction(
        $pdo,
        $_SESSION['admin_id'],
        'ADMIN_BROADCAST_NOTIFICATION',
        $scope === 'single'
            ? "Gửi thông báo tới user #$targetUserId"
            : 'Gửi thông báo tới tất cả người dùng',
        $scope === 'single' ? 'user' : 'broadcast',
        $scope === 'single' ? $targetUserId : null,
        [
            'type' => $type,
            'title' => $title,
            'recipients' => $recipients
        ]
    );

    jsonResponse(true, 'Đã gửi thông báo thành công', [
        'recipients' => $recipients
    ]);
}

// --- Categories (Admin) ---
elseif ($action === 'admin_category_stats' || $action === 'category_stats') {
    $total = (int) $pdo->query("SELECT COUNT(*) FROM categories WHERE status != 'DELETED'")->fetchColumn();
    $income = (int) $pdo->query("SELECT COUNT(*) FROM categories WHERE type = 'INCOME' AND status != 'DELETED'")->fetchColumn();
    $expense = (int) $pdo->query("SELECT COUNT(*) FROM categories WHERE type = 'EXPENSE' AND status != 'DELETED'")->fetchColumn();
    $used = (int) $pdo->query("SELECT COUNT(DISTINCT category_id) FROM transactions")->fetchColumn();

    jsonResponse(true, 'Success', [
        'total' => $total,
        'income' => $income,
        'expense' => $expense,
        'used' => $used
    ]);
} elseif ($action === 'admin_get_categories' || $action === 'get_categories') {
    $search = trim($_POST['search'] ?? $_GET['search'] ?? '');
    $type = strtoupper(trim($_POST['type'] ?? $_GET['type'] ?? ''));
    $status = strtoupper(trim($_POST['status'] ?? $_GET['status'] ?? ''));

    $where = ["c.status != 'DELETED'"];
    $params = [];
    if ($search !== '') {
        $where[] = "(LOWER(c.name) LIKE :search)";
        $params[':search'] = '%' . strtolower($search) . '%';
    }
    if ($type !== '' && in_array($type, ['INCOME', 'EXPENSE'], true)) {
        $where[] = "c.type = :type";
        $params[':type'] = $type;
    }
    if ($status !== '' && in_array($status, ['ACTIVE', 'INACTIVE'], true)) {
        $where[] = "c.status = :status";
        $params[':status'] = $status;
    }
    $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

    $sql = "
        SELECT 
            c.id, c.name, c.type, c.status, c.color, c.icon, c.description, c.created_at,
            COALESCE(tx.usage_count, 0) AS usage_count
        FROM categories c
        LEFT JOIN (
            SELECT category_id, COUNT(*) AS usage_count
            FROM transactions
            GROUP BY category_id
        ) tx ON tx.category_id = c.id
        $whereSql
        ORDER BY c.created_at DESC, c.id DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse(true, 'Success', [
        'items' => array_map(function ($row) {
            $row['type'] = strtoupper($row['type']);
            $row['status'] = strtoupper($row['status']);
            return $row;
        }, $rows)
    ]);
} elseif ($action === 'admin_create_category' || $action === 'add_category') {
    $name = trim($_POST['name'] ?? '');
    $type = strtoupper(trim($_POST['type'] ?? ''));
    $color = trim($_POST['color'] ?? '#3B6FD8');
    $icon = trim($_POST['icon'] ?? 'fa-tag');
    $description = trim($_POST['description'] ?? '');

    if ($name === '' || $type === '') {
        jsonResponse(false, 'Tên và loại danh mục là bắt buộc');
    }
    if (!in_array($type, ['INCOME', 'EXPENSE'], true)) {
        jsonResponse(false, 'Loại không hợp lệ');
    }

    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE LOWER(name) = :name AND status != 'DELETED'");
    $stmtCheck->execute([':name' => strtolower($name)]);
    if ($stmtCheck->fetchColumn() > 0) {
        jsonResponse(false, 'Danh mục đã tồn tại');
    }

    $stmt = $pdo->prepare("
        INSERT INTO categories (user_id, name, type, color, icon, description, status)
        VALUES (:user_id, :name, :type, :color, :icon, :description, 'ACTIVE')
    ");
    $stmt->execute([
        ':user_id' => $_SESSION['admin_id'],
        ':name' => $name,
        ':type' => $type,
        ':color' => $color,
        ':icon' => $icon,
        ':description' => $description
    ]);

    jsonResponse(true, 'Thêm danh mục thành công', ['id' => (int) $pdo->lastInsertId()]);
} elseif ($action === 'admin_update_category') {
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $type = strtoupper(trim($_POST['type'] ?? ''));
    $color = trim($_POST['color'] ?? '#3B6FD8');
    $icon = trim($_POST['icon'] ?? 'fa-tag');
    $description = trim($_POST['description'] ?? '');
    $status = strtoupper(trim($_POST['status'] ?? 'ACTIVE'));

    if ($id <= 0 || $name === '' || $type === '') {
        jsonResponse(false, 'Thiếu thông tin danh mục');
    }
    if (!in_array($type, ['INCOME', 'EXPENSE'], true)) {
        jsonResponse(false, 'Loại không hợp lệ');
    }
    if (!in_array($status, ['ACTIVE', 'INACTIVE', 'DELETED'], true)) {
        jsonResponse(false, 'Trạng thái không hợp lệ');
    }

    $stmt = $pdo->prepare("
        UPDATE categories
        SET name = :name, type = :type, color = :color, icon = :icon, description = :description, status = :status
        WHERE id = :id
    ");
    $stmt->execute([
        ':id' => $id,
        ':name' => $name,
        ':type' => $type,
        ':color' => $color,
        ':icon' => $icon,
        ':description' => $description,
        ':status' => $status
    ]);

    jsonResponse(true, 'Cập nhật danh mục thành công');
} elseif ($action === 'admin_update_category_status' || $action === 'delete_category') {
    $id = (int) ($_POST['id'] ?? 0);
    $status = strtoupper(trim($_POST['status'] ?? 'DELETED'));
    if ($id <= 0) {
        jsonResponse(false, 'Thiếu id danh mục');
    }
    if (!in_array($status, ['ACTIVE', 'INACTIVE', 'DELETED'], true)) {
        jsonResponse(false, 'Trạng thái không hợp lệ');
    }

    $stmt = $pdo->prepare("UPDATE categories SET status = :status WHERE id = :id");
    $stmt->execute([':status' => $status, ':id' => $id]);
    jsonResponse(true, 'Cập nhật trạng thái danh mục thành công');
}

// --- Admin Transactions ---
elseif ($action === 'admin_get_transactions') {
    $page = max(1, (int) ($_POST['page'] ?? $_GET['page'] ?? 1));
    $limit = max(1, min(100, (int) ($_POST['limit'] ?? $_GET['limit'] ?? 20)));
    $offset = ($page - 1) * $limit;

    $search = trim($_POST['search'] ?? $_GET['search'] ?? '');
    $userId = (int) ($_POST['user_id'] ?? $_GET['user_id'] ?? 0);
    $categoryId = (int) ($_POST['category_id'] ?? $_GET['category_id'] ?? 0);
    $type = strtoupper(trim($_POST['type'] ?? $_GET['type'] ?? ''));
    $status = strtoupper(trim($_POST['status'] ?? $_GET['status'] ?? ''));
    $dateFrom = trim($_POST['date_from'] ?? $_GET['date_from'] ?? '');
    $dateTo = trim($_POST['date_to'] ?? $_GET['date_to'] ?? '');

    $where = [];
    $params = [];

    if ($search !== '') {
        $where[] = "(LOWER(u.email) LIKE :search OR LOWER(u.fullname) LIKE :search OR LOWER(t.note) LIKE :search)";
        $params[':search'] = '%' . strtolower($search) . '%';
    }
    if ($userId > 0) {
        $where[] = "t.user_id = :user_id";
        $params[':user_id'] = $userId;
    }
    if ($categoryId > 0) {
        $where[] = "t.category_id = :category_id";
        $params[':category_id'] = $categoryId;
    }
    if ($type !== '' && in_array($type, ['INCOME', 'EXPENSE'], true)) {
        $where[] = "t.type = :type";
        $params[':type'] = $type;
    }
    if ($status !== '' && in_array($status, ['COMPLETED', 'PENDING', 'CANCELED', 'FLAGGED'], true)) {
        $where[] = "t.status = :status";
        $params[':status'] = $status;
    }
    if ($dateFrom !== '') {
        $where[] = "t.transaction_date >= :date_from";
        $params[':date_from'] = $dateFrom;
    }
    if ($dateTo !== '') {
        $where[] = "t.transaction_date <= :date_to";
        $params[':date_to'] = $dateTo;
    }

    $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

    $stmtCount = $pdo->prepare("
        SELECT COUNT(*) 
        FROM transactions t
        INNER JOIN users u ON u.id = t.user_id
        LEFT JOIN categories c ON c.id = t.category_id
        $whereSql
    ");
    $stmtCount->execute($params);
    $total = (int) $stmtCount->fetchColumn();

    $sql = "
        SELECT 
            t.id,
            t.user_id,
            u.email AS user_email,
            u.fullname AS user_name,
            t.category_id,
            c.name AS category_name,
            t.amount,
            t.type,
            t.status,
            t.note,
            t.transaction_date,
            t.created_at
        FROM transactions t
        INNER JOIN users u ON u.id = t.user_id
        LEFT JOIN categories c ON c.id = t.category_id
        $whereSql
        ORDER BY t.transaction_date DESC, t.id DESC
        LIMIT :limit OFFSET :offset
    ";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse(true, 'Success', [
        'items' => array_map(function ($row) {
            $row['type'] = strtoupper($row['type']);
            $row['status'] = strtoupper($row['status']);
            return $row;
        }, $items),
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total
        ]
    ]);
} elseif ($action === 'admin_get_transaction_detail') {
    $id = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
    if ($id <= 0) {
        jsonResponse(false, 'Thiếu id giao dịch');
    }
    $stmt = $pdo->prepare("
        SELECT 
            t.id,
            t.user_id,
            u.email AS user_email,
            u.fullname AS user_name,
            t.category_id,
            c.name AS category_name,
            t.amount,
            t.type,
            t.status,
            t.note,
            t.transaction_date,
            t.created_at,
            t.admin_note
        FROM transactions t
        INNER JOIN users u ON u.id = t.user_id
        LEFT JOIN categories c ON c.id = t.category_id
        WHERE t.id = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $id]);
    $txn = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$txn) {
        jsonResponse(false, 'Không tìm thấy giao dịch');
    }
    $txn['type'] = strtoupper($txn['type']);
    $txn['status'] = strtoupper($txn['status']);
    jsonResponse(true, 'Success', $txn);
} elseif ($action === 'admin_update_transaction_status') {
    $id = (int) ($_POST['id'] ?? 0);
    $status = strtoupper(trim($_POST['status'] ?? ''));
    $adminNote = trim($_POST['admin_note'] ?? '');
    if ($id <= 0 || $status === '') {
        jsonResponse(false, 'Thiếu id hoặc trạng thái');
    }
    $allowed = ['COMPLETED', 'PENDING', 'CANCELED', 'FLAGGED'];
    if (!in_array($status, $allowed, true)) {
        jsonResponse(false, 'Trạng thái không hợp lệ');
    }
    $stmt = $pdo->prepare("
        UPDATE transactions
        SET status = :status, admin_note = :admin_note
        WHERE id = :id
    ");
    $stmt->execute([
        ':status' => $status,
        ':admin_note' => $adminNote,
        ':id' => $id
    ]);
    jsonResponse(true, 'Cập nhật trạng thái giao dịch thành công');
}

// --- Admin Reports / Analytics ---
elseif ($action === 'admin_get_report_summary') {
    $dateFrom = trim($_POST['date_from'] ?? $_GET['date_from'] ?? '');
    $dateTo = trim($_POST['date_to'] ?? $_GET['date_to'] ?? '');
    $userId = (int) ($_POST['user_id'] ?? $_GET['user_id'] ?? 0);

    $where = [];
    $params = [];
    if ($dateFrom !== '') {
        $where[] = "t.transaction_date >= :date_from";
        $params[':date_from'] = $dateFrom;
    }
    if ($dateTo !== '') {
        $where[] = "t.transaction_date <= :date_to";
        $params[':date_to'] = $dateTo;
    }
    if ($userId > 0) {
        $where[] = "t.user_id = :user_id";
        $params[':user_id'] = $userId;
    }
    $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

    $stmt = $pdo->prepare("
        SELECT 
            SUM(CASE WHEN t.type = 'INCOME' THEN t.amount ELSE 0 END) AS total_income,
            SUM(CASE WHEN t.type = 'EXPENSE' THEN t.amount ELSE 0 END) AS total_expense
        FROM transactions t
        $whereSql
    ");
    $stmt->execute($params);
    $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total_income' => 0, 'total_expense' => 0];
    $income = (float) $row['total_income'];
    $expense = (float) $row['total_expense'];
    jsonResponse(true, 'Success', [
        'total_income' => $income,
        'total_expense' => $expense,
        'net' => $income - $expense
    ]);
} elseif ($action === 'admin_get_report_by_month') {
    $year = (int) ($_POST['year'] ?? $_GET['year'] ?? date('Y'));
    $type = strtoupper(trim($_POST['type'] ?? $_GET['type'] ?? ''));

    $params = [':year' => $year];
    $typeFilter = '';
    if ($type !== '' && in_array($type, ['INCOME', 'EXPENSE'], true)) {
        $typeFilter = " AND t.type = :type";
        $params[':type'] = $type;
    }

    $stmt = $pdo->prepare("
        SELECT 
            MONTH(t.transaction_date) AS m,
            SUM(CASE WHEN t.type = 'INCOME' THEN t.amount ELSE 0 END) AS income_total,
            SUM(CASE WHEN t.type = 'EXPENSE' THEN t.amount ELSE 0 END) AS expense_total
        FROM transactions t
        WHERE YEAR(t.transaction_date) = :year $typeFilter
        GROUP BY MONTH(t.transaction_date)
        ORDER BY MONTH(t.transaction_date)
    ");
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $incomeValues = array_fill(0, 12, 0);
    $expenseValues = array_fill(0, 12, 0);
    for ($i = 1; $i <= 12; $i++) {
        $labels[] = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
    }
    foreach ($rows as $r) {
        $idx = (int) $r['m'] - 1;
        if ($idx >= 0 && $idx < 12) {
            $incomeValues[$idx] = (float) $r['income_total'];
            $expenseValues[$idx] = (float) $r['expense_total'];
        }
    }

    jsonResponse(true, 'Success', [
        'labels' => $labels,
        'income_values' => $incomeValues,
        'expense_values' => $expenseValues
    ]);
} elseif ($action === 'admin_get_report_by_category') {
    $dateFrom = trim($_POST['date_from'] ?? $_GET['date_from'] ?? '');
    $dateTo = trim($_POST['date_to'] ?? $_GET['date_to'] ?? '');
    $type = strtoupper(trim($_POST['type'] ?? $_GET['type'] ?? ''));

    $where = [];
    $params = [];
    if ($dateFrom !== '') {
        $where[] = "t.transaction_date >= :date_from";
        $params[':date_from'] = $dateFrom;
    }
    if ($dateTo !== '') {
        $where[] = "t.transaction_date <= :date_to";
        $params[':date_to'] = $dateTo;
    }
    if ($type !== '' && in_array($type, ['INCOME', 'EXPENSE'], true)) {
        $where[] = "t.type = :type";
        $params[':type'] = $type;
    }
    $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

    $stmt = $pdo->prepare("
        SELECT c.name AS category_name, SUM(t.amount) AS total_amount
        FROM transactions t
        LEFT JOIN categories c ON c.id = t.category_id
        $whereSql
        GROUP BY t.category_id, c.name
        ORDER BY total_amount DESC
    ");
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    jsonResponse(true, 'Success', [
        'labels' => array_column($rows, 'category_name'),
        'values' => array_map('floatval', array_column($rows, 'total_amount'))
    ]);
}

// --- System Settings ---
elseif ($action === 'admin_get_settings') {
    $stmt = $pdo->prepare("
        SELECT setting_key, setting_value, setting_type
        FROM system_settings
        ORDER BY setting_key
    ");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $settings = [];
    foreach ($rows as $row) {
        $key = $row['setting_key'];
        $value = $row['setting_value'];
        $type = $row['setting_type'];

        // Convert value based on type
        if ($type === 'number') {
            $settings[$key] = is_numeric($value) ? (strpos($value, '.') !== false ? (float) $value : (int) $value) : 0;
        } elseif ($type === 'boolean') {
            $settings[$key] = (int) $value;
        } else {
            $settings[$key] = $value;
        }
    }

    jsonResponse(true, 'Success', $settings);
} elseif ($action === 'admin_update_settings') {
    $settings = $_POST['settings'] ?? [];

    if (empty($settings) || !is_array($settings)) {
        jsonResponse(false, 'Thiếu thông tin cài đặt');
    }

    // Validation rules
    $validations = [
        'timezone' => ['Asia/Ho_Chi_Minh', 'Asia/Bangkok', 'Asia/Singapore'],
        'currency_format' => ['vnd', 'usd', 'eur'],
        'language' => ['vi', 'en']
    ];

    $pdo->beginTransaction();
    try {
        foreach ($settings as $key => $value) {
            // Validate specific fields
            if (isset($validations[$key])) {
                if (!in_array(strtolower($value), $validations[$key], true)) {
                    throw new Exception("Giá trị {$key} không hợp lệ");
                }
            }

            // Validate numeric ranges
            if ($key === 'warning_threshold') {
                $val = (int) $value;
                if ($val < 0 || $val > 100) {
                    throw new Exception("Ngưỡng cảnh báo phải từ 0-100");
                }
            }
            if ($key === 'exceeded_threshold') {
                $val = (int) $value;
                if ($val < 0 || $val > 200) {
                    throw new Exception("Ngưỡng vượt ngân sách phải từ 0-200");
                }
            }
            if ($key === 'session_timeout') {
                $val = (int) $value;
                if ($val < 15 || $val > 1440) {
                    throw new Exception("Thời gian phiên phải từ 15-1440 phút");
                }
            }
            if ($key === 'min_password_length') {
                $val = (int) $value;
                if ($val < 6 || $val > 20) {
                    throw new Exception("Độ dài mật khẩu phải từ 6-20");
                }
            }
            if ($key === 'bill_reminder_days') {
                $val = (int) $value;
                if ($val < 1 || $val > 30) {
                    throw new Exception("Số ngày nhắc trước phải từ 1-30");
                }
            }
            if ($key === 'default_budget') {
                $val = (float) $value;
                if ($val < 0) {
                    throw new Exception("Ngân sách mặc định phải >= 0");
                }
            }

            // Update or insert setting
            $stmt = $pdo->prepare("
                INSERT INTO system_settings (setting_key, setting_value)
                VALUES (:key, :value)
                ON DUPLICATE KEY UPDATE setting_value = :value
            ");
            $stmt->execute([
                ':key' => $key,
                ':value' => $value
            ]);
        }

        $pdo->commit();

        logAdminAction($pdo, $_SESSION['admin_id'], 'UPDATE_SETTINGS', 'Cập nhật cấu hình hệ thống', 'system_settings', null);

        jsonResponse(true, 'Đã lưu cài đặt thành công');

    } catch (Exception $e) {
        $pdo->rollBack();
        jsonResponse(false, $e->getMessage());
    }
}

// --- Support Tickets (Admin) ---
elseif ($action === 'get_support_tickets') {
    $page = max(1, (int) ($_GET['page'] ?? $_POST['page'] ?? 1));
    $limit = max(1, min(100, (int) ($_GET['limit'] ?? $_POST['limit'] ?? 20)));
    $offset = ($page - 1) * $limit;

    $search = trim($_GET['search'] ?? $_POST['search'] ?? '');
    $status = trim($_GET['status'] ?? $_POST['status'] ?? '');
    $category = trim($_GET['category'] ?? $_POST['category'] ?? '');
    $date = trim($_GET['date'] ?? $_POST['date'] ?? '');

    $where = [];
    $params = [];

    if ($search !== '') {
        $where[] = "(t.subject LIKE :search OR u.fullname LIKE :search OR u.email LIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }
    if ($status !== '' && in_array($status, ['open', 'answered', 'closed'], true)) {
        $where[] = "t.status = :status";
        $params[':status'] = $status;
    }
    if ($category !== '' && in_array($category, ['bug', 'feature', 'question', 'other'], true)) {
        $where[] = "t.category = :category";
        $params[':category'] = $category;
    }
    if ($date !== '') {
        $where[] = "DATE(t.created_at) = :date";
        $params[':date'] = $date;
    }

    $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

    // Count total
    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM support_tickets t INNER JOIN users u ON u.id = t.user_id $whereSql");
    $stmtCount->execute($params);
    $total = (int) $stmtCount->fetchColumn();

    // Get tickets
    $sql = "
        SELECT 
            t.id,
            t.user_id,
            u.fullname AS user_name,
            u.email AS user_email,
            t.subject,
            t.category,
            t.status,
            t.priority,
            t.is_read,
            t.created_at
        FROM support_tickets t
        INNER JOIN users u ON u.id = t.user_id
        $whereSql
        ORDER BY t.created_at DESC
        LIMIT :limit OFFSET :offset
    ";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $k => $v) {
        $stmt->bindValue($k, $v);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get summary
    $summary = [
        'total' => $total,
        'open' => (int) $pdo->query("SELECT COUNT(*) FROM support_tickets WHERE status = 'open'")->fetchColumn(),
        'answered' => (int) $pdo->query("SELECT COUNT(*) FROM support_tickets WHERE status = 'answered'")->fetchColumn(),
        'closed' => (int) $pdo->query("SELECT COUNT(*) FROM support_tickets WHERE status = 'closed'")->fetchColumn()
    ];

    jsonResponse(true, 'Success', [
        'items' => $tickets,
        'summary' => $summary,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total
        ]
    ]);
} elseif ($action === 'get_ticket_detail') {
    $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
    if ($id <= 0) {
        jsonResponse(false, 'Thiếu ID ticket');
    }

    // Get ticket info
    $stmt = $pdo->prepare("
        SELECT 
            t.id,
            t.user_id,
            u.fullname AS user_name,
            u.email AS user_email,
            t.subject,
            t.category,
            t.status,
            t.priority,
            t.created_at,
            t.updated_at,
            t.closed_at
        FROM support_tickets t
        INNER JOIN users u ON u.id = t.user_id
        WHERE t.id = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $id]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        jsonResponse(false, 'Không tìm thấy ticket');
    }

    // Get messages
    $stmtMsg = $pdo->prepare("
        SELECT 
            m.id,
            m.sender_id,
            m.sender_type,
            m.message,
            m.created_at,
            u.fullname AS sender_name
        FROM support_messages m
        INNER JOIN users u ON u.id = m.sender_id
        WHERE m.ticket_id = :ticket_id
        ORDER BY m.created_at ASC
    ");
    $stmtMsg->execute([':ticket_id' => $id]);
    $messages = $stmtMsg->fetchAll(PDO::FETCH_ASSOC);

    $ticket['messages'] = $messages;

    // Mark as read
    $pdo->prepare("UPDATE support_tickets SET is_read = 1 WHERE id = :id")->execute([':id' => $id]);

    jsonResponse(true, 'Success', $ticket);
} elseif ($action === 'reply_ticket') {
    $ticketId = (int) ($_POST['ticket_id'] ?? 0);
    $message = trim($_POST['message'] ?? '');

    if ($ticketId <= 0) {
        jsonResponse(false, 'Thiếu ID ticket');
    }
    if ($message === '') {
        jsonResponse(false, 'Nội dung trả lời không được rỗng');
    }

    // Check ticket exists
    $stmt = $pdo->prepare("SELECT id FROM support_tickets WHERE id = :id");
    $stmt->execute([':id' => $ticketId]);
    if (!$stmt->fetch()) {
        jsonResponse(false, 'Ticket không tồn tại');
    }

    $pdo->beginTransaction();
    try {
        // Insert message
        $stmtMsg = $pdo->prepare("
            INSERT INTO support_messages (ticket_id, sender_id, sender_type, message)
            VALUES (:ticket_id, :sender_id, 'admin', :message)
        ");
        $stmtMsg->execute([
            ':ticket_id' => $ticketId,
            ':sender_id' => $_SESSION['admin_id'],
            ':message' => $message
        ]);

        // Update ticket status to answered
        $stmtUpdate = $pdo->prepare("
            UPDATE support_tickets 
            SET status = 'answered', updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        $stmtUpdate->execute([':id' => $ticketId]);

        $pdo->commit();

        logAdminAction($pdo, $_SESSION['admin_id'], 'REPLY_TICKET', "Phản hồi ticket #$ticketId", 'support_ticket', $ticketId);

        jsonResponse(true, 'Đã gửi trả lời');

    } catch (Exception $e) {
        $pdo->rollBack();
        jsonResponse(false, 'Lỗi khi gửi trả lời: ' . $e->getMessage());
    }
} elseif ($action === 'close_ticket') {
    $ticketId = (int) ($_POST['ticket_id'] ?? 0);

    if ($ticketId <= 0) {
        jsonResponse(false, 'Thiếu ID ticket');
    }

    // Check ticket exists
    $stmt = $pdo->prepare("SELECT id FROM support_tickets WHERE id = :id");
    $stmt->execute([':id' => $ticketId]);
    if (!$stmt->fetch()) {
        jsonResponse(false, 'Ticket không tồn tại');
    }

    // Update ticket status to closed
    $stmtUpdate = $pdo->prepare("
        UPDATE support_tickets 
        SET status = 'closed', closed_at = CURRENT_TIMESTAMP, updated_at = CURRENT_TIMESTAMP
        WHERE id = :id
    ");
    $stmtUpdate->execute([':id' => $ticketId]);

    jsonResponse(true, 'Đã đóng ticket');
}


// --- System Health (Admin) ---
elseif ($action === 'admin_get_system_health') {
    $data = [
        'overall_status' => 'healthy',
        'api' => ['status' => 'ok', 'response_time' => 0],
        'database' => ['status' => 'ok', 'connections' => 0],
        'memory' => ['status' => 'ok', 'usage' => 0],
        'disk' => ['status' => 'ok', 'free_gb' => 0],
        'system_info' => [],
        'recent_activity' => []
    ];

    // 1. Check Database & API Latency
    $start = microtime(true);
    try {
        // Check connection and get thread count
        $stmt = $pdo->query("SHOW STATUS LIKE 'Threads_connected'");
        $threads = $stmt->fetch(PDO::FETCH_ASSOC);
        $data['database']['connections'] = (int) $threads['Value'];

        // Simple query to ensure DB is responsive
        $pdo->query("SELECT 1");

        $latency = round((microtime(true) - $start) * 1000);
        $data['api']['response_time'] = $latency;

        if ($latency > 1000) {
            $data['api']['status'] = 'warning';
            $data['overall_status'] = 'warning';
        }
    } catch (Exception $e) {
        $data['database']['status'] = 'error';
        $data['overall_status'] = 'error';
        $data['recent_activity'][] = [
            'type' => 'error',
            'title' => 'Database Error',
            'time' => date('H:i:s')
        ];
    }

    // 2. Check Disk Space
    $totalSpace = disk_total_space(__DIR__);
    $freeSpace = disk_free_space(__DIR__);
    $freeGb = round($freeSpace / (1024 * 1024 * 1024), 2);
    $data['disk']['free_gb'] = $freeGb;

    if ($freeGb < 1) {
        $data['disk']['status'] = 'error';
        $data['overall_status'] = 'error';
        $data['recent_activity'][] = [
            'type' => 'error',
            'title' => 'Low Disk Space (< 1GB)',
            'time' => date('H:i:s')
        ];
    } elseif ($freeGb < 5) {
        $data['disk']['status'] = 'warning';
        if ($data['overall_status'] !== 'error') {
            $data['overall_status'] = 'warning';
        }
    }

    // 3. Check Memory
    $memoryLimit = ini_get('memory_limit');
    if (preg_match('/^(\d+)(.)$/', $memoryLimit, $matches)) {
        if ($matches[2] == 'M') {
            $memoryLimitBytes = $matches[1] * 1024 * 1024;
        } elseif ($matches[2] == 'G') {
            $memoryLimitBytes = $matches[1] * 1024 * 1024 * 1024;
        } else {
            $memoryLimitBytes = $matches[1];
        }
    } else {
        $memoryLimitBytes = 128 * 1024 * 1024; // Default 128M
    }

    $memoryUsage = memory_get_usage(true);
    $memoryPercent = round(($memoryUsage / $memoryLimitBytes) * 100, 1);
    $data['memory']['usage'] = $memoryPercent;

    if ($memoryPercent > 90) {
        $data['memory']['status'] = 'error';
        $data['overall_status'] = 'error';
    } elseif ($memoryPercent > 75) {
        $data['memory']['status'] = 'warning';
        if ($data['overall_status'] !== 'error') {
            $data['overall_status'] = 'warning';
        }
    }

    // 4. System Info
    $data['system_info'] = [
        'php_version' => phpversion(),
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'os' => PHP_OS,
        'server_time' => date('Y-m-d H:i:s'),
        'uptime' => 'N/A', // PHP doesn't have easy access to system uptime on Windows
        'max_upload' => ini_get('upload_max_filesize')
    ];

    // 5. Recent Activity (Mock some success checks if no errors)
    if (empty($data['recent_activity'])) {
        $data['recent_activity'][] = [
            'type' => 'success',
            'title' => 'System Health Check Passed',
            'time' => date('H:i:s')
        ];
    }

    jsonResponse(true, 'Success', $data);
}

// --- Logs (Admin) ---
elseif ($action === 'admin_get_logs') {
    $stmt = $pdo->query("
        SELECT l.*, u.fullname as user_name 
        FROM activity_logs l 
        LEFT JOIN users u ON u.id = l.user_id 
        ORDER BY l.created_at DESC LIMIT 200
    ");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format logs for frontend
    $logs = array_map(function ($row) {
        return [
            'time' => $row['created_at'],
            'user' => $row['user_name'] ?? 'Unknown',
            'action' => $row['action'],
            'ip' => $row['ip_address'] ?? '',
            'note' => $row['description']
        ];
    }, $rows);

    jsonResponse(true, 'Success', $logs);
}

// --- Fallback ---
else {
    jsonResponse(false, 'Invalid action');
}

function logActivity($pdo, $userId, $action, $description)
{
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, description) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $action, $description]);
    } catch (Exception $e) {
        // Silent fail for logs
    }
}
