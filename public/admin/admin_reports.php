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
    $(document).ready(function () {
        // Set default dates (last 30 days)
        const today = new Date();
        const thirtyDaysAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);

        $('#dateTo').val(today.toISOString().split('T')[0]);
        $('#dateFrom').val(thirtyDaysAgo.toISOString().split('T')[0]);
    });

    function generateReport() {
        const reportType = $('#reportType').val();
        const dateFrom = $('#dateFrom').val();
        const dateTo = $('#dateTo').val();

        if (!dateFrom || !dateTo) {
            showToast('error', 'Vui lòng chọn khoảng thời gian');
            return;
        }

        showToast('info', 'Đang tạo báo cáo...');

        $.get('/api/admin_data.php', {
            action: 'get_report_data',
            type: reportType,
            date_from: dateFrom,
            date_to: dateTo
        }, function (response) {
            if (response.success) {
                renderReport(response.data, reportType);
                $('#reportPreview').fadeIn();
            } else {
                showToast('error', response.message);
            }
        });
    }

    function renderReport(data, reportType) {
        // Update report title and date
        $('#reportTitle').text(getReportTitle(reportType));
        $('#reportDate').text(`${$('#dateFrom').val()} đến ${$('#dateTo').val()}`);

        // Render summary stats
        if (data.summary) {
            renderSummary(data.summary);
        }

        // Render table
        renderReportTable(data.table, reportType);
    }

    function getReportTitle(type) {
        const titles = {
            'monthly_summary': 'Báo cáo tổng quan tháng',
            'user_activity': 'Báo cáo hoạt động người dùng',
            'top_spenders': 'Top người dùng chi tiêu nhiều nhất',
            'category_breakdown': 'Phân tích chi tiêu theo danh mục',
            'budget_analysis': 'Phân tích ngân sách',
            'transaction_report': 'Báo cáo giao dịch chi tiết'
        };
        return titles[type] || 'Báo cáo';
    }

    function renderSummary(summary) {
        const container = $('#reportSummary');
        let html = '<div class="grid grid-cols-1 md:grid-cols-4 gap-4">';

        Object.keys(summary).forEach(key => {
            const item = summary[key];
            const changeClass = item.change >= 0 ? 'positive' : 'negative';
            const changeIcon = item.change >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';

            html += `
            <div class="report-stat-card">
                <div class="report-stat-label">${item.label}</div>
                <div class="report-stat-value">${item.value}</div>
                ${item.change !== undefined ? `
                    <div class="report-stat-change ${changeClass}">
                        <i class="fas ${changeIcon} mr-1"></i>
                        ${Math.abs(item.change)}% so với kỳ trước
                    </div>
                ` : ''}
            </div>
        `;
        });

        html += '</div>';
        container.html(html);
    }

    function renderReportTable(tableData, reportType) {
        const thead = $('#reportTableHead');
        const tbody = $('#reportTableBody');

        // Render headers
        let headerHtml = '<tr>';
        tableData.headers.forEach(header => {
            headerHtml += `<th>${header}</th>`;
        });
        headerHtml += '</tr>';
        thead.html(headerHtml);

        // Render rows
        tbody.empty();
        tableData.rows.forEach(row => {
            let rowHtml = '<tr>';
            row.forEach(cell => {
                rowHtml += `<td>${cell}</td>`;
            });
            rowHtml += '</tr>';
            tbody.append(rowHtml);
        });
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

        generateReport();
    }

    function exportCSV() {
        // TODO: Implement CSV export
        // This would typically send the current report data to a backend endpoint
        // that generates a CSV file and returns it for download

        showToast('info', 'Đang xuất CSV...');

        const reportType = $('#reportType').val();
        const dateFrom = $('#dateFrom').val();
        const dateTo = $('#dateTo').val();

        // Simulate export
        setTimeout(() => {
            showToast('success', 'Xuất CSV thành công! (Chức năng demo)');

            // In production, you would do:
            // window.location.href = `/api/admin_data.php?action=export_csv&type=${reportType}&date_from=${dateFrom}&date_to=${dateTo}`;
        }, 1000);
    }

    function exportPDF() {
        // TODO: Implement PDF export
        // This would typically use a library like TCPDF or mPDF on the backend
        // to generate a PDF from the report data

        showToast('info', 'Đang xuất PDF...');

        const reportType = $('#reportType').val();
        const dateFrom = $('#dateFrom').val();
        const dateTo = $('#dateTo').val();

        // Simulate export
        setTimeout(() => {
            showToast('success', 'Xuất PDF thành công! (Chức năng demo)');

            // In production, you would do:
            // window.location.href = `/api/admin_data.php?action=export_pdf&type=${reportType}&date_from=${dateFrom}&date_to=${dateTo}`;
        }, 1000);
    }
</script>

<?php include 'partials/footer.php'; ?>