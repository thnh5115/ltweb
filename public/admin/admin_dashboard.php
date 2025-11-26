<?php
require_once '../../config.php';
require_once 'admin_functions.php';
requireAdminLogin();

include 'partials/header.php';
include 'partials/sidebar.php';
include 'partials/navbar.php';
?>

<div class="main-content">
    <div class="content-body">
        <!-- Page Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="fas fa-tachometer-alt mr-3 text-primary-600"></i>
                Admin Dashboard
            </h2>
            <p class="text-muted">Tổng quan hệ thống và hoạt động người dùng</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 stagger-children">
            <div class="admin-stat-card">
                <div class="admin-stat-icon blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="admin-stat-content">
                    <span class="admin-stat-label">Tổng người dùng</span>
                    <span class="admin-stat-value" id="total-users">0</span>
                    <span class="admin-stat-change positive">
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
                    <span class="admin-stat-change positive">
                        <i class="fas fa-arrow-up"></i> +8% tháng này
                    </span>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-icon green">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="admin-stat-content">
                    <span class="admin-stat-label">Tổng thu nhập</span>
                    <span class="admin-stat-value" id="total-income" style="color: var(--success);">0 đ</span>
                    <span class="admin-stat-change positive">
                        <i class="fas fa-arrow-up"></i> +15%
                    </span>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-icon red">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div class="admin-stat-content">
                    <span class="admin-stat-label">Tổng chi tiêu</span>
                    <span class="admin-stat-value" id="total-expense" style="color: var(--danger);">0 đ</span>
                    <span class="admin-stat-change negative">
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
                            Chi tiêu toàn hệ thống
                        </h3>
                        <div class="chart-controls">
                            <button class="btn btn-sm btn-outline">Tháng</button>
                            <button class="btn btn-sm btn-outline">Quý</button>
                            <button class="btn btn-sm btn-outline">Năm</button>
                        </div>
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
                            Tỷ lệ Thu/Chi
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
        loadDashboardStats();
        loadCharts();
        loadRecentData();
    });

    function loadDashboardStats() {
        $.get('/api/admin_data.php?action=dashboard_stats', function (res) {
            if (res.success) {
                $('#total-users').text(res.data.total_users);
                $('#total-transactions').text(res.data.total_transactions);
                $('#total-income').text(formatMoney(res.data.total_income));
                $('#total-expense').text(formatMoney(res.data.total_expense));
            }
        });
    }

    function loadCharts() {
        $.get('/api/admin_data.php?action=chart_data', function (res) {
            if (res.success) {
                // Line Chart
                new Chart(document.getElementById('systemLineChart'), {
                    type: 'line',
                    data: {
                        labels: res.data.line.labels,
                        datasets: [{
                            label: 'Chi tiêu',
                            data: res.data.line.data,
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
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    font: {
                                        family: 'JetBrains Mono'
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });

                // Pie Chart
                new Chart(document.getElementById('systemPieChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Thu nhập', 'Chi tiêu'],
                        datasets: [{
                            data: [res.data.pie.income, res.data.pie.expense],
                            backgroundColor: ['#10B981', '#EF4444'],
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
                                    font: {
                                        family: 'Inter',
                                        size: 13,
                                        weight: '600'
                                    }
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
        $.get('/api/admin_data.php?action=get_transactions', function (res) {
            if (res.success) {
                const list = $('#recent-transactions');
                list.empty();
                res.data.slice(0, 5).forEach(t => {
                    const statusBadge = t.status === 'completed'
                        ? '<span class="status-badge status-active">Hoàn thành</span>'
                        : '<span class="status-badge status-warning">Chờ xử lý</span>';
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
        $.get('/api/admin_data.php?action=get_users', function (res) {
            if (res.success) {
                const list = $('#new-users');
                list.empty();
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