<?php
require_once '../../config.php';
require_once 'admin_functions.php';
requireAdminLogin();

include 'partials/header.php';
include 'partials/sidebar.php';
include 'partials/navbar.php';
?>

        <!-- Page Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="fas fa-tachometer-alt mr-3 text-primary-600"></i>
                Admin Dashboard
            </h2>
            <p class="text-muted">Tổng quan hệ thống và hoạt động người dùng</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8 stagger-children">
            <div class="admin-stat-card">
                <div class="admin-stat-icon blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="admin-stat-content">
                    <span class="admin-stat-label">Tổng người dùng</span>
                    <span class="admin-stat-value" id="total-users">0</span>
                    <span class="admin-stat-change positive" id="total-users-change">
                        <i class="fas fa-arrow-up"></i> +12% tháng này
                    </span>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-icon purple">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div class="admin-stat-content">
                    <span class="admin-stat-label">Tổng giao dịch</span>
                    <span class="admin-stat-value" id="total-transactions">0</span>
                    <span class="admin-stat-change positive" id="total-transactions-change">
                        <i class="fas fa-arrow-up"></i> +8% tháng này
                    </span>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-icon teal">
                    <i class="fas fa-th-list"></i>
                </div>
                <div class="admin-stat-content">
                    <span class="admin-stat-label">Danh mục</span>
                    <span class="admin-stat-value" id="total-categories">0</span>
                    <span class="admin-stat-change positive" id="total-categories-change">
                        <i class="fas fa-arrow-up"></i> +5%
                    </span>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-icon green">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="admin-stat-content">
                    <span class="admin-stat-label">Chi tiêu tháng này</span>
                    <span class="admin-stat-value" id="total-expense-month" style="color: var(--success);">0 đ</span>
                    <span class="admin-stat-change positive" id="total-expense-month-change">
                        <i class="fas fa-arrow-up"></i> +15%
                    </span>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-icon red">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="admin-stat-content">
                    <span class="admin-stat-label">Giao dịch pending</span>
                    <span class="admin-stat-value" id="pending-transactions" style="color: var(--danger);">0</span>
                    <span class="admin-stat-change negative" id="pending-transactions-change">
                        <i class="fas fa-arrow-down"></i> -5%
                    </span>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="lg:col-span-2">
                <div class="chart-container">
                    <div class="chart-header">
                        <h3 class="chart-title">
                            <i class="fas fa-chart-line mr-2 text-primary-600"></i>
                            Chi tiêu theo tháng (năm hiện tại)
                        </h3>
                    </div>
                    <div style="height: 320px; padding: var(--space-4);">
                        <canvas id="systemLineChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="chart-container">
                    <div class="chart-header">
                        <h3 class="chart-title">
                            <i class="fas fa-chart-pie mr-2 text-accent-600"></i>
                            Phân bố chi tiêu theo tháng
                        </h3>
                    </div>
                    <div
                        style="height: 320px; padding: var(--space-4); display: flex; justify-content: center; align-items: center;">
                        <canvas id="systemPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Data -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Transactions -->
            <div class="admin-card entrance-fade">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">
                        <i class="fas fa-history mr-2 text-primary-600"></i>
                        Giao dịch mới nhất
                    </h3>
                    <a href="admin_transactions.php" class="btn btn-sm btn-primary">
                        Xem tất cả
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Loại</th>
                                <th>Số tiền</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody id="recent-transactions">
                            <tr>
                                <td colspan="4">
                                    <div class="flex justify-center py-8">
                                        <div class="spinner spinner-sm"></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- New Users -->
            <div class="admin-card entrance-fade">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">
                        <i class="fas fa-user-plus mr-2 text-success"></i>
                        Người dùng mới
                    </h3>
                    <a href="admin_users.php" class="btn btn-sm btn-primary">
                        Xem tất cả
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Tên</th>
                                <th>Email</th>
                                <th>Ngày tạo</th>
                            </tr>
                        </thead>
                        <tbody id="new-users">
                            <tr>
                                <td colspan="3">
                                    <div class="flex justify-center py-8">
                                        <div class="spinner spinner-sm"></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        loadDashboardSummary();
        loadCharts();
        loadRecentData();
    });

    function loadDashboardSummary() {
        $.post('/api/admin_data.php', { action: 'get_dashboard_summary' }, function (res) {
            if (res && res.success && res.data) {
                $('#total-users').text(res.data.total_users ?? 0);
                $('#total-transactions').text(res.data.total_transactions ?? 0);
                $('#total-categories').text(res.data.total_categories ?? 0);
                $('#total-expense-month').text(formatMoney(res.data.total_expense_this_month ?? 0));
                $('#pending-transactions').text(res.data.pending_transactions ?? 0);

                // Update percentage changes
                updatePercentageChange('total-users-change', res.data.users_change_percent);
                updatePercentageChange('total-transactions-change', res.data.transactions_change_percent);
                updatePercentageChange('total-categories-change', res.data.categories_change_percent);
                updatePercentageChange('total-expense-month-change', res.data.expense_change_percent);
                updatePercentageChange('pending-transactions-change', res.data.pending_change_percent);
            }
        });
    }

    function updatePercentageChange(elementId, percent) {
        const element = $('#' + elementId);
        if (percent !== undefined) {
            const isPositive = percent >= 0;
            const sign = isPositive ? '+' : '';
            const icon = isPositive ? 'fa-arrow-up' : 'fa-arrow-down';
            const className = isPositive ? 'positive' : 'negative';

            element.removeClass('positive negative').addClass(className);
            element.html(`<i class="fas ${icon}"></i> ${sign}${Math.abs(percent)}% tháng này`);
        }
    }

    function loadCharts() {
        $.post('/api/admin_data.php', { action: 'get_dashboard_chart' }, function (res) {
            if (res && res.success && res.data) {
                const labels = res.data.labels || [];
                const values = res.data.values || [];

                // Line Chart
                new Chart(document.getElementById('systemLineChart'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Chi tiêu',
                            data: values,
                            borderColor: '#EF4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#EF4444',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(0, 0, 0, 0.05)' },
                                ticks: { font: { family: 'JetBrains Mono' } }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });

                // Pie Chart (phân bố chi tiêu theo tháng)
                new Chart(document.getElementById('systemPieChart'), {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: labels.map(() => '#6C5CE7'),
                            borderWidth: 3,
                            borderColor: '#fff',
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: { family: 'Inter', size: 13, weight: '600' }
                                }
                            }
                        }
                    }
                });
            }
        });
    }

    function loadRecentData() {
        // Recent Transactions
        $.post('/api/admin_data.php', { action: 'get_recent_transactions' }, function (res) {
            if (res.success) {
                const list = $('#recent-transactions');
                list.empty();
                if (!res.data || !res.data.length) {
                    list.append('<tr><td colspan="4" class="text-center text-muted py-4">Không có giao dịch</td></tr>');
                    return;
                }
                res.data.slice(0, 5).forEach(t => {
                    const statusBadge = '<span class="status-badge status-active">Hoàn thành</span>';
                    const amountClass = t.type === 'income' ? 'text-success' : 'text-danger';
                    const html = `
                    <tr>
                        <td class="font-medium">${t.user}</td>
                        <td>
                            <span class="badge ${t.type === 'income' ? 'badge-success' : 'badge-danger'}">
                                ${t.type === 'income' ? 'Thu' : 'Chi'}
                            </span>
                        </td>
                        <td class="${amountClass} font-mono font-bold">${formatMoney(t.amount)}</td>
                        <td>${statusBadge}</td>
                    </tr>
                `;
                    list.append(html);
                });
            }
        });

        // New Users
        $.post('/api/admin_data.php', { action: 'get_recent_users' }, function (res) {
            if (res.success) {
                const list = $('#new-users');
                list.empty();
                if (!res.data || !res.data.length) {
                    list.append('<tr><td colspan="3" class="text-center text-muted py-4">Chưa có người dùng mới</td></tr>');
                    return;
                }
                res.data.slice(0, 5).forEach(u => {
                    const html = `
                    <tr>
                        <td class="font-medium">${u.name}</td>
                        <td class="text-muted">${u.email}</td>
                        <td class="text-sm">${u.created_at}</td>
                    </tr>
                `;
                    list.append(html);
                });
            }
        });
    }
</script>

<?php include 'partials/footer.php'; ?>
