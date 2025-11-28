<?php
require_once '../../config.php';
require_once 'admin_functions.php';
requireAdminLogin();

include 'partials/header.php';
include 'partials/sidebar.php';
include 'partials/navbar.php';
?>

<div class="container">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold">Báo cáo thống kê hệ thống</h2>
        <button class="btn btn-outline">
            <i class="fas fa-file-csv mr-2"></i> Xuất CSV
        </button>
    </div>

    <div class="card mb-6">
        <div class="p-4 border-b grid grid-cols-1 md:grid-cols-5 gap-4 items-center">
            <label class="font-medium">Thời gian:</label>
            <input type="date" id="stats-date-from" class="form-control" placeholder="Từ ngày">
            <input type="date" id="stats-date-to" class="form-control" placeholder="Đến ngày">
            <button class="btn btn-primary" id="load-stats-btn">Xem báo cáo</button>
            <button class="btn btn-outline" id="clear-stats-filter-btn">Xóa bộ lọc</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 p-6">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-muted mb-1">Tổng thu</p>
                <h3 class="text-xl font-bold text-success" id="total-income">0 đ</h3>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-muted mb-1">Tổng chi</p>
                <h3 class="text-xl font-bold text-danger" id="total-expense">0 đ</h3>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-muted mb-1">Tổng giao dịch</p>
                <h3 class="text-xl font-bold text-primary" id="total-transactions">0</h3>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-muted mb-1">User hoạt động</p>
                <h3 class="text-xl font-bold text-blue-600" id="active-users">0</h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Top danh mục chi tiêu</h3>
            </div>
            <div style="height: 300px;">
                <canvas id="adminStatsBarChart"></canvas>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Phân bố Thu/Chi</h3>
            </div>
            <div style="height: 300px; display: flex; justify-content: center;">
                <canvas id="adminStatsPieChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        loadStatisticsSummary();
        // Delay chart loading to ensure Chart.js is loaded
        setTimeout(loadAdminStatsCharts, 1000);

        $('#load-stats-btn').on('click', function () {
            loadStatisticsSummary();
            loadAdminStatsCharts();
        });

        $('#clear-stats-filter-btn').on('click', function () {
            $('#stats-date-from').val('');
            $('#stats-date-to').val('');
            loadStatisticsSummary();
            loadAdminStatsCharts();
        });
    });

    function loadStatisticsSummary() {
        let dateFrom = $('#stats-date-from').val().trim();
        let dateTo = $('#stats-date-to').val().trim();

        // Validate date format
        const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
        if (dateFrom && !dateRegex.test(dateFrom)) {
            alert('Định dạng ngày "Từ ngày" không hợp lệ. Vui lòng nhập theo format YYYY-MM-DD');
            return;
        }
        if (dateTo && !dateRegex.test(dateTo)) {
            alert('Định dạng ngày "Đến ngày" không hợp lệ. Vui lòng nhập theo format YYYY-MM-DD');
            return;
        }

        // Validate date range
        if (dateFrom && dateTo && new Date(dateFrom) > new Date(dateTo)) {
            [dateFrom, dateTo] = [dateTo, dateFrom];
            $('#stats-date-from').val(dateFrom);
            $('#stats-date-to').val(dateTo);
        }

        const params = { action: 'admin_get_report_summary' };
        if (dateFrom) params.date_from = dateFrom;
        if (dateTo) params.date_to = dateTo;

        $.post('/api/admin_data.php', params, function (res) {
            if (res.success && res.data) {
                $('#total-income').text(formatMoney(res.data.total_income));
                $('#total-expense').text(formatMoney(res.data.total_expense));
            } else {
                $('#total-income').text('0 đ');
                $('#total-expense').text('0 đ');
            }
        });

        // Get total transactions count with date filter
        const dashboardParams = { action: 'get_dashboard_summary' };
        if (dateFrom) dashboardParams.date_from = dateFrom;
        if (dateTo) dashboardParams.date_to = dateTo;

        $.post('/api/admin_data.php', dashboardParams, function (res) {
            if (res.success && res.data) {
                $('#total-transactions').text(res.data.total_transactions);
                $('#active-users').text(res.data.total_users);
            } else {
                $('#total-transactions').text('0');
                $('#active-users').text('0');
            }
        });
    }

    function loadAdminStatsCharts() {
        // Check if Chart.js is loaded and Chart constructor exists
        if (typeof Chart === 'undefined' || typeof Chart !== 'function') {
            console.warn('Chart.js not loaded yet or invalid, retrying in 500ms...');
            setTimeout(loadAdminStatsCharts, 500);
            return;
        }

        let dateFrom = $('#stats-date-from').val().trim();
        let dateTo = $('#stats-date-to').val().trim();

        let url = '/api/admin_data.php?action=chart_data';
        if (dateFrom) url += '&date_from=' + encodeURIComponent(dateFrom);
        if (dateTo) url += '&date_to=' + encodeURIComponent(dateTo);

        console.log('Loading charts with URL:', url);
        console.log('Chart.js version:', Chart.version);

        // Use fetch API instead of jQuery for better error handling
        fetch(url)
            .then(response => {
                console.log('Fetch response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status + ' ' + response.statusText);
                }
                return response.json();
            })
            .then(res => {
                console.log('Chart API response:', res);
                if (res.success && res.data) {
                    try {
                        // Destroy existing charts safely
                        if (window.adminStatsBarChart && typeof window.adminStatsBarChart.destroy === 'function') {
                            console.log('Destroying existing bar chart');
                            try {
                                window.adminStatsBarChart.destroy();
                            } catch (destroyError) {
                                console.warn('Error destroying bar chart:', destroyError);
                            }
                            window.adminStatsBarChart = null;
                        }
                        if (window.adminStatsPieChart && typeof window.adminStatsPieChart.destroy === 'function') {
                            console.log('Destroying existing pie chart');
                            try {
                                window.adminStatsPieChart.destroy();
                            } catch (destroyError) {
                                console.warn('Error destroying pie chart:', destroyError);
                            }
                            window.adminStatsPieChart = null;
                        }

                        // Get canvas elements
                        const barCanvas = document.getElementById('adminStatsBarChart');
                        const pieCanvas = document.getElementById('adminStatsPieChart');

                        console.log('Bar canvas found:', !!barCanvas);
                        console.log('Pie canvas found:', !!pieCanvas);

                        if (!barCanvas || !pieCanvas) {
                            console.error('Canvas elements not found');
                            showChartError('Không tìm thấy canvas elements');
                            return;
                        }

                        console.log('Creating bar chart with data:', res.data.bar);
                        // Bar Chart - simplified
                        let barChart = null;
                        try {
                            barChart = new Chart(barCanvas, {
                                type: 'bar',
                                data: {
                                    labels: res.data.bar.labels || [],
                                    datasets: [{
                                        label: 'Số giao dịch',
                                        data: res.data.bar.data || [],
                                        backgroundColor: '#3b82f6'
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false
                                }
                            });
                            window.adminStatsBarChart = barChart;
                            console.log('Bar chart created successfully');
                        } catch (barError) {
                            console.error('Error creating bar chart:', barError);
                            showChartError('Lỗi tạo biểu đồ cột: ' + barError.message);
                            return;
                        }

                        console.log('Creating pie chart with data:', res.data.pie);
                        // Pie Chart - simplified
                        let pieChart = null;
                        try {
                            pieChart = new Chart(pieCanvas, {
                                type: 'pie',
                                data: {
                                    labels: ['Thu nhập', 'Chi tiêu'],
                                    datasets: [{
                                        data: [res.data.pie.income || 0, res.data.pie.expense || 0],
                                        backgroundColor: ['#10b981', '#ef4444']
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            position: 'bottom'
                                        }
                                    }
                                }
                            });
                            window.adminStatsPieChart = pieChart;
                            console.log('Pie chart created successfully');
                        } catch (pieError) {
                            console.error('Error creating pie chart:', pieError);
                            showChartError('Lỗi tạo biểu đồ tròn: ' + pieError.message);
                            // Destroy bar chart if pie chart failed
                            if (barChart && typeof barChart.destroy === 'function') {
                                barChart.destroy();
                                window.adminStatsBarChart = null;
                            }
                            return;
                        }
                    } catch (chartError) {
                        console.error('Error creating charts:', chartError);
                        showChartError('Lỗi tạo biểu đồ: ' + chartError.message);
                    }
                } else {
                    console.error('Invalid API response:', res);
                    showChartError('API trả về dữ liệu không hợp lệ');
                }
            })
            .catch(error => {
                console.error('API request failed:', error);
                showChartError('Lỗi tải dữ liệu: ' + error.message);
            });
    }

    function showChartError(message) {
        // Show error message in canvas
        const barCanvas = document.getElementById('adminStatsBarChart');
        const pieCanvas = document.getElementById('adminStatsPieChart');

        if (barCanvas) {
            const ctx = barCanvas.getContext('2d');
            ctx.clearRect(0, 0, barCanvas.width, barCanvas.height);
            ctx.font = '14px Arial';
            ctx.fillStyle = '#ef4444';
            ctx.textAlign = 'center';
            ctx.fillText(message, barCanvas.width / 2, barCanvas.height / 2);
        }
        if (pieCanvas) {
            const ctx = pieCanvas.getContext('2d');
            ctx.clearRect(0, 0, pieCanvas.width, pieCanvas.height);
            ctx.font = '14px Arial';
            ctx.fillStyle = '#ef4444';
            ctx.textAlign = 'center';
            ctx.fillText(message, pieCanvas.width / 2, pieCanvas.height / 2);
        }
    }
</script>

<?php include 'partials/footer.php'; ?>