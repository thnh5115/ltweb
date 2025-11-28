<?php
/**
 * Data API for User Module
 * Handles categories, transactions, dashboard stats, statistics, charts, and budgets
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../db/config.php';
require_once __DIR__ . '/../functions.php';

// Ensure user is logged in
if (empty($_SESSION['user_id'])) {
    jsonResponse(false, 'Bạn chưa đăng nhập', [
        'redirect' => '/public/user/login.php'
    ]);
}

$userId = $_SESSION['user_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ============================================
// CATEGORIES MODULE
// ============================================

if ($action === 'get_categories') {
    handleGetCategories($pdo, $userId);

} elseif ($action === 'get_category') {
    handleGetCategory($pdo, $userId);

} elseif ($action === 'add_category') {
    handleAddCategory($pdo, $userId);

} elseif ($action === 'edit_category') {
    handleEditCategory($pdo, $userId);

} elseif ($action === 'delete_category') {
    handleDeleteCategory($pdo, $userId);
}

// ============================================
// TRANSACTIONS MODULE
// ============================================
elseif ($action === 'get_transactions') {
    handleGetTransactions($pdo, $userId);

} elseif ($action === 'get_transaction') {
    handleGetTransaction($pdo, $userId);

} elseif ($action === 'add_transaction') {
    handleAddTransaction($pdo, $userId);

} elseif ($action === 'edit_transaction') {
    handleEditTransaction($pdo, $userId);

} elseif ($action === 'delete_transaction') {
    handleDeleteTransaction($pdo, $userId);
}

// ============================================
// STATISTICS MODULE
// ============================================
elseif ($action === 'get_stats_overview') {
    handleGetStatsOverview($pdo, $userId);

} elseif ($action === 'get_stats_by_category') {
    handleGetStatsByCategory($pdo, $userId);

} elseif ($action === 'get_stats_timeseries') {
    handleGetStatsTimeseries($pdo, $userId);

} elseif ($action === 'top_categories') {
    handleTopCategories($pdo, $userId);
}

// ============================================
// BUDGETS MODULE
// ============================================
elseif ($action === 'get_budgets') {
    handleGetBudgets($pdo, $userId);

} elseif ($action === 'get_budget') {
    handleGetBudget($pdo, $userId);

} elseif ($action === 'save_budget') {
    handleSaveBudget($pdo, $userId);

} elseif ($action === 'delete_budget') {
    handleDeleteBudget($pdo, $userId);
}

// ============================================
// GOALS MODULE
// ============================================
elseif ($action === 'get_goals') {
    handleGetGoals($pdo, $userId);

} elseif ($action === 'get_goal') {
    handleGetGoal($pdo, $userId);

} elseif ($action === 'save_goal') {
    handleSaveGoal($pdo, $userId);

} elseif ($action === 'add_savings') {
    handleAddSavings($pdo, $userId);

} elseif ($action === 'delete_goal') {
    handleDeleteGoal($pdo, $userId);
}

// ============================================
// RECURRING MODULE
// ============================================
elseif ($action === 'get_recurring_transactions') {
    handleGetRecurringTransactions($pdo, $userId);

} elseif ($action === 'get_recurring') {
    handleGetRecurring($pdo, $userId);

} elseif ($action === 'save_recurring') {
    handleSaveRecurring($pdo, $userId);

} elseif ($action === 'toggle_recurring_status') {
    handleToggleRecurringStatus($pdo, $userId);

} elseif ($action === 'delete_recurring') {
    handleDeleteRecurring($pdo, $userId);

} elseif ($action === 'run_recurring_now') {
    handleRunRecurringNow($pdo, $userId);
}

// ============================================
// PROFILE MODULE
// ============================================
elseif ($action === 'profile_get') {
    handleProfileGet($pdo, $userId);

} elseif ($action === 'profile_update') {
    handleProfileUpdate($pdo, $userId);

} elseif ($action === 'profile_change_password') {
    handleProfileChangePassword($pdo, $userId);

} elseif ($action === 'profile_get_settings') {
    handleProfileGetSettings($pdo, $userId);

} elseif ($action === 'profile_update_settings') {
    handleProfileUpdateSettings($pdo, $userId);

} elseif ($action === 'profile_overview') {
    handleProfileOverview($pdo, $userId);
}

// ============================================
// NOTIFICATIONS MODULE
// ============================================
elseif ($action === 'notifications_list') {
    handleNotificationsList($pdo, $userId);

} elseif ($action === 'notifications_unread_count') {
    handleNotificationsUnreadCount($pdo, $userId);

} elseif ($action === 'notifications_mark_read') {
    handleNotificationsMarkRead($pdo, $userId);

} elseif ($action === 'notifications_mark_all_read') {
    handleNotificationsMarkAllRead($pdo, $userId);

} elseif ($action === 'notifications_delete') {
    handleNotificationsDelete($pdo, $userId);
}

// ============================================
// BILLS MODULE
// ============================================
elseif ($action === 'get_bills') {
    handleGetBills($pdo, $userId);

} elseif ($action === 'save_bill') {
    handleSaveBill($pdo, $userId);

} elseif ($action === 'mark_bill_paid') {
    handleMarkBillPaid($pdo, $userId);

} elseif ($action === 'delete_bill') {
    handleDeleteBill($pdo, $userId);
}

// ============================================
// DASHBOARD & STATS
// ============================================
elseif ($action === 'dashboard_stats') {
    handleDashboardStats($pdo, $userId);

} elseif ($action === 'recent_transactions') {
    handleRecentTransactions($pdo, $userId);

} elseif ($action === 'chart_data') {
    handleChartData($pdo, $userId);

} elseif ($action === 'statistics_summary') {
    handleStatisticsSummary($pdo, $userId);

} elseif ($action === 'statistics_charts') {
    handleStatisticsCharts($pdo, $userId);

} elseif ($action === 'export_statistics') {
    handleExportStatistics($pdo, $userId);
}

// ============================================
// SUPPORT TICKETS (User)
// ============================================
elseif ($action === 'create_ticket') {
    handleCreateTicket($pdo, $userId);

} elseif ($action === 'get_my_tickets') {
    handleGetMyTickets($pdo, $userId);

} else {
    jsonResponse(false, 'Action không được hỗ trợ: ' . htmlspecialchars($action));
}

// ============================================
// CATEGORY HANDLERS
// ============================================

function handleGetCategories($pdo, $userId)
{
    try {
        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? '';

        $sql = "SELECT id, name, type, color, icon, 
                       COALESCE(spending_limit, 0) as `limit`
                FROM categories 
                WHERE user_id = :user_id";

        $params = [':user_id' => $userId];

        if (!empty($search)) {
            $sql .= " AND name LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }

        if (!empty($type)) {
            $sql .= " AND type = :type";
            $params[':type'] = strtoupper($type);
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($categories as &$cat) {
            $cat['type'] = strtolower($cat['type']);
            $cat['limit'] = (int) $cat['limit'];
        }

        jsonResponse(true, 'Success', $categories);

    } catch (PDOException $e) {
        error_log("Get Categories Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi tải danh mục');
    }
}

function handleGetCategory($pdo, $userId)
{
    try {
        $id = $_GET['id'] ?? 0;

        if (empty($id)) {
            jsonResponse(false, 'ID danh mục không hợp lệ');
        }

        $stmt = $pdo->prepare("
            SELECT id, name, type, color, icon,
                   COALESCE(spending_limit, 0) as `limit`
            FROM categories 
            WHERE id = :id AND user_id = :user_id
        ");

        $stmt->execute([':id' => $id, ':user_id' => $userId]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$category) {
            jsonResponse(false, 'Không tìm thấy danh mục');
        }

        $category['type'] = strtolower($category['type']);
        $category['limit'] = (int) $category['limit'];

        jsonResponse(true, 'Success', $category);

    } catch (PDOException $e) {
        error_log("Get Category Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi tải danh mục');
    }
}

function handleAddCategory($pdo, $userId)
{
    try {
        $name = trim($_POST['name'] ?? '');
        $type = trim($_POST['type'] ?? 'expense');
        $color = trim($_POST['color'] ?? '#EF4444');
        $icon = trim($_POST['icon'] ?? 'fa-wallet');
        $limit = (int) ($_POST['limit'] ?? 0);

        if (empty($name)) {
            jsonResponse(false, 'Tên danh mục không được để trống');
        }

        if (!in_array($type, ['income', 'expense'])) {
            jsonResponse(false, 'Loại danh mục không hợp lệ');
        }

        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE user_id = :user_id AND name = :name");
        $checkStmt->execute([':user_id' => $userId, ':name' => $name]);

        if ($checkStmt->fetchColumn() > 0) {
            jsonResponse(false, 'Danh mục này đã tồn tại');
        }

        $stmt = $pdo->prepare("
            INSERT INTO categories (user_id, name, type, color, icon, spending_limit)
            VALUES (:user_id, :name, :type, :color, :icon, :limit)
        ");

        $stmt->execute([
            ':user_id' => $userId,
            ':name' => $name,
            ':type' => strtoupper($type),
            ':color' => $color,
            ':icon' => $icon,
            ':limit' => $limit > 0 ? $limit : null
        ]);

        jsonResponse(true, 'Thêm danh mục thành công!');

    } catch (PDOException $e) {
        error_log("Add Category Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi thêm danh mục');
    }
}

function handleEditCategory($pdo, $userId)
{
    try {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $type = trim($_POST['type'] ?? 'expense');
        $limit = (int) ($_POST['limit'] ?? 0);

        if (empty($id) || empty($name)) {
            jsonResponse(false, 'Dữ liệu không hợp lệ');
        }

        if (!in_array($type, ['income', 'expense'])) {
            jsonResponse(false, 'Loại danh mục không hợp lệ');
        }

        $checkStmt = $pdo->prepare("SELECT id FROM categories WHERE id = :id AND user_id = :user_id");
        $checkStmt->execute([':id' => $id, ':user_id' => $userId]);

        if (!$checkStmt->fetch()) {
            jsonResponse(false, 'Không tìm thấy danh mục');
        }

        $stmt = $pdo->prepare("
            UPDATE categories 
            SET name = :name, type = :type, spending_limit = :limit
            WHERE id = :id AND user_id = :user_id
        ");

        $stmt->execute([
            ':name' => $name,
            ':type' => strtoupper($type),
            ':limit' => $limit > 0 ? $limit : null,
            ':id' => $id,
            ':user_id' => $userId
        ]);

        jsonResponse(true, 'Cập nhật danh mục thành công!');

    } catch (PDOException $e) {
        error_log("Edit Category Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi cập nhật danh mục');
    }
}

function handleDeleteCategory($pdo, $userId)
{
    try {
        $id = (int) ($_POST['id'] ?? 0);

        if (empty($id)) {
            jsonResponse(false, 'ID danh mục không hợp lệ');
        }

        $checkStmt = $pdo->prepare("SELECT id FROM categories WHERE id = :id AND user_id = :user_id");
        $checkStmt->execute([':id' => $id, ':user_id' => $userId]);

        if (!$checkStmt->fetch()) {
            jsonResponse(false, 'Không tìm thấy danh mục');
        }

        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id AND user_id = :user_id");
        $stmt->execute([':id' => $id, ':user_id' => $userId]);

        jsonResponse(true, 'Xóa danh mục thành công!');

    } catch (PDOException $e) {
        error_log("Delete Category Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi xóa danh mục');
    }
}

// ============================================
// TRANSACTION HANDLERS
// ============================================

function handleGetTransactions($pdo, $userId)
{
    try {
        $page = max(1, (int) ($_GET['page'] ?? $_POST['page'] ?? 1));
        $limit = max(1, min(100, (int) ($_GET['limit'] ?? $_POST['limit'] ?? 10)));
        $offset = ($page - 1) * $limit;

        $search = trim($_GET['search'] ?? $_POST['search'] ?? '');
        $category = (int) ($_GET['category'] ?? $_POST['category'] ?? 0);
        $type = strtoupper(trim($_GET['type'] ?? $_POST['type'] ?? ''));
        $status = strtoupper(trim($_GET['status'] ?? $_POST['status'] ?? ''));
        $singleDate = trim($_GET['date'] ?? $_POST['date'] ?? '');
        $dateFrom = trim($_GET['date_from'] ?? $_POST['date_from'] ?? '');
        $dateTo = trim($_GET['date_to'] ?? $_POST['date_to'] ?? '');
        $minAmountInput = $_GET['min_amount'] ?? $_POST['min_amount'] ?? null;
        $maxAmountInput = $_GET['max_amount'] ?? $_POST['max_amount'] ?? null;
        $minAmount = ($minAmountInput === '' || $minAmountInput === null) ? null : (float) $minAmountInput;
        $maxAmount = ($maxAmountInput === '' || $maxAmountInput === null) ? null : (float) $maxAmountInput;

        if ($singleDate !== '') {
            $dateFrom = $singleDate;
            $dateTo = $singleDate;
        }

        $sortFieldInput = strtolower($_GET['sort_field'] ?? $_POST['sort_field'] ?? 'date');
        $sortDir = strtoupper($_GET['sort_dir'] ?? $_POST['sort_dir'] ?? 'DESC');
        $sortMap = [
            'date' => 't.transaction_date',
            'amount' => 't.amount',
            'created_at' => 't.created_at',
            'category' => 'c.name'
        ];
        $sortField = $sortMap[$sortFieldInput] ?? $sortMap['date'];
        $sortDir = $sortDir === 'ASC' ? 'ASC' : 'DESC';

        $where = ['t.user_id = :user_id'];
        $params = [':user_id' => $userId];

        if ($search !== '') {
            $where[] = '(t.note LIKE :search OR c.name LIKE :search)';
            $params[':search'] = '%' . $search . '%';
        }

        if ($category > 0) {
            $where[] = 't.category_id = :category';
            $params[':category'] = $category;
        }

        if ($type !== '' && in_array($type, ['INCOME', 'EXPENSE'], true)) {
            $where[] = 't.type = :type';
            $params[':type'] = $type;
        }

        if ($status !== '' && in_array($status, ['COMPLETED', 'PENDING', 'CANCELED', 'FLAGGED'], true)) {
            $where[] = 't.status = :status';
            $params[':status'] = $status;
        }

        if ($dateFrom !== '') {
            $where[] = 't.transaction_date >= :date_from';
            $params[':date_from'] = $dateFrom;
        }

        if ($dateTo !== '') {
            $where[] = 't.transaction_date <= :date_to';
            $params[':date_to'] = $dateTo;
        }

        if ($minAmount !== null) {
            $where[] = 't.amount >= :min_amount';
            $params[':min_amount'] = $minAmount;
        }

        if ($maxAmount !== null) {
            $where[] = 't.amount <= :max_amount';
            $params[':max_amount'] = $maxAmount;
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM transactions t INNER JOIN categories c ON t.category_id = c.id $whereSql");
        $stmtCount->execute($params);
        $total = (int) $stmtCount->fetchColumn();

        $sql = "
            SELECT t.id, t.amount, t.type, t.transaction_date as date, t.note,
                   t.category_id, t.status,
                   c.name as category_name,
                   c.icon as category_icon,
                   c.color as category_color
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            $whereSql
            ORDER BY $sortField $sortDir, t.id DESC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($transactions as &$trans) {
            $trans['type'] = strtolower($trans['type']);
            $trans['status'] = strtolower($trans['status']);
            $trans['amount'] = (float) $trans['amount'];
        }

        $summaryStmt = $pdo->prepare("
            SELECT 
                SUM(CASE WHEN t.type = 'INCOME' THEN t.amount ELSE 0 END) AS total_income,
                SUM(CASE WHEN t.type = 'EXPENSE' THEN t.amount ELSE 0 END) AS total_expense
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            $whereSql
        ");
        $summaryStmt->execute($params);
        $summaryRow = $summaryStmt->fetch(PDO::FETCH_ASSOC) ?: ['total_income' => 0, 'total_expense' => 0];
        $totalIncome = (float) ($summaryRow['total_income'] ?? 0);
        $totalExpense = (float) ($summaryRow['total_expense'] ?? 0);

        jsonResponse(true, 'Success', [
            'items' => $transactions,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => max(1, (int) ceil($total / $limit))
            ],
            'summary' => [
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'balance' => $totalIncome - $totalExpense
            ]
        ]);

    } catch (PDOException $e) {
        error_log("Get Transactions Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi tải giao dịch');
    }
}

function handleGetTransaction($pdo, $userId)
{
    try {
        $id = $_GET['id'] ?? 0;

        if (empty($id)) {
            jsonResponse(false, 'ID giao dịch không hợp lệ');
        }

        $stmt = $pdo->prepare("
            SELECT id, amount, type, transaction_date as date, note, category_id
            FROM transactions 
            WHERE id = :id AND user_id = :user_id
        ");

        $stmt->execute([':id' => $id, ':user_id' => $userId]);
        $transaction = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$transaction) {
            jsonResponse(false, 'Không tìm thấy giao dịch');
        }

        $transaction['type'] = strtolower($transaction['type']);
        $transaction['amount'] = (float) $transaction['amount'];

        jsonResponse(true, 'Success', $transaction);

    } catch (PDOException $e) {
        error_log("Get Transaction Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi tải giao dịch');
    }
}

function handleAddTransaction($pdo, $userId)
{
    try {
        $type = trim($_POST['type'] ?? 'expense');
        $amount = (float) ($_POST['amount'] ?? 0);
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $date = trim($_POST['date'] ?? '');
        $note = trim($_POST['note'] ?? '');

        if (!in_array($type, ['income', 'expense'])) {
            jsonResponse(false, 'Loại giao dịch không hợp lệ');
        }

        if ($amount <= 0) {
            jsonResponse(false, 'Số tiền phải lớn hơn 0');
        }

        if (empty($categoryId) || empty($date)) {
            jsonResponse(false, 'Vui lòng điền đầy đủ thông tin');
        }

        $catStmt = $pdo->prepare("SELECT id, name FROM categories WHERE id = :id AND user_id = :user_id");
        $catStmt->execute([':id' => $categoryId, ':user_id' => $userId]);

        $category = $catStmt->fetch(PDO::FETCH_ASSOC);
        if (!$category) {
            jsonResponse(false, 'Danh mục không hợp lệ');
        }

        $stmt = $pdo->prepare("
            INSERT INTO transactions (user_id, category_id, amount, type, transaction_date, note)
            VALUES (:user_id, :category_id, :amount, :type, :date, :note)
        ");

        $stmt->execute([
            ':user_id' => $userId,
            ':category_id' => $categoryId,
            ':amount' => $amount,
            ':type' => strtoupper($type),
            ':date' => $date,
            ':note' => !empty($note) ? $note : null
        ]);

        try {
            $dateTime = DateTime::createFromFormat('Y-m-d', $date) ?: null;
            $displayDate = $dateTime ? $dateTime->format('d/m/Y') : $date;
            $amountText = formatMoney($amount);
            $isIncome = $type === 'income';
            $title = $isIncome ? 'Giao dịch thu nhập mới' : 'Giao dịch chi tiêu mới';
            $actionVerb = $isIncome ? 'nhận' : 'chi';
            $message = sprintf('Bạn vừa %s %s cho "%s" vào ngày %s.', $actionVerb, $amountText, $category['name'], $displayDate);
            $linkUrl = '/public/user/transactions.php';
            createNotification($pdo, $userId, $isIncome ? 'success' : 'warning', $title, $message, $linkUrl);
        } catch (Throwable $notifyError) {
            error_log('Transaction notification error: ' . $notifyError->getMessage());
        }

        jsonResponse(true, 'Thêm giao dịch thành công!');

    } catch (PDOException $e) {
        error_log("Add Transaction Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi thêm giao dịch');
    }
}

function handleEditTransaction($pdo, $userId)
{
    try {
        $id = (int) ($_POST['id'] ?? 0);
        $type = trim($_POST['type'] ?? 'expense');
        $amount = (float) ($_POST['amount'] ?? 0);
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $date = trim($_POST['date'] ?? '');
        $note = trim($_POST['note'] ?? '');

        if (empty($id)) {
            jsonResponse(false, 'ID giao dịch không hợp lệ');
        }

        if (!in_array($type, ['income', 'expense']) || $amount <= 0 || empty($categoryId) || empty($date)) {
            jsonResponse(false, 'Dữ liệu không hợp lệ');
        }

        $checkStmt = $pdo->prepare("SELECT id FROM transactions WHERE id = :id AND user_id = :user_id");
        $checkStmt->execute([':id' => $id, ':user_id' => $userId]);

        if (!$checkStmt->fetch()) {
            jsonResponse(false, 'Không tìm thấy giao dịch');
        }

        $stmt = $pdo->prepare("
            UPDATE transactions 
            SET category_id = :category_id, amount = :amount, type = :type,
                transaction_date = :date, note = :note
            WHERE id = :id AND user_id = :user_id
        ");

        $stmt->execute([
            ':category_id' => $categoryId,
            ':amount' => $amount,
            ':type' => strtoupper($type),
            ':date' => $date,
            ':note' => !empty($note) ? $note : null,
            ':id' => $id,
            ':user_id' => $userId
        ]);

        jsonResponse(true, 'Cập nhật giao dịch thành công!');

    } catch (PDOException $e) {
        error_log("Edit Transaction Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi cập nhật giao dịch');
    }
}

function handleDeleteTransaction($pdo, $userId)
{
    try {
        $id = (int) ($_POST['id'] ?? 0);

        if (empty($id)) {
            jsonResponse(false, 'ID giao dịch không hợp lệ');
        }

        $checkStmt = $pdo->prepare("SELECT id FROM transactions WHERE id = :id AND user_id = :user_id");
        $checkStmt->execute([':id' => $id, ':user_id' => $userId]);

        if (!$checkStmt->fetch()) {
            jsonResponse(false, 'Không tìm thấy giao dịch');
        }

        $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = :id AND user_id = :user_id");
        $stmt->execute([':id' => $id, ':user_id' => $userId]);

        jsonResponse(true, 'Xóa giao dịch thành công!');

    } catch (PDOException $e) {
        error_log("Delete Transaction Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi xóa giao dịch');
    }
}

// ============================================
// STATISTICS HANDLERS
// ============================================

function handleGetStatsOverview($pdo, $userId)
{
    try {
        $dateFrom = $_REQUEST['date_from'] ?? null;
        $dateTo = $_REQUEST['date_to'] ?? null;

        if (!$dateFrom || !$dateTo) {
            $dateFrom = $dateFrom ?: date('Y-m-01');
            $dateTo = $dateTo ?: date('Y-m-t');
        }

        $sql = "
            SELECT
                SUM(CASE WHEN type = 'INCOME' THEN amount ELSE 0 END) AS total_income,
                SUM(CASE WHEN type = 'EXPENSE' THEN amount ELSE 0 END) AS total_expense
            FROM transactions
            WHERE user_id = :uid
              AND transaction_date BETWEEN :date_from AND :date_to
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':uid' => $userId,
            ':date_from' => $dateFrom,
            ':date_to' => $dateTo,
        ]);

        $row = $stmt->fetch() ?: ['total_income' => 0, 'total_expense' => 0];

        $income = (float) ($row['total_income'] ?? 0);
        $expense = (float) ($row['total_expense'] ?? 0);
        $balance = $income - $expense;

        jsonResponse(true, 'Lấy thống kê tổng quan thành công', [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'total_income' => $income,
            'total_expense' => $expense,
            'balance' => $balance,
        ]);

    } catch (PDOException $e) {
        error_log("Stats Overview Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi tải thống kê');
    }
}

function handleGetStatsByCategory($pdo, $userId)
{
    try {
        $dateFrom = $_REQUEST['date_from'] ?? null;
        $dateTo = $_REQUEST['date_to'] ?? null;

        if (!$dateFrom || !$dateTo) {
            $dateFrom = $dateFrom ?: date('Y-m-01');
            $dateTo = $dateTo ?: date('Y-m-t');
        }

        $sql = "
            SELECT 
                c.id AS category_id,
                c.name AS category_name,
                c.type AS category_type,
                SUM(CASE WHEN t.type = 'INCOME' THEN t.amount ELSE 0 END) AS total_income,
                SUM(CASE WHEN t.type = 'EXPENSE' THEN t.amount ELSE 0 END) AS total_expense
            FROM transactions t
            JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = :uid
              AND t.transaction_date BETWEEN :date_from AND :date_to
            GROUP BY c.id, c.name, c.type
            ORDER BY c.name ASC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':uid' => $userId,
            ':date_from' => $dateFrom,
            ':date_to' => $dateTo,
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        jsonResponse(true, 'Lấy thống kê theo danh mục thành công', [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'items' => $rows,
        ]);

    } catch (PDOException $e) {
        error_log("Stats By Category Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi tải thống kê');
    }
}

function handleGetStatsTimeseries($pdo, $userId)
{
    try {
        $dateFrom = $_REQUEST['date_from'] ?? null;
        $dateTo = $_REQUEST['date_to'] ?? null;

        if (!$dateFrom || !$dateTo) {
            $dateFrom = $dateFrom ?: date('Y-m-01');
            $dateTo = $dateTo ?: date('Y-m-t');
        }

        $sql = "
            SELECT
                transaction_date,
                SUM(CASE WHEN type = 'INCOME' THEN amount ELSE 0 END) AS total_income,
                SUM(CASE WHEN type = 'EXPENSE' THEN amount ELSE 0 END) AS total_expense
            FROM transactions
            WHERE user_id = :uid
              AND transaction_date BETWEEN :date_from AND :date_to
            GROUP BY transaction_date
            ORDER BY transaction_date ASC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':uid' => $userId,
            ':date_from' => $dateFrom,
            ':date_to' => $dateTo,
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        jsonResponse(true, 'Lấy thống kê theo ngày thành công', [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'items' => $rows,
        ]);

    } catch (PDOException $e) {
        error_log("Stats Timeseries Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi tải thống kê');
    }
}

// ============================================
// BUDGET HANDLERS
// ============================================

function handleGetBudgets($pdo, $userId)
{
    try {
        // Get current month by default
        $currentMonth = date('Y-m-01');
        $currentMonthEnd = date('Y-m-t');

        $sql = "
            SELECT 
                b.id,
                b.category_id,
                b.amount,
                b.period_start,
                b.period_end,
                b.note,
                b.status,
                c.name as category,
                c.color,
                c.icon,
                COALESCE(
                    (SELECT SUM(t.amount) 
                     FROM transactions t 
                     WHERE t.user_id = :user_id 
                       AND t.category_id = b.category_id 
                       AND t.type = 'EXPENSE'
                       AND t.transaction_date BETWEEN b.period_start AND b.period_end
                    ), 0
                ) as spent
            FROM budgets b
            INNER JOIN categories c ON b.category_id = c.id
            WHERE b.user_id = :user_id
              AND b.status = 'ACTIVE'
              AND b.period_start <= :period_end
              AND b.period_end >= :period_start
            ORDER BY b.created_at DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':period_start' => $currentMonth,
            ':period_end' => $currentMonthEnd
        ]);

        $budgets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate summary
        $totalBudget = 0;
        $totalSpent = 0;

        foreach ($budgets as &$budget) {
            $budget['id'] = (int) $budget['id'];
            $budget['category_id'] = (int) $budget['category_id'];
            $budget['amount'] = (float) $budget['amount'];
            $budget['spent'] = (float) $budget['spent'];
            $budget['note'] = $budget['note'] ?? '';
            $totalBudget += $budget['amount'];
            $totalSpent += $budget['spent'];
        }

        jsonResponse(true, 'Success', $budgets, [
            'summary' => [
                'total_budget' => $totalBudget,
                'total_spent' => $totalSpent,
                'total_remaining' => $totalBudget - $totalSpent
            ]
        ]);

    } catch (PDOException $e) {
        error_log("Get Budgets Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi tải ngân sách');
    }
}

function handleGetBudget($pdo, $userId)
{
    try {
        $id = $_GET['id'] ?? 0;

        if (empty($id)) {
            jsonResponse(false, 'ID ngân sách không hợp lệ');
        }

        $stmt = $pdo->prepare("
            SELECT id, category_id, amount, period_start, period_end, note, status
            FROM budgets 
            WHERE id = :id AND user_id = :user_id
        ");

        $stmt->execute([':id' => $id, ':user_id' => $userId]);
        $budget = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$budget) {
            jsonResponse(false, 'Không tìm thấy ngân sách');
        }

        $budget['id'] = (int) $budget['id'];
        $budget['category_id'] = (int) $budget['category_id'];
        $budget['amount'] = (float) $budget['amount'];
        $budget['note'] = $budget['note'] ?? '';

        jsonResponse(true, 'Success', $budget);

    } catch (PDOException $e) {
        error_log("Get Budget Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi tải ngân sách');
    }
}

function handleSaveBudget($pdo, $userId)
{
    try {
        $id = (int) ($_POST['id'] ?? 0);
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $amount = (float) ($_POST['amount'] ?? 0);
        $note = trim($_POST['note'] ?? '');
        $noteValue = $note !== '' ? $note : null;

        // Auto-set period to current month
        $periodStart = date('Y-m-01');
        $periodEnd = date('Y-m-t');

        // Validation
        if (empty($categoryId) || $amount <= 0) {
            jsonResponse(false, 'Du lieu khong hop le');
        }

        // Verify category belongs to user
        $catStmt = $pdo->prepare("SELECT id FROM categories WHERE id = :id AND user_id = :user_id");
        $catStmt->execute([':id' => $categoryId, ':user_id' => $userId]);

        if (!$catStmt->fetch()) {
            jsonResponse(false, 'Danh muc khong hop le');
        }

        if ($id > 0) {
            // Update existing budget
            $checkStmt = $pdo->prepare("SELECT id, period_start, period_end FROM budgets WHERE id = :id AND user_id = :user_id");
            $checkStmt->execute([':id' => $id, ':user_id' => $userId]);

            $budget = $checkStmt->fetch(PDO::FETCH_ASSOC);
            if (!$budget) {
                jsonResponse(false, 'Khong tim thay ngan sach');
            }

            // Avoid duplicate budget when switching category inside the same period
            $dupStmt = $pdo->prepare("
                SELECT id FROM budgets 
                WHERE user_id = :user_id 
                  AND category_id = :category_id 
                  AND period_start = :period_start 
                  AND period_end = :period_end
                  AND id <> :id
            ");
            $dupStmt->execute([
                ':user_id' => $userId,
                ':category_id' => $categoryId,
                ':period_start' => $budget['period_start'],
                ':period_end' => $budget['period_end'],
                ':id' => $id
            ]);

            if ($dupStmt->fetch()) {
                jsonResponse(false, 'Ngan sach cho danh muc nay trong thoi gian nay da ton tai');
            }

            $stmt = $pdo->prepare("
                UPDATE budgets 
                SET category_id = :category_id,
                    amount = :amount,
                    note = :note
                WHERE id = :id AND user_id = :user_id
            ");

            $stmt->execute([
                ':category_id' => $categoryId,
                ':amount' => $amount,
                ':note' => $noteValue,
                ':id' => $id,
                ':user_id' => $userId
            ]);

            jsonResponse(true, 'Cap nhat ngan sach thanh cong!');

        } else {
            // Create new budget
            $dupStmt = $pdo->prepare("
                SELECT id FROM budgets 
                WHERE user_id = :user_id 
                  AND category_id = :category_id 
                  AND period_start = :period_start 
                  AND period_end = :period_end
            ");
            $dupStmt->execute([
                ':user_id' => $userId,
                ':category_id' => $categoryId,
                ':period_start' => $periodStart,
                ':period_end' => $periodEnd
            ]);

            if ($dupStmt->fetch()) {
                jsonResponse(false, 'Ngan sach cho danh muc nay trong thang nay da ton tai');
            }

            $stmt = $pdo->prepare("
                INSERT INTO budgets (user_id, category_id, amount, period_start, period_end, note, status)
                VALUES (:user_id, :category_id, :amount, :period_start, :period_end, :note, 'ACTIVE')
            ");

            $stmt->execute([
                ':user_id' => $userId,
                ':category_id' => $categoryId,
                ':amount' => $amount,
                ':period_start' => $periodStart,
                ':period_end' => $periodEnd,
                ':note' => $noteValue
            ]);

            jsonResponse(true, 'Them ngan sach thanh cong!');
        }

    } catch (PDOException $e) {
        error_log("Save Budget Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi luu ngan sach');
    }
}


function handleDeleteBudget($pdo, $userId)
{
    try {
        $id = (int) ($_POST['id'] ?? 0);

        if (empty($id)) {
            jsonResponse(false, 'ID ngân sách không hợp lệ');
        }

        $checkStmt = $pdo->prepare("SELECT id FROM budgets WHERE id = :id AND user_id = :user_id");
        $checkStmt->execute([':id' => $id, ':user_id' => $userId]);

        if (!$checkStmt->fetch()) {
            jsonResponse(false, 'Không tìm thấy ngân sách');
        }

        $stmt = $pdo->prepare("DELETE FROM budgets WHERE id = :id AND user_id = :user_id");
        $stmt->execute([':id' => $id, ':user_id' => $userId]);

        jsonResponse(true, 'Xóa ngân sách thành công!');

    } catch (PDOException $e) {
        error_log("Delete Budget Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi xóa ngân sách');
    }
}

// ============================================
// GOALS HANDLERS (MODULE 7) - START
// ============================================

function handleGetGoals($pdo, $userId)
{
    try {
        $filter = strtolower($_GET['filter'] ?? 'all');

        $statusMap = [
            'all' => [],
            'active' => ['ACTIVE'],
            'completed' => ['COMPLETED'],
            'archived' => ['ARCHIVED']
        ];

        $sql = "
            SELECT id, name, target_amount, current_amount, start_date, deadline, description, status
            FROM goals
            WHERE user_id = :user_id
        ";
        $params = [':user_id' => $userId];

        if (!empty($statusMap[$filter])) {
            $in = implode("','", $statusMap[$filter]);
            $sql .= " AND status IN ('{$in}')";
        }

        $sql .= " ORDER BY deadline ASC, created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $goals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $summary = [
            'total' => 0,
            'active' => 0,
            'completed' => 0,
            'total_saved' => 0
        ];

        foreach ($goals as &$goal) {
            $goal['id'] = (int) $goal['id'];
            $goal['target_amount'] = (float) $goal['target_amount'];
            $goal['current_amount'] = (float) $goal['current_amount'];
            $goal['status'] = strtolower($goal['status']);
            $goal['description'] = $goal['description'] ?? '';

            $summary['total']++;
            if ($goal['status'] === 'active') {
                $summary['active']++;
            } elseif ($goal['status'] === 'completed') {
                $summary['completed']++;
            }
            $summary['total_saved'] += $goal['current_amount'];
        }

        jsonResponse(true, 'Success', $goals, ['summary' => $summary]);

    } catch (PDOException $e) {
        error_log("Get Goals Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi tai muc tieu');
    }
}

function handleGetGoal($pdo, $userId)
{
    try {
        $id = (int) ($_GET['id'] ?? 0);
        if (empty($id)) {
            jsonResponse(false, 'ID muc tieu khong hop le');
        }

        $stmt = $pdo->prepare("SELECT id, name, target_amount, current_amount, start_date, deadline, description, status FROM goals WHERE id = :id AND user_id = :user_id");
        $stmt->execute([':id' => $id, ':user_id' => $userId]);
        $goal = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$goal) {
            jsonResponse(false, 'Khong tim thay muc tieu');
        }

        $goal['id'] = (int) $goal['id'];
        $goal['target_amount'] = (float) $goal['target_amount'];
        $goal['current_amount'] = (float) $goal['current_amount'];
        $goal['status'] = strtolower($goal['status']);
        $goal['description'] = $goal['description'] ?? '';

        jsonResponse(true, 'Success', $goal);

    } catch (PDOException $e) {
        error_log("Get Goal Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi tai muc tieu');
    }
}

function computeGoalStatus($currentAmount, $targetAmount, $deadline)
{
    if ($currentAmount >= $targetAmount) {
        return 'COMPLETED';
    }
    if (!empty($deadline) && strtotime(date('Y-m-d')) > strtotime($deadline)) {
        return 'FAILED';
    }
    return 'ACTIVE';
}

function handleSaveGoal($pdo, $userId)
{
    try {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $targetAmount = (float) ($_POST['target_amount'] ?? 0);
        $currentAmount = isset($_POST['current_amount']) ? (float) $_POST['current_amount'] : 0;
        $startDate = trim($_POST['start_date'] ?? date('Y-m-d'));
        $deadline = trim($_POST['deadline'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($name) || $targetAmount <= 0 || empty($deadline) || empty($startDate)) {
            jsonResponse(false, 'Du lieu khong hop le');
        }

        $status = computeGoalStatus($currentAmount, $targetAmount, $deadline);

        if ($id > 0) {
            $checkStmt = $pdo->prepare("SELECT id FROM goals WHERE id = :id AND user_id = :user_id");
            $checkStmt->execute([':id' => $id, ':user_id' => $userId]);
            if (!$checkStmt->fetch()) {
                jsonResponse(false, 'Khong tim thay muc tieu');
            }

            $stmt = $pdo->prepare("
                UPDATE goals
                SET name = :name,
                    target_amount = :target_amount,
                    current_amount = :current_amount,
                    start_date = :start_date,
                    deadline = :deadline,
                    description = :description,
                    status = :status
                WHERE id = :id AND user_id = :user_id
            ");
            $stmt->execute([
                ':name' => $name,
                ':target_amount' => $targetAmount,
                ':current_amount' => $currentAmount,
                ':start_date' => $startDate,
                ':deadline' => $deadline,
                ':description' => $description !== '' ? $description : null,
                ':status' => $status,
                ':id' => $id,
                ':user_id' => $userId
            ]);

            jsonResponse(true, 'Cap nhat muc tieu thanh cong');

        } else {
            $stmt = $pdo->prepare("
                INSERT INTO goals (user_id, name, target_amount, current_amount, start_date, deadline, description, status)
                VALUES (:user_id, :name, :target_amount, :current_amount, :start_date, :deadline, :description, :status)
            ");
            $stmt->execute([
                ':user_id' => $userId,
                ':name' => $name,
                ':target_amount' => $targetAmount,
                ':current_amount' => $currentAmount,
                ':start_date' => $startDate,
                ':deadline' => $deadline,
                ':description' => $description !== '' ? $description : null,
                ':status' => $status
            ]);

            jsonResponse(true, 'Them muc tieu thanh cong');
        }

    } catch (PDOException $e) {
        error_log("Save Goal Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi luu muc tieu');
    }
}

function handleAddSavings($pdo, $userId)
{
    try {
        $goalId = (int) ($_POST['goal_id'] ?? 0);
        $amount = (float) ($_POST['amount'] ?? 0);
        if ($goalId <= 0 || $amount <= 0) {
            jsonResponse(false, 'Du lieu khong hop le');
        }

        $stmt = $pdo->prepare("SELECT id, target_amount, current_amount, deadline FROM goals WHERE id = :id AND user_id = :user_id");
        $stmt->execute([':id' => $goalId, ':user_id' => $userId]);
        $goal = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$goal) {
            jsonResponse(false, 'Khong tim thay muc tieu');
        }

        $newCurrent = (float) $goal['current_amount'] + $amount;
        $status = computeGoalStatus($newCurrent, (float) $goal['target_amount'], $goal['deadline']);

        $upd = $pdo->prepare("UPDATE goals SET current_amount = :current_amount, status = :status WHERE id = :id AND user_id = :user_id");
        $upd->execute([
            ':current_amount' => $newCurrent,
            ':status' => $status,
            ':id' => $goalId,
            ':user_id' => $userId
        ]);

        jsonResponse(true, 'Cap nhat so tien thanh cong');

    } catch (PDOException $e) {
        error_log("Add Savings Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi cap nhat so tien');
    }
}

function handleDeleteGoal($pdo, $userId)
{
    try {
        $id = (int) ($_POST['id'] ?? 0);
        if (empty($id)) {
            jsonResponse(false, 'ID muc tieu khong hop le');
        }

        $checkStmt = $pdo->prepare("SELECT id FROM goals WHERE id = :id AND user_id = :user_id");
        $checkStmt->execute([':id' => $id, ':user_id' => $userId]);
        if (!$checkStmt->fetch()) {
            jsonResponse(false, 'Khong tim thay muc tieu');
        }

        $stmt = $pdo->prepare("DELETE FROM goals WHERE id = :id AND user_id = :user_id");
        $stmt->execute([':id' => $id, ':user_id' => $userId]);

        jsonResponse(true, 'Xoa muc tieu thanh cong');

    } catch (PDOException $e) {
        error_log("Delete Goal Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi xoa muc tieu');
    }
}

// ============================================
// GOALS HANDLERS (MODULE 7) - END
// ============================================
// ============================================
// RECURRING HANDLERS (MODULE 8) - START
// ============================================

function computeNextRunDate($currentDate, $frequency)
{
    $date = new DateTime($currentDate);
    switch (strtoupper($frequency)) {
        case 'DAILY':
            $date->modify('+1 day');
            break;
        case 'WEEKLY':
            $date->modify('+7 days');
            break;
        case 'MONTHLY':
            $date->modify('+1 month');
            break;
        case 'QUARTERLY':
            $date->modify('+3 months');
            break;
        case 'YEARLY':
            $date->modify('+1 year');
            break;
        default:
            $date->modify('+1 month');
    }
    return $date->format('Y-m-d');
}

function handleGetRecurringTransactions($pdo, $userId)
{
    try {
        $sql = "
            SELECT r.id, r.name, r.amount, r.frequency, r.next_run_date, r.start_date, r.status,
                   r.category_id, r.note,
                   c.name AS category
            FROM recurring_transactions r
            LEFT JOIN categories c ON r.category_id = c.id
            WHERE r.user_id = :user_id
            ORDER BY r.next_run_date ASC, r.created_at DESC
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $summary = [
            'total' => 0,
            'active' => 0,
            'monthly_total' => 0
        ];

        foreach ($rows as &$row) {
            $row['id'] = (int) $row['id'];
            $row['category_id'] = $row['category_id'] !== null ? (int) $row['category_id'] : null;
            $row['amount'] = (float) $row['amount'];
            $row['status'] = strtolower($row['status']);
            $row['note'] = $row['note'] ?? '';
            $row['category'] = $row['category'] ?? '';
            $row['frequency'] = strtolower($row['frequency']);

            $summary['total']++;
            if ($row['status'] === 'active') {
                $summary['active']++;
                $summary['monthly_total'] += $row['amount'];
            }
        }

        jsonResponse(true, 'Success', $rows, ['summary' => $summary]);

    } catch (PDOException $e) {
        error_log("Get Recurring Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi tai giao dich dinh ky');
    }
}

function handleGetRecurring($pdo, $userId)
{
    try {
        $id = (int) ($_GET['id'] ?? 0);
        if (empty($id)) {
            jsonResponse(false, 'ID khong hop le');
        }

        $stmt = $pdo->prepare("
            SELECT id, name, amount, category_id, frequency, start_date, next_run_date, status, note
            FROM recurring_transactions
            WHERE id = :id AND user_id = :user_id
        ");
        $stmt->execute([':id' => $id, ':user_id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            jsonResponse(false, 'Khong tim thay ban ghi');
        }

        $row['id'] = (int) $row['id'];
        $row['category_id'] = $row['category_id'] !== null ? (int) $row['category_id'] : null;
        $row['amount'] = (float) $row['amount'];
        $row['status'] = strtolower($row['status']);
        $row['frequency'] = strtolower($row['frequency']);
        $row['note'] = $row['note'] ?? '';

        jsonResponse(true, 'Success', $row);

    } catch (PDOException $e) {
        error_log("Get Recurring One Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi tai giao dich');
    }
}

function handleSaveRecurring($pdo, $userId)
{
    try {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $amount = (float) ($_POST['amount'] ?? 0);
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $frequency = strtolower(trim($_POST['frequency'] ?? 'monthly'));
        $startDate = trim($_POST['start_date'] ?? '');
        $note = trim($_POST['note'] ?? '');
        $statusInput = strtolower(trim($_POST['status'] ?? 'active'));

        $allowedFrequency = ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'];
        if (!in_array($frequency, $allowedFrequency, true)) {
            $frequency = 'monthly';
        }

        if (empty($name) || $amount <= 0 || empty($startDate)) {
            jsonResponse(false, 'Du lieu khong hop le');
        }

        if ($categoryId > 0) {
            $catStmt = $pdo->prepare("SELECT id, type FROM categories WHERE id = :id AND user_id = :user_id");
            $catStmt->execute([':id' => $categoryId, ':user_id' => $userId]);
            $catRow = $catStmt->fetch(PDO::FETCH_ASSOC);
            if (!$catRow) {
                jsonResponse(false, 'Danh muc khong hop le');
            }
            $type = strtoupper($catRow['type']);
        } else {
            $categoryId = null;
            $type = 'EXPENSE';
        }

        $statusDb = ($statusInput === 'inactive') ? 'INACTIVE' : 'ACTIVE';
        $nextRun = $startDate;

        if ($id > 0) {
            $checkStmt = $pdo->prepare("SELECT id FROM recurring_transactions WHERE id = :id AND user_id = :user_id");
            $checkStmt->execute([':id' => $id, ':user_id' => $userId]);
            if (!$checkStmt->fetch()) {
                jsonResponse(false, 'Khong tim thay giao dich dinh ky');
            }

            $stmt = $pdo->prepare("
                UPDATE recurring_transactions
                SET name = :name,
                    amount = :amount,
                    category_id = :category_id,
                    frequency = :frequency,
                    start_date = :start_date,
                    next_run_date = :next_run_date,
                    status = :status,
                    note = :note,
                    type = :type
                WHERE id = :id AND user_id = :user_id
            ");
            $stmt->execute([
                ':name' => $name,
                ':amount' => $amount,
                ':category_id' => $categoryId,
                ':frequency' => strtoupper($frequency),
                ':start_date' => $startDate,
                ':next_run_date' => $nextRun,
                ':status' => $statusDb,
                ':note' => $note !== '' ? $note : null,
                ':type' => $type,
                ':id' => $id,
                ':user_id' => $userId
            ]);

            jsonResponse(true, 'Cap nhat giao dich dinh ky thanh cong');

        } else {
            $stmt = $pdo->prepare("
                INSERT INTO recurring_transactions
                (user_id, name, amount, category_id, frequency, start_date, next_run_date, status, note, type)
                VALUES
                (:user_id, :name, :amount, :category_id, :frequency, :start_date, :next_run_date, :status, :note, :type)
            ");

            $stmt->execute([
                ':user_id' => $userId,
                ':name' => $name,
                ':amount' => $amount,
                ':category_id' => $categoryId,
                ':frequency' => strtoupper($frequency),
                ':start_date' => $startDate,
                ':next_run_date' => $nextRun,
                ':status' => $statusDb,
                ':note' => $note !== '' ? $note : null,
                ':type' => $type
            ]);

            jsonResponse(true, 'Them giao dich dinh ky thanh cong');
        }

    } catch (PDOException $e) {
        error_log("Save Recurring Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi luu giao dich dinh ky');
    }
}

function handleToggleRecurringStatus($pdo, $userId)
{
    try {
        $id = (int) ($_POST['id'] ?? 0);
        $status = strtolower($_POST['status'] ?? 'active');
        if (empty($id)) {
            jsonResponse(false, 'ID khong hop le');
        }

        $check = $pdo->prepare("SELECT id FROM recurring_transactions WHERE id = :id AND user_id = :user_id");
        $check->execute([':id' => $id, ':user_id' => $userId]);
        if (!$check->fetch()) {
            jsonResponse(false, 'Khong tim thay giao dich dinh ky');
        }

        $statusDb = $status === 'inactive' ? 'INACTIVE' : 'ACTIVE';
        $upd = $pdo->prepare("UPDATE recurring_transactions SET status = :status WHERE id = :id AND user_id = :user_id");
        $upd->execute([':status' => $statusDb, ':id' => $id, ':user_id' => $userId]);

        jsonResponse(true, 'Cap nhat trang thai thanh cong');

    } catch (PDOException $e) {
        error_log("Toggle Recurring Status Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi cap nhat trang thai');
    }
}

function handleDeleteRecurring($pdo, $userId)
{
    try {
        $id = (int) ($_POST['id'] ?? 0);
        if (empty($id)) {
            jsonResponse(false, 'ID khong hop le');
        }

        $check = $pdo->prepare("SELECT id FROM recurring_transactions WHERE id = :id AND user_id = :user_id");
        $check->execute([':id' => $id, ':user_id' => $userId]);
        if (!$check->fetch()) {
            jsonResponse(false, 'Khong tim thay giao dich dinh ky');
        }

        $del = $pdo->prepare("DELETE FROM recurring_transactions WHERE id = :id AND user_id = :user_id");
        $del->execute([':id' => $id, ':user_id' => $userId]);

        jsonResponse(true, 'Xoa giao dich dinh ky thanh cong');

    } catch (PDOException $e) {
        error_log("Delete Recurring Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi xoa giao dich dinh ky');
    }
}

function handleRunRecurringNow($pdo, $userId)
{
    try {
        $id = (int) ($_POST['id'] ?? 0);
        $runDate = $_POST['run_date'] ?? date('Y-m-d');
        if (empty($id)) {
            jsonResponse(false, 'ID khong hop le');
        }

        $stmt = $pdo->prepare("
            SELECT r.*, c.type AS category_type
            FROM recurring_transactions r
            LEFT JOIN categories c ON r.category_id = c.id
            WHERE r.id = :id AND r.user_id = :user_id AND r.status = 'ACTIVE'
        ");
        $stmt->execute([':id' => $id, ':user_id' => $userId]);
        $rec = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$rec) {
            jsonResponse(false, 'Khong tim thay giao dich dinh ky dang hoat dong');
        }

        $transType = $rec['type'] ?? $rec['category_type'] ?? 'EXPENSE';
        $note = '[Recurring] ' . $rec['name'];
        if (!empty($rec['note'])) {
            $note .= ' - ' . $rec['note'];
        }

        $ins = $pdo->prepare("
            INSERT INTO transactions (user_id, category_id, amount, type, transaction_date, note)
            VALUES (:user_id, :category_id, :amount, :type, :date, :note)
        ");
        $ins->execute([
            ':user_id' => $userId,
            ':category_id' => $rec['category_id'] ?: null,
            ':amount' => $rec['amount'],
            ':type' => strtoupper($transType),
            ':date' => $runDate,
            ':note' => $note
        ]);

        $newNext = computeNextRunDate($rec['next_run_date'], $rec['frequency']);

        $upd = $pdo->prepare("
            UPDATE recurring_transactions
            SET last_run_date = :last_run_date,
                next_run_date = :next_run_date
            WHERE id = :id AND user_id = :user_id
        ");
        $upd->execute([
            ':last_run_date' => $runDate,
            ':next_run_date' => $newNext,
            ':id' => $id,
            ':user_id' => $userId
        ]);

        jsonResponse(true, 'Da thuc thi giao dich dinh ky');

    } catch (PDOException $e) {
        error_log("Run Recurring Now Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi chay giao dich dinh ky');
    }
}

// ============================================
// RECURRING HANDLERS (MODULE 8) - END
// ============================================

// ============================================
// PROFILE HANDLERS (MODULE 10) - START
// ============================================

function handleProfileGet($pdo, $userId)
{
    try {
        $stmt = $pdo->prepare("
            SELECT 
                id,
                fullname AS full_name,
                email,
                phone,
                avatar_url,
                address,
                DATE_FORMAT(date_of_birth, '%Y-%m-%d') AS date_of_birth,
                gender,
                bio,
                created_at
            FROM users
            WHERE id = :id
        ");
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            jsonResponse(false, 'Khong tim thay tai khoan');
        }

        jsonResponse(true, 'Success', $user);

    } catch (PDOException $e) {
        error_log("Profile Get Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi tai thong tin');
    }
}

function handleProfileUpdate($pdo, $userId)
{
    try {
        $fullName = trim($_POST['full_name'] ?? $_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $avatar = trim($_POST['avatar_url'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $gender = strtoupper(trim($_POST['gender'] ?? ''));
        $bio = trim($_POST['bio'] ?? '');
        $dobInput = trim($_POST['date_of_birth'] ?? $_POST['dob'] ?? '');

        if (empty($fullName)) {
            jsonResponse(false, 'Ho ten khong duoc de trong');
        }

        if (!empty($email)) {
            $checkEmail = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id <> :id");
            $checkEmail->execute([':email' => $email, ':id' => $userId]);
            if ($checkEmail->fetch()) {
                jsonResponse(false, 'Email da duoc su dung');
            }
        }

        $allowedGenders = ['MALE','FEMALE','OTHER','PREFER_NOT'];
        if (!in_array($gender, $allowedGenders, true)) {
            $gender = 'OTHER';
        }

        $dob = null;
        if ($dobInput !== '') {
            $dt = DateTime::createFromFormat('Y-m-d', $dobInput);
            if ($dt === false) {
                jsonResponse(false, 'Ngay sinh khong hop le (dinh dang YYYY-MM-DD)');
            }
            $dob = $dt->format('Y-m-d');
        }

        $sql = "
            UPDATE users
            SET fullname = :fullname,
                phone = :phone,
                avatar_url = :avatar_url,
                address = :address,
                date_of_birth = :dob,
                gender = :gender,
                bio = :bio
        ";
        $params = [
            ':fullname' => $fullName,
            ':phone' => $phone !== '' ? $phone : null,
            ':avatar_url' => $avatar !== '' ? $avatar : null,
            ':address' => $address !== '' ? $address : null,
            ':dob' => $dob,
            ':gender' => $gender,
            ':bio' => $bio !== '' ? $bio : null,
            ':id' => $userId
        ];
        if (!empty($email)) {
            $sql .= ", email = :email";
            $params[':email'] = $email;
        }
        $sql .= " WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $_SESSION['user_name'] = $fullName;
        $_SESSION['user_phone'] = $phone;
        $_SESSION['user_avatar'] = $avatar !== '' ? $avatar : null;
        if (!empty($email)) {
            $_SESSION['user_email'] = $email;
        }

        jsonResponse(true, 'Cap nhat thong tin thanh cong');

    } catch (PDOException $e) {
        error_log("Profile Update Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi cap nhat thong tin');
    }
}

function handleProfileChangePassword($pdo, $userId)
{
    try {
        $oldPassword = $_POST['old_password'] ?? $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (empty($oldPassword) || empty($newPassword) || empty($confirm)) {
            jsonResponse(false, 'Vui long nhap day du mat khau');
        }
        if (strlen($newPassword) < 6) {
            jsonResponse(false, 'Mat khau moi phai tu 6 ky tu');
        }
        if ($newPassword !== $confirm) {
            jsonResponse(false, 'Xac nhan mat khau khong khop');
        }

        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || !password_verify($oldPassword, $row['password_hash'])) {
            jsonResponse(false, 'Mat khau hien tai khong dung');
        }

        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $upd = $pdo->prepare("UPDATE users SET password_hash = :hash WHERE id = :id");
        $upd->execute([':hash' => $newHash, ':id' => $userId]);

        jsonResponse(true, 'Doi mat khau thanh cong');

    } catch (PDOException $e) {
        error_log("Profile Change Password Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi doi mat khau');
    }
}

function ensureUserSettings($pdo, $userId)
{
    $stmt = $pdo->prepare("SELECT id FROM user_settings WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $userId]);
    if (!$stmt->fetch()) {
        $insert = $pdo->prepare("
            INSERT INTO user_settings (user_id, default_currency, monthly_budget_limit, notify_email, notify_push)
            VALUES (:user_id, 'VND', NULL, 1, 1)
        ");
        $insert->execute([':user_id' => $userId]);
    }
}

function handleProfileGetSettings($pdo, $userId)
{
    try {
        ensureUserSettings($pdo, $userId);

        $stmt = $pdo->prepare("
            SELECT default_currency, monthly_budget_limit, notify_email, notify_push
            FROM user_settings
            WHERE user_id = :user_id
        ");
        $stmt->execute([':user_id' => $userId]);
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);

        $settings['monthly_budget_limit'] = $settings['monthly_budget_limit'] !== null
            ? (float) $settings['monthly_budget_limit'] : null;
        $settings['notify_email'] = (int) $settings['notify_email'];
        $settings['notify_push'] = (int) $settings['notify_push'];

        jsonResponse(true, 'Success', $settings);

    } catch (PDOException $e) {
        error_log("Profile Get Settings Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi tai cai dat');
    }
}

function handleProfileUpdateSettings($pdo, $userId)
{
    try {
        $currency = strtoupper(trim($_POST['default_currency'] ?? 'VND'));
        $limit = isset($_POST['monthly_budget_limit']) && $_POST['monthly_budget_limit'] !== ''
            ? (float) $_POST['monthly_budget_limit']
            : null;
        $notifyEmail = isset($_POST['notify_email']) ? (int) $_POST['notify_email'] : 0;
        $notifyPush = isset($_POST['notify_push']) ? (int) $_POST['notify_push'] : 0;

        ensureUserSettings($pdo, $userId);

        $stmt = $pdo->prepare("
            UPDATE user_settings
            SET default_currency = :currency,
                monthly_budget_limit = :limit,
                notify_email = :notify_email,
                notify_push = :notify_push
            WHERE user_id = :user_id
        ");
        $stmt->execute([
            ':currency' => $currency !== '' ? $currency : 'VND',
            ':limit' => $limit,
            ':notify_email' => $notifyEmail ? 1 : 0,
            ':notify_push' => $notifyPush ? 1 : 0,
            ':user_id' => $userId
        ]);

        jsonResponse(true, 'Cap nhat cai dat thanh cong');

    } catch (PDOException $e) {
        error_log("Profile Update Settings Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi cap nhat cai dat');
    }
}

function handleProfileOverview($pdo, $userId)
{
    try {
        $stmtTransactions = $pdo->prepare("SELECT COUNT(*) FROM transactions WHERE user_id = :user_id");
        $stmtTransactions->execute([':user_id' => $userId]);
        $transactions = (int) $stmtTransactions->fetchColumn();

        $stmtCategories = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE user_id = :user_id");
        $stmtCategories->execute([':user_id' => $userId]);
        $categories = (int) $stmtCategories->fetchColumn();

        $stmtExpense = $pdo->prepare("
            SELECT COALESCE(SUM(amount), 0)
            FROM transactions
            WHERE user_id = :user_id AND type = 'EXPENSE'
        ");
        $stmtExpense->execute([':user_id' => $userId]);
        $totalExpense = (float) $stmtExpense->fetchColumn();

        ensureUserSettings($pdo, $userId);
        $stmtUser = $pdo->prepare("
            SELECT 
                u.created_at, 
                u.status,
                u.last_login_at,
                COALESCE(us.default_currency, 'VND') AS default_currency
            FROM users u
            LEFT JOIN user_settings us ON us.user_id = u.id
            WHERE u.id = :id
            LIMIT 1
        ");
        $stmtUser->execute([':id' => $userId]);
        $userRow = $stmtUser->fetch(PDO::FETCH_ASSOC);

        $data = [
            'transactions' => $transactions,
            'categories' => $categories,
            'total_expense' => $totalExpense,
            'joined_at' => $userRow['created_at'] ?? null,
            'status' => $userRow['status'] ?? 'ACTIVE',
            'last_login_at' => $userRow['last_login_at'] ?? null,
            'default_currency' => $userRow['default_currency'] ?? 'VND'
        ];

        jsonResponse(true, 'Success', $data);

    } catch (PDOException $e) {
        error_log('Profile Overview Error: ' . $e->getMessage());
        jsonResponse(false, 'Loi khi tai thong tin tai khoan');
    }
}

// ============================================
// PROFILE HANDLERS (MODULE 10) - END
// ============================================

// ============================================
// NOTIFICATIONS HANDLERS (MODULE 9) - START
// ============================================

function handleNotificationsList($pdo, $userId)
{
    syncBillNotifications($pdo, $userId);
    try {
        $unreadOnly = isset($_GET['unread_only']) && (int) $_GET['unread_only'] === 1;
        $status = strtolower(trim($_GET['status'] ?? ''));
        $type = trim($_GET['type'] ?? '');
        $date = trim($_GET['date'] ?? '');
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 100;
        if ($limit <= 0) {
            $limit = 10;
        }
        $limit = min($limit, 100);

        $sql = "
            SELECT id, type, title, message, link_url, is_read, read_at, created_at
            FROM notifications
            WHERE user_id = :user_id
        ";
        $params = [':user_id' => $userId];

        if ($unreadOnly) {
            $sql .= " AND is_read = 0";
        }

        if ($status === 'unread') {
            $sql .= " AND is_read = 0";
        } elseif ($status === 'read') {
            $sql .= " AND is_read = 1";
        }

        if ($type !== '') {
            $sql .= " AND type = :type";
            $params[':type'] = $type;
        }

        if ($date !== '') {
            $dateObj = DateTime::createFromFormat('Y-m-d', $date);
            if ($dateObj) {
                $sql .= " AND DATE(created_at) = :created_date";
                $params[':created_date'] = $dateObj->format('Y-m-d');
            }
        }

        $sql .= " ORDER BY created_at DESC LIMIT $limit";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $row['id'] = (int) $row['id'];
            $row['is_read'] = (int) $row['is_read'];
            $row['link_url'] = $row['link_url'] ?? '';
            $row['read_at'] = $row['read_at'] ?? null;
        }

        jsonResponse(true, 'Success', $rows);

    } catch (PDOException $e) {
        error_log("Notifications List Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi tai thong bao');
    }
}

function handleNotificationsUnreadCount($pdo, $userId)
{
    syncBillNotifications($pdo, $userId);
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM notifications WHERE user_id = :user_id AND is_read = 0");
        $stmt->execute([':user_id' => $userId]);
        $count = (int) ($stmt->fetchColumn() ?: 0);

        jsonResponse(true, 'Success', ['unread_count' => $count]);

    } catch (PDOException $e) {
        error_log("Notifications Unread Count Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi dem thong bao chua doc');
    }
}

function handleNotificationsMarkRead($pdo, $userId)
{
    try {
        $id = (int) ($_POST['id'] ?? 0);
        if (empty($id)) {
            jsonResponse(false, 'ID thong bao khong hop le');
        }

        $stmt = $pdo->prepare("
            UPDATE notifications
            SET is_read = 1, read_at = NOW()
            WHERE id = :id AND user_id = :user_id
        ");
        $stmt->execute([':id' => $id, ':user_id' => $userId]);

        if ($stmt->rowCount() === 0) {
            jsonResponse(false, 'Khong tim thay thong bao');
        }

        jsonResponse(true, 'Da danh dau da doc');

    } catch (PDOException $e) {
        error_log("Notifications Mark Read Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi cap nhat thong bao');
    }
}

function handleNotificationsMarkAllRead($pdo, $userId)
{
    try {
        $stmt = $pdo->prepare("
            UPDATE notifications
            SET is_read = 1, read_at = NOW()
            WHERE user_id = :user_id AND is_read = 0
        ");
        $stmt->execute([':user_id' => $userId]);

        jsonResponse(true, 'Da danh dau tat ca da doc');

    } catch (PDOException $e) {
        error_log("Notifications Mark All Read Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi cap nhat thong bao');
    }
}

function handleNotificationsDelete($pdo, $userId)
{
    try {
        $id = (int) ($_POST['id'] ?? 0);
        if (empty($id)) {
            jsonResponse(false, 'ID thong bao khong hop le');
        }

        $stmt = $pdo->prepare("DELETE FROM notifications WHERE id = :id AND user_id = :user_id");
        $stmt->execute([':id' => $id, ':user_id' => $userId]);

        if ($stmt->rowCount() === 0) {
            jsonResponse(false, 'Khong tim thay thong bao');
        }

        jsonResponse(true, 'Xoa thong bao thanh cong');

    } catch (PDOException $e) {
        error_log("Notifications Delete Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi xoa thong bao');
    }
}

function syncBillNotifications(PDO $pdo, int $userId): void
{
    try {
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $upcomingEnd = date('Y-m-d', strtotime('+3 days'));
        $linkBase = '/public/user/bill_calendar.php?highlight=';

        $overdueStmt = $pdo->prepare("
            SELECT id, name, amount, due_date
            FROM bills
            WHERE user_id = :user_id
              AND status IN ('PENDING','OVERDUE')
              AND due_date < :today
        ");
        $overdueStmt->execute([':user_id' => $userId, ':today' => $today]);
        foreach ($overdueStmt->fetchAll(PDO::FETCH_ASSOC) as $bill) {
            $linkUrl = $linkBase . $bill['id'];
            $title = 'Hoa don qua han: ' . $bill['name'];
            $message = sprintf(
                'Hoa don "%s" tri gia %s da qua han tu ngay %s. Vui long thanh toan som.',
                $bill['name'],
                formatMoney((float) $bill['amount']),
                date('d/m/Y', strtotime($bill['due_date']))
            );
            createNotificationIfMissing($pdo, $userId, 'error', $title, $message, $linkUrl);
        }

        $dueTodayStmt = $pdo->prepare("
            SELECT id, name, amount, due_date
            FROM bills
            WHERE user_id = :user_id
              AND status IN ('PENDING','OVERDUE')
              AND due_date = :today
        ");
        $dueTodayStmt->execute([':user_id' => $userId, ':today' => $today]);
        foreach ($dueTodayStmt->fetchAll(PDO::FETCH_ASSOC) as $bill) {
            $linkUrl = $linkBase . $bill['id'];
            $title = 'Hoa don den han hom nay: ' . $bill['name'];
            $message = sprintf(
                'Hoa don "%s" tri gia %s den han vao ngay %s. Hay thanh toan ngay hom nay.',
                $bill['name'],
                formatMoney((float) $bill['amount']),
                date('d/m/Y', strtotime($bill['due_date']))
            );
            createNotificationIfMissing($pdo, $userId, 'warning', $title, $message, $linkUrl);
        }

        $upcomingStmt = $pdo->prepare("
            SELECT id, name, amount, due_date
            FROM bills
            WHERE user_id = :user_id
              AND status IN ('PENDING','OVERDUE')
              AND due_date BETWEEN :tomorrow AND :upcoming_end
        ");
        $upcomingStmt->execute([
            ':user_id' => $userId,
            ':tomorrow' => $tomorrow,
            ':upcoming_end' => $upcomingEnd
        ]);
        foreach ($upcomingStmt->fetchAll(PDO::FETCH_ASSOC) as $bill) {
            $linkUrl = $linkBase . $bill['id'];
            $title = 'Hoa don sap den han: ' . $bill['name'];
            $message = sprintf(
                'Hoa don "%s" tri gia %s se den han vao ngay %s. Ban nen chuan bi thanh toan.',
                $bill['name'],
                formatMoney((float) $bill['amount']),
                date('d/m/Y', strtotime($bill['due_date']))
            );
            createNotificationIfMissing($pdo, $userId, 'reminder', $title, $message, $linkUrl);
        }

    } catch (Throwable $e) {
        error_log('Sync bill notifications error: ' . $e->getMessage());
    }
}

// ============================================
// NOTIFICATIONS HANDLERS (MODULE 9) - END
// ============================================

// ============================================
// BILLS HANDLERS
// ============================================


function handleGetBills($pdo, $userId)
{
    try {
        $status = $_GET['status'] ?? '';
        $dateFrom = $_GET['date_from'] ?? '';
        $dateTo = $_GET['date_to'] ?? '';

        $sql = "
            SELECT b.id, b.name, b.amount, b.due_date, b.paid_date, b.status, b.note,
                   b.category_id, c.name AS category
            FROM bills b
            LEFT JOIN categories c ON b.category_id = c.id
            WHERE b.user_id = :user_id
        ";
        $params = [':user_id' => $userId];

        if (!empty($status)) {
            $sql .= " AND b.status = :status";
            $params[':status'] = strtoupper($status);
        }

        if (!empty($dateFrom)) {
            $sql .= " AND b.due_date >= :date_from";
            $params[':date_from'] = $dateFrom;
        }

        if (!empty($dateTo)) {
            $sql .= " AND b.due_date <= :date_to";
            $params[':date_to'] = $dateTo;
        }

        $sql .= " ORDER BY b.due_date ASC, b.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $bills = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($bills as &$bill) {
            $bill['id'] = (int) $bill['id'];
            $bill['category_id'] = $bill['category_id'] !== null ? (int) $bill['category_id'] : null;
            $bill['amount'] = (float) $bill['amount'];
            $bill['status'] = strtolower($bill['status']);
            $bill['note'] = $bill['note'] ?? '';
            $bill['category'] = $bill['category'] ?? '';
        }

        jsonResponse(true, 'Success', $bills);

    } catch (PDOException $e) {
        error_log("Get Bills Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi tai hoa don');
    }
}

function handleSaveBill($pdo, $userId)
{
    try {
        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $amount = (float) ($_POST['amount'] ?? 0);
        $dueDate = trim($_POST['due_date'] ?? '');
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $note = trim($_POST['note'] ?? '');
        $statusInput = strtoupper(trim($_POST['status'] ?? 'PENDING'));
        $paidDateInput = trim($_POST['paid_date'] ?? '');

        $allowedStatus = ['PENDING', 'PAID', 'OVERDUE', 'CANCELLED'];
        if (!in_array($statusInput, $allowedStatus, true)) {
            $statusInput = 'PENDING';
        }

        if (empty($name) || $amount <= 0 || empty($dueDate)) {
            jsonResponse(false, 'Du lieu khong hop le');
        }

        if ($categoryId > 0) {
            $catStmt = $pdo->prepare("SELECT id FROM categories WHERE id = :id AND user_id = :user_id");
            $catStmt->execute([':id' => $categoryId, ':user_id' => $userId]);
            if (!$catStmt->fetch()) {
                jsonResponse(false, 'Danh muc khong hop le');
            }
        } else {
            $categoryId = null;
        }

        $paidDate = null;
        if ($statusInput === 'PAID') {
            $paidDate = !empty($paidDateInput) ? $paidDateInput : date('Y-m-d');
        }

        if ($id > 0) {
            $checkStmt = $pdo->prepare("SELECT id FROM bills WHERE id = :id AND user_id = :user_id");
            $checkStmt->execute([':id' => $id, ':user_id' => $userId]);
            if (!$checkStmt->fetch()) {
                jsonResponse(false, 'Khong tim thay hoa don');
            }

            $stmt = $pdo->prepare("
                UPDATE bills
                SET name = :name,
                    amount = :amount,
                    due_date = :due_date,
                    paid_date = :paid_date,
                    status = :status,
                    note = :note,
                    category_id = :category_id
                WHERE id = :id AND user_id = :user_id
            ");
            $stmt->execute([
                ':name' => $name,
                ':amount' => $amount,
                ':due_date' => $dueDate,
                ':paid_date' => $paidDate,
                ':status' => $statusInput,
                ':note' => $note !== '' ? $note : null,
                ':category_id' => $categoryId,
                ':id' => $id,
                ':user_id' => $userId
            ]);

            jsonResponse(true, 'Cap nhat hoa don thanh cong');

        } else {
            $stmt = $pdo->prepare("
                INSERT INTO bills (user_id, name, amount, due_date, paid_date, status, note, category_id)
                VALUES (:user_id, :name, :amount, :due_date, :paid_date, :status, :note, :category_id)
            ");

            $stmt->execute([
                ':user_id' => $userId,
                ':name' => $name,
                ':amount' => $amount,
                ':due_date' => $dueDate,
                ':paid_date' => $paidDate,
                ':status' => $statusInput,
                ':note' => $note !== '' ? $note : null,
                ':category_id' => $categoryId
            ]);

            jsonResponse(true, 'Them hoa don thanh cong');
        }

    } catch (PDOException $e) {
        error_log("Save Bill Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi luu hoa don');
    }
}

function handleMarkBillPaid($pdo, $userId)
{
    try {
        $id = (int) ($_POST['id'] ?? 0);
        if (empty($id)) {
            jsonResponse(false, 'ID hoa don khong hop le');
        }

        $checkStmt = $pdo->prepare("SELECT id, name, amount FROM bills WHERE id = :id AND user_id = :user_id");
        $checkStmt->execute([':id' => $id, ':user_id' => $userId]);
        $bill = $checkStmt->fetch(PDO::FETCH_ASSOC);
        if (!$bill) {
            jsonResponse(false, 'Khong tim thay hoa don');
        }

        $stmt = $pdo->prepare("
            UPDATE bills
            SET status = 'PAID',
                paid_date = :paid_date
            WHERE id = :id AND user_id = :user_id
        ");
        $stmt->execute([
            ':paid_date' => date('Y-m-d'),
            ':id' => $id,
            ':user_id' => $userId
        ]);

        try {
            $linkUrl = '/public/user/bill_calendar.php?highlight=' . $bill['id'];
            $message = sprintf('Bạn đã thanh toán hóa đơn "%s" với số tiền %s.', $bill['name'], formatMoney((float) $bill['amount']));
            createNotification($pdo, $userId, 'success', 'Đã thanh toán hóa đơn', $message, $linkUrl);

            $markOldNotif = $pdo->prepare("UPDATE notifications SET is_read = 1, read_at = NOW() WHERE user_id = :user_id AND link_url = :link AND is_read = 0");
            $markOldNotif->execute([
                ':user_id' => $userId,
                ':link' => $linkUrl
            ]);
        } catch (Throwable $notifyError) {
            error_log('Bill paid notification error: ' . $notifyError->getMessage());
        }

        jsonResponse(true, 'Da danh dau thanh toan');

    } catch (PDOException $e) {
        error_log("Mark Bill Paid Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi cap nhat thanh toan');
    }
}

function handleDeleteBill($pdo, $userId)
{
    try {
        $id = (int) ($_POST['id'] ?? 0);
        if (empty($id)) {
            jsonResponse(false, 'ID hoa don khong hop le');
        }

        $checkStmt = $pdo->prepare("SELECT id FROM bills WHERE id = :id AND user_id = :user_id");
        $checkStmt->execute([':id' => $id, ':user_id' => $userId]);
        if (!$checkStmt->fetch()) {
            jsonResponse(false, 'Khong tim thay hoa don');
        }

        $stmt = $pdo->prepare("DELETE FROM bills WHERE id = :id AND user_id = :user_id");
        $stmt->execute([':id' => $id, ':user_id' => $userId]);

        jsonResponse(true, 'Xoa hoa don thanh cong');

    } catch (PDOException $e) {
        error_log("Delete Bill Error: " . $e->getMessage());
        jsonResponse(false, 'Loi khi xoa hoa don');
    }
}

// ============================================
// STATISTICS HANDLERS
// ============================================

function handleStatisticsSummary($pdo, $userId)
{
    try {
        $period = $_GET['period'] ?? 'this_month';
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;

        // Calculate date range based on period or custom dates
        if ($period === 'custom' && $startDate && $endDate) {
            $dateFrom = $startDate;
            $dateTo = $endDate;
        } else {
            switch ($period) {
                case 'this_month':
                    $dateFrom = date('Y-m-01');
                    $dateTo = date('Y-m-t');
                    break;
                case 'last_month':
                    $dateFrom = date('Y-m-01', strtotime('last month'));
                    $dateTo = date('Y-m-t', strtotime('last month'));
                    break;
                case '3_months':
                    $dateFrom = date('Y-m-d', strtotime('-3 months'));
                    $dateTo = date('Y-m-d');
                    break;
                case '6_months':
                    $dateFrom = date('Y-m-d', strtotime('-6 months'));
                    $dateTo = date('Y-m-d');
                    break;
                case 'this_year':
                    $dateFrom = date('Y-01-01');
                    $dateTo = date('Y-12-31');
                    break;
                default:
                    $dateFrom = date('Y-m-01');
                    $dateTo = date('Y-m-t');
            }
        }

        // Get current period stats
        $stmt = $pdo->prepare("
            SELECT
                SUM(CASE WHEN type = 'INCOME' THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type = 'EXPENSE' THEN amount ELSE 0 END) as expense,
                COUNT(*) as count
            FROM transactions
            WHERE user_id = :user_id
              AND transaction_date BETWEEN :date_from AND :date_to
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':date_from' => $dateFrom,
            ':date_to' => $dateTo
        ]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);

        $currentIncome = (float) ($current['income'] ?? 0);
        $currentExpense = (float) ($current['expense'] ?? 0);
        $currentCount = (int) ($current['count'] ?? 0);

        // Get previous period for comparison
        $prevIncome = 0;
        $prevExpense = 0;

        if ($period === 'custom' && $startDate && $endDate) {
            // For custom range, compare with same range last year
            $prevFrom = date('Y-m-d', strtotime($startDate . ' -1 year'));
            $prevTo = date('Y-m-d', strtotime($endDate . ' -1 year'));
        } elseif ($period === 'this_month') {
            // Compare with last month
            $prevFrom = date('Y-m-01', strtotime('last month'));
            $prevTo = date('Y-m-t', strtotime('last month'));
        } elseif ($period === 'this_year') {
            // Compare with last year
            $prevFrom = date('Y-01-01', strtotime('-1 year'));
            $prevTo = date('Y-12-31', strtotime('-1 year'));
        } else {
            // For other periods, compare with same period last year
            $prevFrom = date('Y-m-d', strtotime($dateFrom . ' -1 year'));
            $prevTo = date('Y-m-d', strtotime($dateTo . ' -1 year'));
        }

        $prevStmt = $pdo->prepare("
            SELECT
                SUM(CASE WHEN type = 'INCOME' THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type = 'EXPENSE' THEN amount ELSE 0 END) as expense
            FROM transactions
            WHERE user_id = :user_id
              AND transaction_date BETWEEN :date_from AND :date_to
        ");
        $prevStmt->execute([
            ':user_id' => $userId,
            ':date_from' => $prevFrom,
            ':date_to' => $prevTo
        ]);
        $prev = $prevStmt->fetch(PDO::FETCH_ASSOC);

        $prevIncome = (float) ($prev['income'] ?? 0);
        $prevExpense = (float) ($prev['expense'] ?? 0);

        // Calculate percentage changes
        $incomeChange = $prevIncome > 0 ? (($currentIncome - $prevIncome) / $prevIncome) * 100 : 0;
        $expenseChange = $prevExpense > 0 ? (($currentExpense - $prevExpense) / $prevExpense) * 100 : 0;

        jsonResponse(true, 'Success', [
            'income' => $currentIncome,
            'expense' => $currentExpense,
            'balance' => $currentIncome - $currentExpense,
            'count' => $currentCount,
            'income_change' => round($incomeChange, 1),
            'expense_change' => round($expenseChange, 1),
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ]);

    } catch (PDOException $e) {
        error_log("Statistics Summary Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi tải thống kê tổng quan');
    }
}

function handleStatisticsCharts($pdo, $userId)
{
    try {
        $period = $_GET['period'] ?? 'this_month';
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;

        // Calculate date range based on period or custom dates
        if ($period === 'custom' && $startDate && $endDate) {
            $dateFrom = $startDate;
            $dateTo = $endDate;
        } else {
            switch ($period) {
                case 'this_month':
                    $dateFrom = date('Y-m-01');
                    $dateTo = date('Y-m-t');
                    break;
                case 'last_month':
                    $dateFrom = date('Y-m-01', strtotime('last month'));
                    $dateTo = date('Y-m-t', strtotime('last month'));
                    break;
                case '3_months':
                    $dateFrom = date('Y-m-d', strtotime('-3 months'));
                    $dateTo = date('Y-m-d');
                    break;
                case '6_months':
                    $dateFrom = date('Y-m-d', strtotime('-6 months'));
                    $dateTo = date('Y-m-d');
                    break;
                case 'this_year':
                    $dateFrom = date('Y-01-01');
                    $dateTo = date('Y-12-31');
                    break;
                default:
                    $dateFrom = date('Y-m-01');
                    $dateTo = date('Y-m-t');
            }
        }

        // Time series data (line chart)
        $timeStmt = $pdo->prepare("
            SELECT 
                DATE(transaction_date) as date,
                SUM(CASE WHEN type = 'INCOME' THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type = 'EXPENSE' THEN amount ELSE 0 END) as expense
            FROM transactions
            WHERE user_id = :user_id 
              AND transaction_date BETWEEN :date_from AND :date_to
            GROUP BY DATE(transaction_date)
            ORDER BY DATE(transaction_date) ASC
        ");
        $timeStmt->execute([
            ':user_id' => $userId,
            ':date_from' => $dateFrom,
            ':date_to' => $dateTo
        ]);
        $timeData = $timeStmt->fetchAll(PDO::FETCH_ASSOC);

        $timeLabels = [];
        $timeIncome = [];
        $timeExpense = [];

        foreach ($timeData as $row) {
            $timeLabels[] = date('d/m', strtotime($row['date']));
            $timeIncome[] = (float) $row['income'];
            $timeExpense[] = (float) $row['expense'];
        }

        // Category distribution (pie chart)
        $catStmt = $pdo->prepare("
            SELECT c.name, c.color, SUM(t.amount) as amount
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = :user_id AND t.type = 'EXPENSE'
              AND t.transaction_date BETWEEN :date_from AND :date_to
            GROUP BY c.id, c.name, c.color
            ORDER BY SUM(t.amount) DESC
        ");
        $catStmt->execute([
            ':user_id' => $userId,
            ':date_from' => $dateFrom,
            ':date_to' => $dateTo
        ]);
        $catData = $catStmt->fetchAll(PDO::FETCH_ASSOC);

        $catLabels = [];
        $catValues = [];
        $catColors = [];

        foreach ($catData as $row) {
            $catLabels[] = $row['name'];
            $catValues[] = (float) $row['amount'];
            $catColors[] = $row['color'];
        }

        // Trend chart (expense by month/week)
        $trendLabels = [];
        $trendValues = [];

        if ($period === 'this_year' || $period === '6_months' || $period === '3_months') {
            // Monthly trend
            $trendStmt = $pdo->prepare("
                SELECT 
                    DATE_FORMAT(transaction_date, '%Y-%m') as period,
                    SUM(CASE WHEN type = 'EXPENSE' THEN amount ELSE 0 END) as expense
                FROM transactions
                WHERE user_id = :user_id 
                  AND transaction_date BETWEEN :date_from AND :date_to
                  AND type = 'EXPENSE'
                GROUP BY DATE_FORMAT(transaction_date, '%Y-%m')
                ORDER BY period ASC
            ");
            $trendStmt->execute([
                ':user_id' => $userId,
                ':date_from' => $dateFrom,
                ':date_to' => $dateTo
            ]);
            $trendData = $trendStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($trendData as $row) {
                $trendLabels[] = date('m/Y', strtotime($row['period'] . '-01'));
                $trendValues[] = (float) $row['expense'];
            }
        } else {
            // Weekly trend for shorter periods
            $trendStmt = $pdo->prepare("
                SELECT 
                    YEARWEEK(transaction_date) as week,
                    SUM(CASE WHEN type = 'EXPENSE' THEN amount ELSE 0 END) as expense
                FROM transactions
                WHERE user_id = :user_id 
                  AND transaction_date BETWEEN :date_from AND :date_to
                  AND type = 'EXPENSE'
                GROUP BY YEARWEEK(transaction_date)
                ORDER BY week ASC
            ");
            $trendStmt->execute([
                ':user_id' => $userId,
                ':date_from' => $dateFrom,
                ':date_to' => $dateTo
            ]);
            $trendData = $trendStmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($trendData as $row) {
                $trendLabels[] = 'Tuần ' . substr($row['week'], -2);
                $trendValues[] = (float) $row['expense'];
            }
        }

        // Income sources (bar chart)
        $incomeStmt = $pdo->prepare("
            SELECT c.name, SUM(t.amount) as amount
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = :user_id AND t.type = 'INCOME'
              AND t.transaction_date BETWEEN :date_from AND :date_to
            GROUP BY c.id, c.name
            ORDER BY SUM(t.amount) DESC
        ");
        $incomeStmt->execute([
            ':user_id' => $userId,
            ':date_from' => $dateFrom,
            ':date_to' => $dateTo
        ]);
        $incomeData = $incomeStmt->fetchAll(PDO::FETCH_ASSOC);

        $incomeLabels = [];
        $incomeValues = [];

        foreach ($incomeData as $row) {
            $incomeLabels[] = $row['name'];
            $incomeValues[] = (float) $row['amount'];
        }

        jsonResponse(true, 'Success', [
            'time' => [
                'labels' => $timeLabels,
                'income' => $timeIncome,
                'expense' => $timeExpense
            ],
            'categories' => [
                'labels' => $catLabels,
                'values' => $catValues,
                'colors' => $catColors
            ],
            'trend' => [
                'labels' => $trendLabels,
                'values' => $trendValues
            ],
            'income' => [
                'labels' => $incomeLabels,
                'values' => $incomeValues
            ]
        ]);

    } catch (PDOException $e) {
        error_log("Statistics Charts Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi tải dữ liệu biểu đồ');
    }
}

function handleTopCategories($pdo, $userId)
{
    try {
        $period = $_GET['period'] ?? 'this_month';
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;

        // Calculate date range based on period or custom dates
        if ($period === 'custom' && $startDate && $endDate) {
            $dateFrom = $startDate;
            $dateTo = $endDate;
        } else {
            switch ($period) {
                case 'this_month':
                    $dateFrom = date('Y-m-01');
                    $dateTo = date('Y-m-t');
                    break;
                case 'last_month':
                    $dateFrom = date('Y-m-01', strtotime('last month'));
                    $dateTo = date('Y-m-t', strtotime('last month'));
                    break;
                case '3_months':
                    $dateFrom = date('Y-m-d', strtotime('-3 months'));
                    $dateTo = date('Y-m-d');
                    break;
                case '6_months':
                    $dateFrom = date('Y-m-d', strtotime('-6 months'));
                    $dateTo = date('Y-m-d');
                    break;
                case 'this_year':
                    $dateFrom = date('Y-01-01');
                    $dateTo = date('Y-12-31');
                    break;
                default:
                    $dateFrom = date('Y-m-01');
                    $dateTo = date('Y-m-t');
            }
        }

        // Get total expense for percentage calculation
        $totalStmt = $pdo->prepare("
            SELECT SUM(amount) as total
            FROM transactions
            WHERE user_id = :user_id AND type = 'EXPENSE'
              AND transaction_date BETWEEN :date_from AND :date_to
        ");
        $totalStmt->execute([
            ':user_id' => $userId,
            ':date_from' => $dateFrom,
            ':date_to' => $dateTo
        ]);
        $totalRow = $totalStmt->fetch(PDO::FETCH_ASSOC);
        $totalExpense = (float) ($totalRow['total'] ?? 0);

        // Get top categories
        $stmt = $pdo->prepare("
            SELECT 
                c.name,
                c.icon,
                c.color,
                COUNT(t.id) as count,
                SUM(t.amount) as amount
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = :user_id AND t.type = 'EXPENSE'
              AND t.transaction_date BETWEEN :date_from AND :date_to
            GROUP BY c.id, c.name, c.icon, c.color
            ORDER BY SUM(t.amount) DESC
            LIMIT 10
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':date_from' => $dateFrom,
            ':date_to' => $dateTo
        ]);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($categories as $cat) {
            $amount = (float) $cat['amount'];
            $percentage = $totalExpense > 0 ? ($amount / $totalExpense) * 100 : 0;

            $result[] = [
                'name' => $cat['name'],
                'icon' => $cat['icon'],
                'color' => $cat['color'],
                'count' => (int) $cat['count'],
                'amount' => $amount,
                'percentage' => round($percentage, 1)
            ];
        }

        jsonResponse(true, 'Success', $result);

    } catch (PDOException $e) {
        error_log("Top Categories Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi tải danh mục hàng đầu');
    }
}

// ============================================
// DASHBOARD & STATS HANDLERS
// ============================================

function handleDashboardStats($pdo, $userId)
{
    try {
        $stmt = $pdo->prepare("
            SELECT 
                SUM(CASE WHEN type = 'INCOME' THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type = 'EXPENSE' THEN amount ELSE 0 END) as expense,
                COUNT(*) as transaction_count
            FROM transactions
            WHERE user_id = :user_id
        ");

        $stmt->execute([':user_id' => $userId]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        $income = (float) ($stats['income'] ?? 0);
        $expense = (float) ($stats['expense'] ?? 0);

        jsonResponse(true, 'Success', [
            'income' => $income,
            'expense' => $expense,
            'balance' => $income - $expense,
            'transaction_count' => (int) $stats['transaction_count']
        ]);

    } catch (PDOException $e) {
        error_log("Dashboard Stats Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi tải thống kê');
    }
}

function handleRecentTransactions($pdo, $userId)
{
    try {
        $stmt = $pdo->prepare("
            SELECT t.id, t.amount, t.type, t.transaction_date as date, t.note,
                   c.name as category_name,
                   c.icon as category_icon,
                   c.color as category_color
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = :user_id
            ORDER BY t.transaction_date DESC, t.created_at DESC
            LIMIT 5
        ");

        $stmt->execute([':user_id' => $userId]);
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($transactions as &$trans) {
            $trans['type'] = strtolower($trans['type']);
            $trans['amount'] = (float) $trans['amount'];
        }

        jsonResponse(true, 'Success', $transactions);

    } catch (PDOException $e) {
        error_log("Recent Transactions Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi tải giao dịch gần đây');
    }
}

function handleChartData($pdo, $userId)
{
    try {
        // Pie Chart Data
        $pieStmt = $pdo->prepare("
            SELECT c.name, c.color, SUM(t.amount) as amount
            FROM transactions t
            INNER JOIN categories c ON t.category_id = c.id
            WHERE t.user_id = :user_id AND t.type = 'EXPENSE'
            GROUP BY c.id, c.name, c.color
        ");
        $pieStmt->execute([':user_id' => $userId]);
        $pieData = $pieStmt->fetchAll(PDO::FETCH_ASSOC);

        $expenseByCategory = [];
        foreach ($pieData as $row) {
            $expenseByCategory[$row['name']] = [
                'amount' => (float) $row['amount'],
                'color' => $row['color']
            ];
        }

        // Line Chart Data - Last 30 days
        $lineStmt = $pdo->prepare("
            SELECT 
                DATE(t.transaction_date) as date,
                SUM(CASE WHEN t.type = 'INCOME' THEN t.amount ELSE 0 END) as income,
                SUM(CASE WHEN t.type = 'EXPENSE' THEN t.amount ELSE 0 END) as expense
            FROM transactions t
            WHERE t.user_id = :user_id 
              AND t.transaction_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(t.transaction_date)
            ORDER BY DATE(t.transaction_date) ASC
        ");
        $lineStmt->execute([':user_id' => $userId]);
        $lineData = $lineStmt->fetchAll(PDO::FETCH_ASSOC);

        $labels = [];
        $income = [];
        $expense = [];

        foreach ($lineData as $row) {
            $labels[] = date('d/m', strtotime($row['date']));
            $income[] = (float) $row['income'];
            $expense[] = (float) $row['expense'];
        }

        jsonResponse(true, 'Success', [
            'pie' => $expenseByCategory,
            'line' => [
                'labels' => $labels,
                'income' => $income,
                'expense' => $expense
            ]
        ]);

    } catch (PDOException $e) {
        error_log("Chart Data Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi tải dữ liệu biểu đồ');
    }
}

// ============================================
// EXPORT HANDLERS
// ============================================

function handleExportStatistics($pdo, $userId)
{
    try {
        $period = $_GET['period'] ?? 'this_month';
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;
        $format = $_GET['format'] ?? 'csv'; // csv, json, pdf

        // Calculate date range based on period or custom dates
        if ($period === 'custom' && $startDate && $endDate) {
            $dateFrom = $startDate;
            $dateTo = $endDate;
        } else {
            switch ($period) {
                case 'this_month':
                    $dateFrom = date('Y-m-01');
                    $dateTo = date('Y-m-t');
                    break;
                case 'last_month':
                    $dateFrom = date('Y-m-01', strtotime('last month'));
                    $dateTo = date('Y-m-t', strtotime('last month'));
                    break;
                case '3_months':
                    $dateFrom = date('Y-m-d', strtotime('-3 months'));
                    $dateTo = date('Y-m-d');
                    break;
                case '6_months':
                    $dateFrom = date('Y-m-d', strtotime('-6 months'));
                    $dateTo = date('Y-m-d');
                    break;
                case 'this_year':
                    $dateFrom = date('Y-01-01');
                    $dateTo = date('Y-12-31');
                    break;
                default:
                    $dateFrom = date('Y-m-01');
                    $dateTo = date('Y-m-t');
            }
        }

        if ($format === 'csv') {
            // Get transactions data
            $stmt = $pdo->prepare("
                SELECT
                    t.transaction_date,
                    t.type,
                    t.amount,
                    t.note,
                    c.name as category_name,
                    c.type as category_type
                FROM transactions t
                INNER JOIN categories c ON t.category_id = c.id
                WHERE t.user_id = :user_id
                  AND t.transaction_date BETWEEN :date_from AND :date_to
                ORDER BY t.transaction_date DESC, t.created_at DESC
            ");
            $stmt->execute([
                ':user_id' => $userId,
                ':date_from' => $dateFrom,
                ':date_to' => $dateTo
            ]);
            $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Generate CSV
            $csvData = "Ngày,Giao dịch,Loại,Số tiền,Danh mục,Ghi chú\n";

            foreach ($transactions as $trans) {
                $type = $trans['type'] === 'INCOME' ? 'Thu nhập' : 'Chi tiêu';
                $amount = number_format($trans['amount'], 0, ',', '.');
                $note = str_replace('"', '""', $trans['note'] ?? ''); // Escape quotes for CSV

                $csvData .= sprintf(
                    "\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n",
                    $trans['transaction_date'],
                    $trans['type'] === 'INCOME' ? 'Thu nhập' : 'Chi tiêu',
                    $trans['category_type'] === 'INCOME' ? 'Thu nhập' : 'Chi tiêu',
                    $amount,
                    $trans['category_name'],
                    $note
                );
            }

            // Set headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="bao-cao-thong-ke-' . date('Y-m-d') . '.csv"');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');

            echo "\xEF\xBB\xBF"; // UTF-8 BOM for Excel
            echo $csvData;
            exit;

        } elseif ($format === 'json') {
            // Get comprehensive statistics data
            $stats = [];

            // Summary stats
            $summaryStmt = $pdo->prepare("
                SELECT
                    SUM(CASE WHEN type = 'INCOME' THEN amount ELSE 0 END) as income,
                    SUM(CASE WHEN type = 'EXPENSE' THEN amount ELSE 0 END) as expense,
                    COUNT(*) as count
                FROM transactions
                WHERE user_id = :user_id
                  AND transaction_date BETWEEN :date_from AND :date_to
            ");
            $summaryStmt->execute([
                ':user_id' => $userId,
                ':date_from' => $dateFrom,
                ':date_to' => $dateTo
            ]);
            $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);

            $stats['summary'] = [
                'period' => $period,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'total_income' => (float) ($summary['income'] ?? 0),
                'total_expense' => (float) ($summary['expense'] ?? 0),
                'balance' => (float) (($summary['income'] ?? 0) - ($summary['expense'] ?? 0)),
                'transaction_count' => (int) ($summary['count'] ?? 0)
            ];

            // Category breakdown
            $catStmt = $pdo->prepare("
                SELECT
                    c.name,
                    c.type,
                    SUM(t.amount) as amount,
                    COUNT(t.id) as count
                FROM transactions t
                INNER JOIN categories c ON t.category_id = c.id
                WHERE t.user_id = :user_id
                  AND t.transaction_date BETWEEN :date_from AND :date_to
                GROUP BY c.id, c.name, c.type
                ORDER BY SUM(t.amount) DESC
            ");
            $catStmt->execute([
                ':user_id' => $userId,
                ':date_from' => $dateFrom,
                ':date_to' => $dateTo
            ]);
            $stats['categories'] = $catStmt->fetchAll(PDO::FETCH_ASSOC);

            // Daily breakdown
            $dailyStmt = $pdo->prepare("
                SELECT
                    transaction_date,
                    SUM(CASE WHEN type = 'INCOME' THEN amount ELSE 0 END) as income,
                    SUM(CASE WHEN type = 'EXPENSE' THEN amount ELSE 0 END) as expense
                FROM transactions
                WHERE user_id = :user_id
                  AND transaction_date BETWEEN :date_from AND :date_to
                GROUP BY transaction_date
                ORDER BY transaction_date ASC
            ");
            $dailyStmt->execute([
                ':user_id' => $userId,
                ':date_from' => $dateFrom,
                ':date_to' => $dateTo
            ]);
            $stats['daily'] = $dailyStmt->fetchAll(PDO::FETCH_ASSOC);

            // Set headers for JSON download
            header('Content-Type: application/json; charset=utf-8');
            header('Content-Disposition: attachment; filename="bao-cao-thong-ke-' . date('Y-m-d') . '.json"');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');

            echo json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;

        } else {
            jsonResponse(false, 'Định dạng không được hỗ trợ');
        }

    } catch (PDOException $e) {
        error_log("Export Statistics Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi xuất báo cáo');
    }
}

// ============================================
// SUPPORT TICKETS HANDLERS
// ============================================

function handleCreateTicket($pdo, $userId)
{
    try {
        $subject = trim($_POST['subject'] ?? '');
        $category = trim($_POST['category'] ?? 'question');
        $message = trim($_POST['message'] ?? '');

        if (empty($subject)) {
            jsonResponse(false, 'Tiêu đề không được để trống');
        }
        if (empty($message)) {
            jsonResponse(false, 'Nội dung không được để trống');
        }
        if (!in_array($category, ['bug', 'feature', 'question', 'other'], true)) {
            $category = 'question';
        }

        $pdo->beginTransaction();
        try {
            // Insert ticket
            $stmtTicket = $pdo->prepare("
                INSERT INTO support_tickets (user_id, subject, category, status)
                VALUES (:user_id, :subject, :category, 'open')
            ");
            $stmtTicket->execute([
                ':user_id' => $userId,
                ':subject' => $subject,
                ':category' => $category
            ]);

            $ticketId = $pdo->lastInsertId();

            // Insert first message
            $stmtMessage = $pdo->prepare("
                INSERT INTO support_messages (ticket_id, sender_id, sender_type, message)
                VALUES (:ticket_id, :sender_id, 'user', :message)
            ");
            $stmtMessage->execute([
                ':ticket_id' => $ticketId,
                ':sender_id' => $userId,
                ':message' => $message
            ]);

            $pdo->commit();
            jsonResponse(true, 'Đã gửi yêu cầu hỗ trợ thành công', ['ticket_id' => $ticketId]);

        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }

    } catch (PDOException $e) {
        error_log("Create Ticket Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi tạo yêu cầu hỗ trợ');
    }
}

function handleGetMyTickets($pdo, $userId)
{
    try {
        $status = trim($_GET['status'] ?? $_POST['status'] ?? '');
        $category = trim($_GET['category'] ?? $_POST['category'] ?? '');
        $priority = trim($_GET['priority'] ?? $_POST['priority'] ?? '');

        $sql = "
            SELECT
                t.id,
                t.subject,
                t.category,
                t.status,
                t.priority,
                t.is_read,
                t.created_at,
                t.updated_at,
                m.message
            FROM support_tickets t
            LEFT JOIN support_messages m ON m.ticket_id = t.id
                AND m.id = (SELECT MIN(id) FROM support_messages WHERE ticket_id = t.id AND sender_type = 'user')
            WHERE t.user_id = :user_id
        ";

        $params = [':user_id' => $userId];

        if ($status !== '' && in_array($status, ['open', 'answered', 'closed'], true)) {
            $sql .= " AND t.status = :status";
            $params[':status'] = $status;
        }

        if ($category !== '' && in_array($category, ['bug', 'feature', 'question', 'other'], true)) {
            $sql .= " AND t.category = :category";
            $params[':category'] = $category;
        }

        if ($priority !== '' && in_array($priority, ['low', 'medium', 'high'], true)) {
            $sql .= " AND t.priority = :priority";
            $params[':priority'] = $priority;
        }

        $sql .= " ORDER BY t.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        jsonResponse(true, 'Success', $tickets);

    } catch (PDOException $e) {
        error_log("Get My Tickets Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi khi tải danh sách yêu cầu');
    }
}

