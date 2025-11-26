<?php
require_once '../../config.php';
require_once '../../functions.php'; // Reuse helper functions if needed

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// --- Auth ---
if ($action === 'admin_login') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_id'] = 1;
        $_SESSION['admin_name'] = 'Super Admin';
        jsonResponse(true, 'Đăng nhập thành công!');
    } else {
        jsonResponse(false, 'Sai thông tin đăng nhập.');
    }
}

// Check Auth for other actions
if (!isset($_SESSION['admin_id'])) {
    jsonResponse(false, 'Unauthorized');
}

// --- Dashboard Stats ---
if ($action === 'dashboard_stats') {
    jsonResponse(true, 'Success', [
        'total_users' => 150,
        'total_transactions' => 1250,
        'total_expense' => 45000000,
        'total_income' => 60000000,
        'total_categories' => 12
    ]);
}

// --- Users ---
elseif ($action === 'get_users') {
    // Mock Users
    $users = [
        ['id' => 1, 'name' => 'Nguyễn Văn A', 'email' => 'user@example.com', 'transactions' => 50, 'expense' => 5000000, 'status' => 'active', 'created_at' => '2023-01-15'],
        ['id' => 2, 'name' => 'Trần Thị B', 'email' => 'b@example.com', 'transactions' => 30, 'expense' => 2000000, 'status' => 'active', 'created_at' => '2023-02-20'],
        ['id' => 3, 'name' => 'Lê Văn C', 'email' => 'c@example.com', 'transactions' => 0, 'expense' => 0, 'status' => 'banned', 'created_at' => '2023-10-01'],
    ];
    jsonResponse(true, 'Success', $users);
}

// --- Categories ---
elseif ($action === 'get_categories') {
    // Mock Categories (Global)
    $categories = [
        ['id' => 1, 'name' => 'Ăn uống', 'type' => 'expense', 'users_count' => 120, 'created_at' => '2023-01-01'],
        ['id' => 2, 'name' => 'Di chuyển', 'type' => 'expense', 'users_count' => 100, 'created_at' => '2023-01-01'],
        ['id' => 3, 'name' => 'Lương', 'type' => 'income', 'users_count' => 150, 'created_at' => '2023-01-01'],
    ];
    jsonResponse(true, 'Success', $categories);
}

// --- Transactions ---
elseif ($action === 'get_transactions') {
    // Mock Transactions
    $transactions = [
        ['id' => 101, 'user' => 'Nguyễn Văn A', 'type' => 'expense', 'category' => 'Ăn uống', 'amount' => 50000, 'date' => '2023-10-26', 'status' => 'completed'],
        ['id' => 102, 'user' => 'Trần Thị B', 'type' => 'income', 'category' => 'Lương', 'amount' => 10000000, 'date' => '2023-10-25', 'status' => 'completed'],
        ['id' => 103, 'user' => 'Nguyễn Văn A', 'type' => 'expense', 'category' => 'Mua sắm', 'amount' => 200000, 'date' => '2023-10-24', 'status' => 'flagged'],
    ];
    jsonResponse(true, 'Success', $transactions);
}

// --- Logs ---
elseif ($action === 'get_logs') {
    $logs = [
        ['id' => 1, 'time' => '2023-10-26 10:00:00', 'user' => 'admin', 'action' => 'Login', 'ip' => '127.0.0.1', 'note' => 'Đăng nhập thành công'],
        ['id' => 2, 'time' => '2023-10-26 10:05:00', 'user' => 'admin', 'action' => 'Delete User', 'ip' => '127.0.0.1', 'note' => 'Xóa user ID 5'],
    ];
    jsonResponse(true, 'Success', $logs);
}

// --- Charts ---
elseif ($action === 'chart_data') {
    jsonResponse(true, 'Success', [
        'line' => [
            'labels' => ['01/10', '05/10', '10/10', '15/10', '20/10', '25/10'],
            'data' => [500000, 1200000, 800000, 2000000, 1500000, 3000000]
        ],
        'pie' => [
            'income' => 60,
            'expense' => 40
        ],
        'bar' => [
            'labels' => ['Ăn uống', 'Di chuyển', 'Mua sắm', 'Hóa đơn'],
            'data' => [40, 20, 25, 15]
        ]
    ]);
} else {
    jsonResponse(false, 'Invalid action');
}
?>