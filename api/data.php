<?php
require_once '../../config.php';
require_once '../../functions.php';

header('Content-Type: application/json');

// Mock Data Store (In a real app, this would be DB queries)
$categories = [
    ['id' => 1, 'name' => 'Ăn uống', 'type' => 'expense', 'icon' => 'fa-utensils', 'color' => '#EF4444', 'limit' => 5000000],
    ['id' => 2, 'name' => 'Di chuyển', 'type' => 'expense', 'icon' => 'fa-car', 'color' => '#F59E0B', 'limit' => 2000000],
    ['id' => 3, 'name' => 'Lương', 'type' => 'income', 'icon' => 'fa-money-bill-wave', 'color' => '#10B981', 'limit' => 0],
    ['id' => 4, 'name' => 'Thưởng', 'type' => 'income', 'icon' => 'fa-gift', 'color' => '#8B5CF6', 'limit' => 0],
    ['id' => 5, 'name' => 'Mua sắm', 'type' => 'expense', 'icon' => 'fa-shopping-bag', 'color' => '#EC4899', 'limit' => 3000000],
];

$transactions = [
    ['id' => 1, 'amount' => 50000, 'date' => '2023-10-25', 'note' => 'Ăn trưa', 'category_id' => 1, 'type' => 'expense'],
    ['id' => 2, 'amount' => 15000000, 'date' => '2023-10-01', 'note' => 'Lương tháng 10', 'category_id' => 3, 'type' => 'income'],
    ['id' => 3, 'amount' => 30000, 'date' => '2023-10-26', 'note' => 'Grab đi làm', 'category_id' => 2, 'type' => 'expense'],
    ['id' => 4, 'amount' => 250000, 'date' => '2023-10-24', 'note' => 'Mua áo thun', 'category_id' => 5, 'type' => 'expense'],
    ['id' => 5, 'amount' => 1000000, 'date' => '2023-10-20', 'note' => 'Thưởng dự án', 'category_id' => 4, 'type' => 'income'],
];

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'dashboard_stats') {
    // Calculate stats
    $income = 0;
    $expense = 0;
    foreach ($transactions as $t) {
        if ($t['type'] === 'income')
            $income += $t['amount'];
        else
            $expense += $t['amount'];
    }

    jsonResponse(true, 'Success', [
        'income' => $income,
        'expense' => $expense,
        'balance' => $income - $expense,
        'transaction_count' => count($transactions)
    ]);

} elseif ($action === 'recent_transactions') {
    // Enrich transaction data with category info
    $enriched = [];
    foreach ($transactions as $t) {
        foreach ($categories as $c) {
            if ($t['category_id'] == $c['id']) {
                $t['category_name'] = $c['name'];
                $t['category_icon'] = $c['icon'];
                $t['category_color'] = $c['color'];
                break;
            }
        }
        $enriched[] = $t;
    }
    // Sort by date desc (mock)
    usort($enriched, function ($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });

    jsonResponse(true, 'Success', array_slice($enriched, 0, 5));

} elseif ($action === 'chart_data') {
    // Prepare data for charts
    $expense_by_category = [];
    foreach ($transactions as $t) {
        if ($t['type'] === 'expense') {
            $cat_name = 'Khác';
            $cat_color = '#ccc';
            foreach ($categories as $c) {
                if ($t['category_id'] == $c['id']) {
                    $cat_name = $c['name'];
                    $cat_color = $c['color'];
                    break;
                }
            }
            if (!isset($expense_by_category[$cat_name])) {
                $expense_by_category[$cat_name] = ['amount' => 0, 'color' => $cat_color];
            }
            $expense_by_category[$cat_name]['amount'] += $t['amount'];
        }
    }

    jsonResponse(true, 'Success', [
        'pie' => $expense_by_category,
        'line' => [
            'labels' => ['01/10', '05/10', '10/10', '15/10', '20/10', '25/10', '30/10'],
            'income' => [15000000, 0, 0, 0, 1000000, 0, 0],
            'expense' => [200000, 500000, 300000, 150000, 800000, 330000, 0]
        ]
    ]);

} elseif ($action === 'get_categories') {
    jsonResponse(true, 'Success', $categories);

} elseif ($action === 'get_transactions') {
    // Enrich transaction data with category info
    $enriched = [];
    foreach ($transactions as $t) {
        foreach ($categories as $c) {
            if ($t['category_id'] == $c['id']) {
                $t['category_name'] = $c['name'];
                $t['category_icon'] = $c['icon'];
                $t['category_color'] = $c['color'];
                break;
            }
        }
        $enriched[] = $t;
    }
    jsonResponse(true, 'Success', $enriched);

} elseif ($action === 'add_category') {
    jsonResponse(true, 'Thêm danh mục thành công!');
} elseif ($action === 'update_category') {
    jsonResponse(true, 'Cập nhật danh mục thành công!');
} elseif ($action === 'delete_category') {
    jsonResponse(true, 'Xóa danh mục thành công!');
} elseif ($action === 'add_transaction') {
    jsonResponse(true, 'Thêm giao dịch thành công!');
} elseif ($action === 'update_transaction') {
    jsonResponse(true, 'Cập nhật giao dịch thành công!');
} elseif ($action === 'delete_transaction') {
    jsonResponse(true, 'Xóa giao dịch thành công!');
} else {
    jsonResponse(false, 'Invalid action');
}
?>