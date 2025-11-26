<?php
require_once '../../config.php';
require_once 'admin_functions.php';
requireAdminLogin();

include 'partials/header.php';
include 'partials/sidebar.php';
include 'partials/navbar.php';
?>

<div class="container">
    <h2 class="text-xl font-bold mb-6">Nhật ký hệ thống</h2>

    <div class="card mb-6">
        <div class="p-4 border-b grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" class="form-control" placeholder="Tìm theo User, IP...">
            <select class="form-control">
                <option value="">Tất cả hành động</option>
                <option value="login">Login</option>
                <option value="delete">Delete</option>
                <option value="update">Update</option>
            </select>
            <input type="date" class="form-control">
            <button class="btn btn-primary w-full"><i class="fas fa-filter mr-2"></i> Lọc</button>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>User</th>
                        <th>Hành động</th>
                        <th>IP</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody id="logs-list">
                    <!-- Loaded via AJAX -->
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="p-4 border-t flex justify-between items-center">
            <span class="text-sm text-muted">Hiển thị 1-20 trong 500 logs</span>
            <div class="flex gap-2">
                <button class="btn btn-outline btn-sm" disabled>Trước</button>
                <button class="btn btn-outline btn-sm">Sau</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        loadLogs();
    });

    function loadLogs() {
        $.get('/api/admin_data.php?action=get_logs', function (res) {
            if (res.success) {
                const list = $('#logs-list');
                list.empty();
                res.data.forEach(log => {
                    const html = `
                    <tr>
                        <td class="text-muted text-sm">${log.time}</td>
                        <td class="font-medium">${log.user}</td>
                        <td><span class="badge badge-warning">${log.action}</span></td>
                        <td class="text-sm font-mono">${log.ip}</td>
                        <td>${log.note}</td>
                    </tr>
                `;
                    list.append(html);
                });
            }
        });
    }
</script>

<?php include 'partials/footer.php'; ?>