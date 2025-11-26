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
        <div class="p-4 border-b flex gap-4 items-center">
            <label class="font-medium">Thời gian:</label>
            <select class="form-control w-auto">
                <option>Tháng này</option>
                <option>Tháng trước</option>
                <option>Năm 2023</option>
            </select>
            <button class="btn btn-primary">Xem báo cáo</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 p-6">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-muted mb-1">Tổng thu</p>
                <h3 class="text-xl font-bold text-success">60.000.000 đ</h3>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-muted mb-1">Tổng chi</p>
                <h3 class="text-xl font-bold text-danger">45.000.000 đ</h3>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-muted mb-1">Tổng giao dịch</p>
                <h3 class="text-xl font-bold text-primary">1,250</h3>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-muted mb-1">User hoạt động</p>
                <h3 class="text-xl font-bold text-blue-600">120</h3>
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
        loadAdminStatsCharts();
    });

    function loadAdminStatsCharts() {
        $.get('/api/admin_data.php?action=chart_data', function (res) {
            if (res.success) {
                // Bar Chart
                new Chart(document.getElementById('adminStatsBarChart'), {
                    type: 'bar',
                    data: {
                        labels: res.data.bar.labels,
                        datasets: [{
                            label: 'Số giao dịch',
                            data: res.data.bar.data,
                            backgroundColor: '#3b82f6'
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });

                // Pie Chart
                new Chart(document.getElementById('adminStatsPieChart'), {
                    type: 'pie',
                    data: {
                        labels: ['Thu nhập', 'Chi tiêu'],
                        datasets: [{
                            data: [res.data.pie.income, res.data.pie.expense],
                            backgroundColor: ['#10b981', '#ef4444'],
                            borderWidth: 0
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false }
                });
            }
        });
    }
</script>

<?php include 'partials/footer.php'; ?>