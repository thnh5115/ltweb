<?php
require_once '../../config.php';
require_once 'admin_functions.php';
requireAdminLogin();

include 'partials/header.php';
include 'partials/sidebar.php';
include 'partials/navbar.php';
?>

<div class="container">
    <div class="mb-6">
        <h2 class="text-xl font-bold">Báo cáo & Xuất dữ liệu</h2>
        <p class="text-muted text-sm">Tạo và xuất báo cáo hệ thống</p>
    </div>

    <!-- Report Configuration -->
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="card-title">Cấu hình báo cáo</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="form-group">
                    <label class="form-label">Loại báo cáo</label>
                    <select class="form-control" id="reportType">
                        <option value="monthly_summary">Tổng quan tháng</option>
                        <option value="user_activity">Hoạt động người dùng</option>
                        <option value="top_spenders">Top chi tiêu</option>
                        <option value="category_breakdown">Phân tích danh mục</option>
                        <option value="budget_analysis">Phân tích ngân sách</option>
                        <option value="transaction_report">Báo cáo giao dịch</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Từ ngày</label>
                    <input type="date" class="form-control" id="dateFrom">
                </div>
                <div class="form-group">
                    <label class="form-label">Đến ngày</label>
                    <input type="date" class="form-control" id="dateTo">
                </div>
            </div>
            <div class="flex gap-3 mt-4">
                <button class="btn btn-primary" onclick="generateReport()" style="background-color: #1e40af;">
                    <i class="fas fa-chart-bar mr-2"></i> Tạo báo cáo
                </button>
                <button class="btn btn-outline" onclick="exportCSV()">
                    <i class="fas fa-file-csv mr-2"></i> Xuất CSV
                </button>
                <button class="btn btn-outline" onclick="exportPDF()">
                    <i class="fas fa-file-pdf mr-2"></i> Xuất PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Report Preview -->
    <div class="card" id="reportPreview" style="display: none;">
        <div class="card-header flex justify-between items-center">
            <h3 class="card-title" id="reportTitle">Báo cáo</h3>
            <span class="text-sm text-muted" id="reportDate"></span>
        </div>

        <!-- Summary Stats -->
        <div class="p-6 border-b" id="reportSummary">
            <!-- Loaded dynamically -->
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thu/Chi theo tháng</h3>
                </div>
                <div style="height: 320px; padding: var(--space-4);">
                    <canvas id="reportMonthChart"></canvas>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Phân bổ theo danh mục</h3>
                </div>
                <div style="height: 320px; padding: var(--space-4); display:flex;align-items:center;justify-content:center;">
                    <canvas id="reportCategoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Report Table -->
        <div class="table-responsive">
            <table class="table admin-table" id="reportTable">
                <thead id="reportTableHead">
                    <!-- Loaded dynamically -->
                </thead>
                <tbody id="reportTableBody">
                    <!-- Loaded dynamically -->
                </tbody>
            </table>
        </div>
    </div>
<!-- Quick Reports -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <div class="card">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold">Báo cáo tháng này</h4>
                        <p class="text-sm text-muted">Tổng quan tháng hiện tại</p>
                    </div>
                </div>
                <button class="btn btn-outline w-full" onclick="quickReport('current_month')">
                    <i class="fas fa-download mr-2"></i> Tạo báo cáo
                </button>
            </div>
        </div>

        <div class="card">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                        <i class="fas fa-chart-line text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold">Báo cáo quý</h4>
                        <p class="text-sm text-muted">Tổng quan quý hiện tại</p>
                    </div>
                </div>
                <button class="btn btn-outline w-full" onclick="quickReport('current_quarter')">
                    <i class="fas fa-download mr-2"></i> Tạo báo cáo
                </button>
            </div>
        </div>

        <div class="card">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                        <i class="fas fa-calendar-check text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold">Báo cáo năm</h4>
                        <p class="text-sm text-muted">Tổng quan năm hiện tại</p>
                    </div>
                </div>
                <button class="btn btn-outline w-full" onclick="quickReport('current_year')">
                    <i class="fas fa-download mr-2"></i> Tạo báo cáo
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .report-stat-card {
        background-color: #f9fafb;
        padding: 1.5rem;
        border-radius: 8px;
        text-align: center;
    }

    .report-stat-label {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }

    .report-stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #111827;
    }

    .report-stat-change {
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    .report-stat-change.positive {
        color: #10b981;
    }

    .report-stat-change.negative {
        color: #ef4444;
    }
</style>

<script>
    let monthChart = null;
    let categoryChart = null;

    $(document).ready(function () {
        loadReports();
    });

    function getFilters() {
        return {
            date_from: $('#dateFrom').val(),
            date_to: $('#dateTo').val(),
            year: ($('#dateFrom').val() || '').slice(0, 4) || new Date().getFullYear(),
            type: $('#reportType').val() === 'transaction_report' ? '' : ''
        };
    }

    function loadReports() {
        const filters = getFilters();
        const reportType = $('#reportType').val();
        $('#reportTitle').text(getReportTitle(reportType));
        $('#reportDate').text(`${filters.date_from || '---'} đến ${filters.date_to || '---'}`);

        loadSummary(filters);
        loadMonthChart(filters);
        loadCategoryChart(filters);
    }

    function loadSummary(filters) {
        $.ajax({
            url: '/api/admin_data.php',
            method: 'POST',
            dataType: 'json',
            data: Object.assign({ action: 'admin_get_report_summary' }, filters)
        }).done(function (res) {
            if (res.success) {
                const data = res.data || {};
                const html = `
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="report-stat-card">
                        <div class="report-stat-label">Tổng thu</div>
                        <div class="report-stat-value text-success">${formatMoney(data.total_income || 0)}</div>
                    </div>
                    <div class="report-stat-card">
                        <div class="report-stat-label">Tổng chi</div>
                        <div class="report-stat-value text-danger">${formatMoney(data.total_expense || 0)}</div>
                    </div>
                    <div class="report-stat-card">
                        <div class="report-stat-label">Net (Thu - Chi)</div>
                        <div class="report-stat-value">${formatMoney((data.net || 0))}</div>
                    </div>
                </div>`;
                $('#reportSummary').html(html);
                $('#reportPreview').show();
            }
        }).fail(function () {
            showToast('error', 'Không tải được summary');
        });
    }

    function loadMonthChart(filters) {
        $.ajax({
            url: '/api/admin_data.php',
            method: 'POST',
            dataType: 'json',
            data: Object.assign({ action: 'admin_get_report_by_month' }, filters)
        }).done(function (res) {
            if (res.success) {
                const ctx = document.getElementById('reportMonthChart');
                const labels = res.data.labels || [];
                const incomeValues = res.data.income_values || [];
                const expenseValues = res.data.expense_values || [];
                if (monthChart) monthChart.destroy();
                monthChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Thu',
                                data: incomeValues,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16,185,129,0.1)',
                                tension: 0.4,
                                fill: true
                            },
                            {
                                label: 'Chi',
                                data: expenseValues,
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239,68,68,0.1)',
                                tension: 0.4,
                                fill: true
                            }
                        ]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            }
        }).fail(function () {
            showToast('error', 'Không tải được biểu đồ tháng');
        });
    }

    function loadCategoryChart(filters) {
        $.ajax({
            url: '/api/admin_data.php',
            method: 'POST',
            dataType: 'json',
            data: Object.assign({ action: 'admin_get_report_by_category' }, filters)
        }).done(function (res) {
            if (res.success) {
                const labels = res.data.labels || [];
                const values = res.data.values || [];
                const ctx = document.getElementById('reportCategoryChart');
                if (categoryChart) categoryChart.destroy();
                categoryChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: labels.map(() => '#6C5CE7'),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' } }
                    }
                });

                renderCategoryTable(labels, values);
                $('#reportPreview').show();
            }
        }).fail(function () {
            showToast('error', 'Không tải được phân bổ danh mục');
        });
    }

    function renderCategoryTable(labels, values) {
        const thead = $('#reportTableHead');
        const tbody = $('#reportTableBody');
        thead.html('<tr><th>Danh mục</th><th>Số tiền</th></tr>');
        tbody.empty();
        labels.forEach((label, idx) => {
            tbody.append(`<tr><td>${label}</td><td>${formatMoney(values[idx] || 0)}</td></tr>`);
        });
    }

    function getReportTitle(type) {
        const titles = {
            'monthly_summary': 'Báo cáo tổng quan',
            'user_activity': 'Hoạt động người dùng',
            'top_spenders': 'Top chi tiêu',
            'category_breakdown': 'Phân tích danh mục',
            'budget_analysis': 'Phân tích ngân sách',
            'transaction_report': 'Báo cáo giao dịch chi tiết'
        };
        return titles[type] || 'Báo cáo';
    }

    function quickReport(period) {
        const today = new Date();
        let dateFrom, dateTo;

        if (period === 'current_month') {
            dateFrom = new Date(today.getFullYear(), today.getMonth(), 1);
            dateTo = today;
        } else if (period === 'current_quarter') {
            const quarter = Math.floor(today.getMonth() / 3);
            dateFrom = new Date(today.getFullYear(), quarter * 3, 1);
            dateTo = today;
        } else if (period === 'current_year') {
            dateFrom = new Date(today.getFullYear(), 0, 1);
            dateTo = today;
        }

        $('#dateFrom').val(dateFrom.toISOString().split('T')[0]);
        $('#dateTo').val(dateTo.toISOString().split('T')[0]);
        $('#reportType').val('monthly_summary');
        loadReports();
    }

    function generateReport() {
        loadReports();
    }

    function exportCSV() {
        showToast('info', 'Chức năng export CSV chưa được triển khai.');
    }

    function exportPDF() {
        showToast('info', 'Chức năng export PDF chưa được triển khai.');
    }
</script>


<?php include 'partials/footer.php'; ?>
