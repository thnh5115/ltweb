<?php
require_once '../../config.php';
require_once '../../functions.php';
requireLogin();

include 'partials/header.php';
include 'partials/sidebar.php';
include 'partials/navbar.php';
?>


    
        <!-- Page Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="fas fa-chart-bar mr-3 text-primary-600"></i>
                Báo cáo thống kê
            </h2>
            <p class="text-muted">Phân tích chi tiêu và thu nhập của bạn</p>
        </div>

        <!-- Date Range Filter -->
        <div class="card mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <select id="periodSelect" class="form-control">
                        <option value="this_month">Tháng này</option>
                        <option value="last_month">Tháng trước</option>
                        <option value="3_months">3 tháng</option>
                        <option value="6_months">6 tháng</option>
                        <option value="this_year">Năm nay</option>
                        <option value="custom">Tùy chỉnh</option>
                    </select>
                    <input type="date" id="startDate" class="form-control" placeholder="Từ ngày">
                    <input type="date" id="endDate" class="form-control" placeholder="Đến ngày">
                    <select id="export_format" class="form-control" style="width: auto;">
                        <option value="csv">CSV</option>
                        <option value="json">JSON</option>
                    </select>
                    <button class="btn btn-primary" onclick="loadStatistics()">
                        <i class="fas fa-filter mr-2"></i> Lọc
                    </button>
                    <button class="btn btn-outline" onclick="exportReport()">
                        <i class="fas fa-download mr-2"></i> Xuất báo cáo
                    </button>
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 stagger-children">
            <div class="stat-card">
                <div class="stat-icon" style="background: var(--gradient-success);">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <span class="stat-label">Tổng thu nhập</span>
                <span class="stat-value" id="total-income">0 đ</span>
                <span class="trend-indicator trend-up">
                    <i class="fas fa-arrow-up"></i> <span id="income-change">0%</span>
                </span>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: var(--gradient-danger);">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <span class="stat-label">Tổng chi tiêu</span>
                <span class="stat-value" id="total-expense">0 đ</span>
                <span class="trend-indicator trend-down">
                    <i class="fas fa-arrow-down"></i> <span id="expense-change">0%</span>
                </span>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: var(--gradient-primary);">
                    <i class="fas fa-wallet"></i>
                </div>
                <span class="stat-label">Số dư</span>
                <span class="stat-value" id="balance">0 đ</span>
                <span class="trend-indicator" style="background: var(--primary-100); color: var(--primary-700);">
                    <i class="fas fa-equals"></i> Hiện tại
                </span>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background: var(--gradient-warning);">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <span class="stat-label">Giao dịch</span>
                <span class="stat-value" id="transaction-count">0</span>
                <span class="trend-indicator" style="background: var(--warning-light); color: var(--warning-dark);">
                    <i class="fas fa-list"></i> Tổng số
                </span>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Income vs Expense Chart -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-chart-line mr-2 text-primary-600"></i>
                        Thu chi theo thời gian
                    </h3>
                    <div class="chart-controls">
                        <button class="btn btn-sm btn-outline chart-interval-btn active" data-interval="day">Ngày</button>
                        <button class="btn btn-sm btn-outline chart-interval-btn" data-interval="week">Tuần</button>
                        <button class="btn btn-sm btn-outline chart-interval-btn" data-interval="month">Tháng</button>
                    </div>
                </div>
                <div style="height: 320px; padding: var(--space-4);">
                    <canvas id="timeChart"></canvas>
                </div>
            </div>

            <!-- Category Distribution -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-chart-pie mr-2 text-accent-600"></i>
                        Phân bố theo danh mục
                    </h3>
                </div>
                <div
                    style="height: 320px; padding: var(--space-4); display: flex; justify-content: center; align-items: center;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>

            <!-- Expense Trend -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-chart-area mr-2 text-danger"></i>
                        Xu hướng chi tiêu
                    </h3>
                </div>
                <div style="height: 320px; padding: var(--space-4);">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <!-- Income Sources -->
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-chart-bar mr-2 text-success"></i>
                        Nguồn thu nhập
                    </h3>
                </div>
                <div style="height: 320px; padding: var(--space-4);">
                    <canvas id="incomeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Categories Table -->
        <div class="card entrance-fade">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-trophy mr-2 text-warning"></i>
                    Top danh mục chi tiêu
                </h3>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Xếp hạng</th>
                            <th>Danh mục</th>
                            <th>Số giao dịch</th>
                            <th>Tổng chi tiêu</th>
                            <th>% Tổng</th>
                        </tr>
                    </thead>
                    <tbody id="top-categories">
                        <tr>
                            <td colspan="5">
                                <div class="flex justify-center py-8">
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

<script>
    let statisticsTimeChartInstance = null;
    let statsInterval = 'day';

    $(document).ready(function () {
        loadStatistics();

        $('#periodSelect').on('change', function () {
            if ($(this).val() === 'custom') {
                $('#startDate, #endDate').prop('disabled', false);
            } else {
                $('#startDate, #endDate').prop('disabled', true);
                loadStatistics();
            }
        });

        $('#startDate, #endDate').on('change', function () {
            if ($('#periodSelect').val() === 'custom' && $('#startDate').val() && $('#endDate').val()) {
                loadStatistics();
            }
        });

        $('.chart-interval-btn').on('click', function () {
            if ($(this).hasClass('active')) return;
            $('.chart-interval-btn').removeClass('active');
            $(this).addClass('active');
            statsInterval = $(this).data('interval') || 'day';
            loadStatistics();
        });
    });

    function loadStatistics() {
        const period = $('#periodSelect').val();
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        const isCustomRange = period === 'custom';

        if (isCustomRange && (!startDate || !endDate)) {
            return;
        }

        let urlParams = 'period=' + period;
        if (isCustomRange) {
            urlParams += '&start_date=' + startDate + '&end_date=' + endDate;
        }

        // Load Summary Stats
        $.get('/api/data.php?action=statistics_summary&' + urlParams, function (res) {
            if (res.success) {
                $('#total-income').text(formatMoney(res.data.income));
                $('#total-expense').text(formatMoney(res.data.expense));
                $('#balance').text(formatMoney(res.data.balance));
                $('#transaction-count').text(res.data.count);
                $('#income-change').text(res.data.income_change + '%');
                $('#expense-change').text(res.data.expense_change + '%');
            }
        });

        // Load Charts
        const chartParams = urlParams + '&interval=' + statsInterval;
        $.get('/api/data.php?action=statistics_charts&' + chartParams, function (res) {
            if (res.success) {
                renderCharts(res.data);
            }
        });

        // Load Top Categories
        $.get('/api/data.php?action=top_categories&' + urlParams, function (res) {
            if (res.success) {
                const tbody = $('#top-categories');
                tbody.empty();

                if (res.data.length === 0) {
                    tbody.html(`
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="fas fa-chart-pie"></i>
                                <h3>Chưa có dữ liệu</h3>
                                <p>Thêm giao dịch để xem thống kê</p>
                            </div>
                        </td>
                    </tr>
                `);
                    return;
                }

                res.data.forEach((cat, index) => {
                    const rankBadge = index < 3
                        ? `<span class="text-2xl">${['🥇', '🥈', '🥉'][index]}</span>`
                        : `<span class="font-bold text-gray-500">#${index + 1}</span>`;

                    const html = `
                    <tr>
                        <td class="text-center">${rankBadge}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white shadow-sm" style="background: ${cat.color}">
                                    <i class="fas ${cat.icon}"></i>
                                </div>
                                <span class="font-medium">${cat.name}</span>
                            </div>
                        </td>
                        <td class="font-medium">${cat.count} giao dịch</td>
                        <td class="font-mono font-bold text-danger">${formatMoney(cat.amount)}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-danger h-2 rounded-full" style="width: ${cat.percentage}%"></div>
                                </div>
                                <span class="text-sm font-semibold">${cat.percentage}%</span>
                            </div>
                        </td>
                    </tr>
                `;
                    tbody.append(html);
                });
            }
        });
    }

    function renderCharts(data) {
        if (statisticsTimeChartInstance) {
            statisticsTimeChartInstance.destroy();
        }
        statisticsTimeChartInstance = new Chart(document.getElementById('timeChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: data.time.labels,
                datasets: buildSmoothDatasets(data.time)
            },
            options: getSmoothChartOptions()
        });

        // Category Chart
        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: data.categories.labels,
                datasets: [{
                    data: data.categories.values,
                    backgroundColor: data.categories.colors,
                    borderWidth: 3,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Trend Chart
        new Chart(document.getElementById('trendChart'), {
            type: 'bar',
            data: {
                labels: data.trend.labels,
                datasets: [{
                    label: 'Chi tiêu',
                    data: data.trend.values,
                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Income Chart
        new Chart(document.getElementById('incomeChart'), {
            type: 'bar',
            data: {
                labels: data.income.labels,
                datasets: [{
                    label: 'Thu nhập',
                    data: data.income.values,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    function exportReport() {
        const period = document.getElementById('periodSelect').value;
        const startDate = document.getElementById('startDate')?.value || '';
        const endDate = document.getElementById('endDate')?.value || '';
        const format = document.getElementById('export_format')?.value || 'csv';

        const params = new URLSearchParams({
            action: 'export_statistics',
            period,
            format
        });

        if (period === 'custom' && startDate && endDate) {
            params.append('start_date', startDate);
            params.append('end_date', endDate);
        }

        showToast('info', 'Đang chuẩn bị file báo cáo...');

        const link = document.createElement('a');
        link.href = `/api/data.php?${params.toString()}`;
        link.setAttribute('download', `bao-cao-thong-ke.${format}`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        setTimeout(() => showToast('success', 'Báo cáo đang được tải xuống'), 600);
    }
</script>

<script>
    function buildSmoothDatasets(series) {
        return [
            createSmoothDataset('Thu nhập', series.income, '#10B981'),
            createSmoothDataset('Chi tiêu', series.expense, '#EF4444')
        ];
    }

    function createSmoothDataset(label, data, color) {
        return {
            label,
            data,
            borderColor: color,
            pointBackgroundColor: color,
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            fill: true,
            borderWidth: 3,
            tension: 0.45,
            cubicInterpolationMode: 'monotone',
            pointRadius: ctx => getSmoothPointRadius(ctx),
            pointHoverRadius: 7,
            backgroundColor: ctx => buildSmoothGradient(ctx, color)
        };
    }

    function getSmoothChartOptions() {
        return {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 16,
                        font: { family: 'Inter', size: 13, weight: '600' }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        label: ctx => `${ctx.dataset.label}: ${formatMoney(ctx.parsed.y)}`
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

    function getSmoothPointRadius(ctx) {
        const value = ctx.raw || 0;
        if (value <= 0) return 0;
        const lastIndex = ctx.dataset.data.length - 1;
        return ctx.dataIndex === lastIndex ? 6 : 3;
    }

    function buildSmoothGradient(ctx, color) {
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