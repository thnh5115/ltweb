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
    $_SESSION['admin_id']    = $_SESSION['user_id'];
    $_SESSION['admin_name']  = $_SESSION['user_name'] ?? 'Admin';
    $_SESSION['admin_email'] = $_SESSION['user_email'] ?? null;
}

if (!isset($_SESSION['admin_id'])) {
    jsonResponse(false, 'Unauthorized');
}

// --- Dashboard Summary ---
if ($action === 'get_dashboard_summary') {
    $totalUsers = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $totalCategories = (int)$pdo->query("SELECT COUNT(*) FROM categories WHERE status != 'DELETED'")->fetchColumn();
    $totalTransactions = (int)$pdo->query("SELECT COUNT(*) FROM transactions")->fetchColumn();

    $stmtExpense = $pdo->prepare("
        SELECT COALESCE(SUM(amount), 0)
        FROM transactions
        WHERE type = 'EXPENSE'
          AND YEAR(transaction_date) = YEAR(CURDATE())
          AND MONTH(transaction_date) = MONTH(CURDATE())
    ");
    $stmtExpense->execute();
    $totalExpenseThisMonth = (float)$stmtExpense->fetchColumn();

    $stmtPending = $pdo->prepare("SELECT COUNT(*) FROM bills WHERE status = 'PENDING'");
    $stmtPending->execute();
    $pendingTransactions = (int)$stmtPending->fetchColumn();

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
        $idx = (int)$row['m'] - 1;
        if ($idx >= 0 && $idx < 12) {
            $values[$idx] = (float)$row['total'];
        }
    }

    jsonResponse(true, 'Success', [
        'labels' => $labels,
        'values' => $values
    ]);
}

// --- Recent Transactions ---
elseif ($action === 'get_recent_transactions') {
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
            'id' => (int)$row['id'],
            'user' => $row['user_name'] ?? 'N/A',
            'type' => strtolower($row['type'] ?? ''),
            'category' => $row['category_name'] ?? '-',
            'amount' => (float)$row['amount'],
            'date' => $row['transaction_date'],
            'status' => 'completed',
            'note' => $row['note'] ?? ''
        ];
    }, $rows);

    jsonResponse(true, 'Success', $data);
}

// --- Recent Users ---
elseif ($action === 'get_recent_users') {
    $stmt = $pdo->prepare("
        SELECT id, fullname AS name, email, created_at
        FROM users
        ORDER BY created_at DESC, id DESC
        LIMIT 10
    ");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    jsonResponse(true, 'Success', $users);
}

// --- Admin User Management ---
elseif ($action === 'admin_get_users') {
    $page  = max(1, (int)($_POST['page'] ?? $_GET['page'] ?? 1));
    $limit = max(1, min(100, (int)($_POST['limit'] ?? $_GET['limit'] ?? 10)));
    $offset = ($page - 1) * $limit;

    $search = trim($_POST['search'] ?? $_GET['search'] ?? '');
    $role   = trim($_POST['role'] ?? $_GET['role'] ?? '');
    $status = trim($_POST['status'] ?? $_GET['status'] ?? '');

    $where = [];
    $params = [];

    if ($search !== '') {
        $where[] = "(LOWER(u.fullname) LIKE :search OR LOWER(u.email) LIKE :search)";
        $params[':search'] = '%' . strtolower($search) . '%';
    }
    if ($role !== '') {
        $where[] = "u.role = :role";
        $params[':role'] = strtoupper($role);
    }
    if ($status !== '') {
        $where[] = "u.status = :status";
        $params[':status'] = strtoupper($status);
    }

    $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

    $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM users u $whereSql");
    $stmtCount->execute($params);
    $total = (int)$stmtCount->fetchColumn();

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
        'total'  => $total,
        'active' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'ACTIVE'")->fetchColumn(),
        'banned' => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'BANNED'")->fetchColumn(),
        'new'    => (int)$pdo->query("SELECT COUNT(*) FROM users WHERE YEAR(created_at)=YEAR(CURDATE()) AND MONTH(created_at)=MONTH(CURDATE())")->fetchColumn(),
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
}

elseif ($action === 'admin_get_user_detail') {
    $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
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
}

elseif ($action === 'admin_update_user_status') {
    $id = (int)($_POST['id'] ?? 0);
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
    jsonResponse(true, 'Cập nhật trạng thái thành công');
}

elseif ($action === 'admin_update_user_role') {
    $id = (int)($_POST['id'] ?? 0);
    $role = strtoupper(trim($_POST['role'] ?? ''));
    if ($id <= 0 || $role === '') {
        jsonResponse(false, 'Thiếu id hoặc role');
    }
    $allowed = ['ADMIN', 'USER'];
    if (!in_array($role, $allowed, true)) {
        jsonResponse(false, 'Role không hợp lệ');
    }

    $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
    $stmt->execute([':role' => $role, ':id' => $id]);
    jsonResponse(true, 'Cập nhật quyền thành công');
}

// --- Categories (Admin) ---
elseif ($action === 'admin_category_stats' || $action === 'category_stats') {
    $total = (int)$pdo->query("SELECT COUNT(*) FROM categories WHERE status != 'DELETED'")->fetchColumn();
    $income = (int)$pdo->query("SELECT COUNT(*) FROM categories WHERE type = 'INCOME' AND status != 'DELETED'")->fetchColumn();
    $expense = (int)$pdo->query("SELECT COUNT(*) FROM categories WHERE type = 'EXPENSE' AND status != 'DELETED'")->fetchColumn();
    $used = (int)$pdo->query("SELECT COUNT(DISTINCT category_id) FROM transactions")->fetchColumn();

    jsonResponse(true, 'Success', [
        'total' => $total,
        'income' => $income,
        'expense' => $expense,
        'used' => $used
    ]);
}

elseif ($action === 'admin_get_categories' || $action === 'get_categories') {
    $search = trim($_POST['search'] ?? $_GET['search'] ?? '');
    $type   = strtoupper(trim($_POST['type'] ?? $_GET['type'] ?? ''));
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
}

elseif ($action === 'admin_create_category' || $action === 'add_category') {
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

    jsonResponse(true, 'Thêm danh mục thành công', ['id' => (int)$pdo->lastInsertId()]);
}

elseif ($action === 'admin_update_category') {
    $id = (int)($_POST['id'] ?? 0);
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
}

elseif ($action === 'admin_update_category_status' || $action === 'delete_category') {
    $id = (int)($_POST['id'] ?? 0);
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
    $page  = max(1, (int)($_POST['page'] ?? $_GET['page'] ?? 1));
    $limit = max(1, min(100, (int)($_POST['limit'] ?? $_GET['limit'] ?? 20)));
    $offset = ($page - 1) * $limit;

    $search      = trim($_POST['search'] ?? $_GET['search'] ?? '');
    $userId      = (int)($_POST['user_id'] ?? $_GET['user_id'] ?? 0);
    $categoryId  = (int)($_POST['category_id'] ?? $_GET['category_id'] ?? 0);
    $type        = strtoupper(trim($_POST['type'] ?? $_GET['type'] ?? ''));
    $status      = strtoupper(trim($_POST['status'] ?? $_GET['status'] ?? ''));
    $dateFrom    = trim($_POST['date_from'] ?? $_GET['date_from'] ?? '');
    $dateTo      = trim($_POST['date_to'] ?? $_GET['date_to'] ?? '');

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
    $total = (int)$stmtCount->fetchColumn();

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
}

elseif ($action === 'admin_get_transaction_detail') {
    $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
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
}

elseif ($action === 'admin_update_transaction_status') {
    $id = (int)($_POST['id'] ?? 0);
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
    $dateTo   = trim($_POST['date_to'] ?? $_GET['date_to'] ?? '');
    $userId   = (int)($_POST['user_id'] ?? $_GET['user_id'] ?? 0);

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
    $income = (float)$row['total_income'];
    $expense = (float)$row['total_expense'];
    jsonResponse(true, 'Success', [
        'total_income' => $income,
        'total_expense' => $expense,
        'net' => $income - $expense
    ]);
}

elseif ($action === 'admin_get_report_by_month') {
    $year = (int)($_POST['year'] ?? $_GET['year'] ?? date('Y'));
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
        $labels[] = str_pad((string)$i, 2, '0', STR_PAD_LEFT);
    }
    foreach ($rows as $r) {
        $idx = (int)$r['m'] - 1;
        if ($idx >= 0 && $idx < 12) {
            $incomeValues[$idx] = (float)$r['income_total'];
            $expenseValues[$idx] = (float)$r['expense_total'];
        }
    }

    jsonResponse(true, 'Success', [
        'labels' => $labels,
        'income_values' => $incomeValues,
        'expense_values' => $expenseValues
    ]);
}

elseif ($action === 'admin_get_report_by_category') {
    $dateFrom = trim($_POST['date_from'] ?? $_GET['date_from'] ?? '');
    $dateTo   = trim($_POST['date_to'] ?? $_GET['date_to'] ?? '');
    $type     = strtoupper(trim($_POST['type'] ?? $_GET['type'] ?? ''));

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

// --- Fallback ---
else {
    jsonResponse(false, 'Invalid action');
}
