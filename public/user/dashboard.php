<?php
require_once '../../config.php';
require_once '../../functions.php';
requireLogin();

include 'partials/header.php';
include 'partials/sidebar.php';
include 'partials/navbar.php';
?>


    
        <!-- Hero Section -->
        <div class="dashboard-hero mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-4xl font-bold text-white mb-2">
                        Xin chào, <?php echo $_SESSION['user_name'] ?? 'User'; ?>! 👋
                    </h1>
                    <p class="text-primary-100 text-lg">
                        Đây là tổng quan tài chính của bạn
                    </p>
                </div>
                <a href="transactions.php" class="btn btn-outline"
                    style="background: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.3); color: white;">
                    <i class="fas fa-plus mr-2"></i> Thêm giao dịch
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 stagger-children">
            <!-- Income Card -->
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--gradient-success);">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <span class="stat-label">Tổng thu nhập</span>
                <span class="stat-value" id="total-income">0 đ</span>
                <span class="trend-indicator trend-up">
                    <i class="fas fa-arrow-up"></i> Tháng này
                </span>
            </div>

            <!-- Expense Card -->
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--gradient-danger);">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <span class="stat-label">Tổng chi tiêu</span>
                <span class="stat-value" id="total-expense">0 đ</span>
                <span class="trend-indicator trend-down">
                    <i class="fas fa-arrow-down"></i> Tháng này
                </span>
            </div>

            <!-- Balance Card -->
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--gradient-primary);">
                    <i class="fas fa-wallet"></i>
                </div>
                <span class="stat-label">Số dư hiện tại</span>
                <span class="stat-value" id="total-balance">0 đ</span>
                <span class="trend-indicator" style="background: var(--primary-100); color: var(--primary-700);">
                    <i class="fas fa-chart-line"></i> Cập nhật
                </span>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Line Chart -->
            <div class="lg:col-span-2">
                <div class="card card-premium">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line mr-2 text-primary-600"></i>
                            Biểu đồ thu chi
                        </h3>
                        <select id="dashboardChartPeriod" class="form-control"
                            style="width: auto; padding: 0.5rem 1rem; font-size: var(--text-sm);">
                            <option value="this_month">Tháng này</option>
                            <option value="last_month">Tháng trước</option>
                            <option value="3_months">3 tháng</option>
                            <option value="6_months">6 tháng</option>
                            <option value="12_months">12 tháng</option>
                        </select>
                    </div>
                    <div style="height: 320px; padding: var(--space-4);">
                        <canvas id="expenseLineChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Pie Chart -->
            <div class="lg:col-span-1">
                <div class="card card-premium">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-2 text-accent-600"></i>
                            Phân bố chi tiêu
                        </h3>
                    </div>
                    <div
                        style="height: 320px; padding: var(--space-4); display: flex; justify-content: center; align-items: center;">
                        <canvas id="categoryPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="card entrance-fade">
            <div class="card-header">
                <div>
                    <h3 class="card-title">
                        <i class="fas fa-history mr-2 text-primary-600"></i>
                        Giao dịch gần đây
                    </h3>
                    <p class="text-sm text-muted mt-1">5 giao dịch mới nhất</p>
                </div>
                <a href="transactions.php" class="btn btn-primary">
                    <i class="fas fa-list mr-2"></i> Xem tất cả
                </a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Danh mục</th>
                            <th>Ghi chú</th>
                            <th>Ngày</th>
                            <th class="text-right">Số tiền</th>
                        </tr>
                    </thead>
                    <tbody id="recent-transactions-list">
                        <!-- Loaded via AJAX -->
                        <tr>
                            <td colspan="4">
                                <div class="flex justify-center items-center py-8">
                                    <div class="spinner"></div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .dashboard-hero {
        background: var(--gradient-primary);
        padding: var(--space-12) var(--space-8);
        border-radius: var(--radius-2xl);
        box-shadow: var(--shadow-xl), var(--shadow-primary);
        position: relative;
        overflow: hidden;
    }

    .dashboard-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .dashboard-hero::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -5%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
        border-radius: 50%;
    }

    .dashboard-hero>* {
        position: relative;
        z-index: 1;
    }
</style>

<script>
    let overviewLineChartInstance = null;
    let overviewPieChartInstance = null;
    let dashboardChartPeriod = 'this_month';

    $(document).ready(function () {
        const $chartPeriodSelect = $('#dashboardChartPeriod');
        if ($chartPeriodSelect.length) {
            dashboardChartPeriod = $chartPeriodSelect.val() || 'this_month';
            $chartPeriodSelect.on('change', function () {
                dashboardChartPeriod = $(this).val();
                loadDashboardCharts(dashboardChartPeriod);
            });
        }

        loadDashboardData();
        loadDashboardCharts(dashboardChartPeriod);
    });

    function loadDashboardData() {
        // Load Stats
        $.get('/api/data.php?action=dashboard_stats', function (res) {
            if (res.success) {
                $('#total-income').text(formatMoney(res.data.income));
                $('#total-expense').text(formatMoney(res.data.expense));
                $('#total-balance').text(formatMoney(res.data.balance));
            }
        });

        // Load Recent Transactions
        $.get('/api/data.php?action=recent_transactions', function (res) {
            if (res.success) {
                const list = $('#recent-transactions-list');
                list.empty();

                if (res.data.length === 0) {
                    list.html(`
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h3>Chưa có giao dịch</h3>
                                <p>Bắt đầu thêm giao dịch đầu tiên của bạn</p>
                            </div>
                        </td>
                    </tr>
                `);
                    return;
                }

                res.data.forEach(t => {
                    const amountClass = t.type === 'income' ? 'text-success' : 'text-danger';
                    const sign = t.type === 'income' ? '+' : '-';
                    const html = `
                    <tr class="transition-all">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white shadow-md" style="background: ${t.category_color}">
                                    <i class="fas ${t.category_icon}"></i>
                                </div>
                                <span class="font-medium">${t.category_name}</span>
                            </div>
                        </td>
                        <td class="text-muted">${t.note || '-'}</td>
                        <td class="text-sm">${t.date}</td>
                        <td class="text-right">
                            <span class="font-mono font-bold text-lg ${amountClass}">
                                ${sign}${formatMoney(t.amount)}
                            </span>
                        </td>
                    </tr>
                `;
                    list.append(html);
                });
            }
        });
    }

    function loadDashboardCharts(period = 'this_month') {
        const params = $.param({ action: 'chart_data', period });
        $.get(`/api/data.php?${params}`, function (res) {
            if (res.success) {
                renderDashboardCharts(res.data);
            }
        });
    }

    function renderDashboardCharts(data) {
        // Pie Chart - Updated colors to match design system
        const pieCanvas = document.getElementById('categoryPieChart');
        if (pieCanvas) {
            const pieCtx = pieCanvas.getContext('2d');
            const pieLabels = Object.keys(data.pie);
            const pieData = pieLabels.map(k => data.pie[k].amount);
            const pieColors = pieLabels.map(k => data.pie[k].color);

            if (overviewPieChartInstance) {
                overviewPieChartInstance.destroy();
            }

            overviewPieChartInstance = new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: pieLabels,
                    datasets: [{
                        data: pieData,
                        backgroundColor: pieColors,
                        borderWidth: 3,
                        borderColor: '#fff',
                        hoverBorderWidth: 4,
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
                                    size: 12,
                                    weight: '500'
                                },
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                label: function (context) {
                                    return context.label + ': ' + formatMoney(context.parsed);
                                }
                            }
                        }
                    }
                }
            });
        }

        // Area Line Chart styled like mockup
        overviewLineChartInstance = createAreaLineChart('expenseLineChart', data.line.labels, [
            {
                label: 'Thu nhập',
                data: data.line.income,
                color: '#10B981'
            },
            {
                label: 'Chi tiêu',
                data: data.line.expense,
                color: '#EF4444'
            }
        ], { legendPosition: 'top' });
    }

    function createAreaLineChart(canvasId, labels, series, options = {}) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return null;

        if (overviewLineChartInstance) {
            overviewLineChartInstance.destroy();
        }

        const datasets = series.map(cfg => buildAreaDataset(cfg));

        return new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: { labels, datasets },
            options: getAreaChartOptions(options)
        });
    }

    function buildAreaDataset(config) {
        return {
            label: config.label,
            data: config.data,
            borderColor: config.color,
            pointBackgroundColor: config.color,
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            borderWidth: 3,
            cubicInterpolationMode: 'monotone',
            tension: 0.45,
            fill: true,
            pointRadius: ctx => getPointRadius(ctx),
            pointHoverRadius: 7,
            backgroundColor: ctx => buildGradientBackground(ctx, config.color)
        };
    }

    function getAreaChartOptions(extra = {}) {
        return {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    display: true,
                    position: extra.legendPosition || 'start',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 20,
                        font: {
                            family: 'Inter',
                            size: 13,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        label: context => `${context.dataset.label}: ${formatMoney(context.parsed.y)}`
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: {
                        color: 'var(--gray-500)',
                        font: { family: 'Inter', size: 12 }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(148, 163, 184, 0.2)',
                        drawBorder: false,
                        drawTicks: false
                    },
                    ticks: {
                        color: 'var(--gray-500)',
                        font: { family: 'JetBrains Mono', size: 11 },
                        callback: value => formatCompactCurrency(value)
                    }
                }
            }
        };
    }

    function getPointRadius(ctx) {
        const value = ctx.raw || 0;
        if (value <= 0) return 0;
        const lastIndex = ctx.dataset.data.length - 1;
        return ctx.dataIndex === lastIndex ? 6 : 3;
    }

    function buildGradientBackground(ctx, color) {
        const chart = ctx.chart;
        const { ctx: canvasCtx, chartArea } = chart;
        if (!chartArea) {
            return hexToRgba(color, 0.15);
        }
        const gradient = canvasCtx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
        gradient.addColorStop(0, hexToRgba(color, 0));
        gradient.addColorStop(1, hexToRgba(color, 0.35));
        return gradient;
    }

    function hexToRgba(hex, alpha) {
        const sanitized = hex.replace('#', '');
        const bigint = parseInt(sanitized.length === 3 ? sanitized.repeat(2) : sanitized, 16);
        const r = (bigint >> 16) & 255;
        const g = (bigint >> 8) & 255;
        const b = bigint & 255;
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    function formatCompactCurrency(value) {
        const absVal = Math.abs(value);
        if (absVal >= 1_000_000_000) {
            return (value / 1_000_000_000).toFixed(1).replace(/\.0$/, '') + 'B';
        }
        if (absVal >= 1_000_000) {
            return (value / 1_000_000).toFixed(1).replace(/\.0$/, '') + 'M';
        }
        if (absVal >= 1_000) {
            return (value / 1_000).toFixed(1).replace(/\.0$/, '') + 'K';
        }
        return value.toString();
    }
</script>

<?php include 'partials/footer.php'; ?>