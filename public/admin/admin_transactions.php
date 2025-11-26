<?php
require_once '../../config.php';
require_once 'admin_functions.php';
requireAdminLogin();

include 'partials/header.php';
include 'partials/sidebar.php';
include 'partials/navbar.php';
?>

<div class="container">
    <h2 class="text-xl font-bold mb-6">Quản lý giao dịch toàn hệ thống</h2>

    <div class="card mb-6">
        <div class="p-4 border-b grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" class="form-control" placeholder="Tìm theo User, ID...">
            <select class="form-control">
                <option value="">Tất cả loại</option>
                <option value="income">Thu nhập</option>
                <option value="expense">Chi tiêu</option>
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
                        <th>ID</th>
                        <th>Người tạo</th>
                        <th>Loại</th>
                        <th>Danh mục</th>
                        <th>Số tiền</th>
                        <th>Ngày</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody id="transactions-list">
                    <!-- Loaded via AJAX -->
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="p-4 border-t flex justify-between items-center">
            <span class="text-sm text-muted">Hiển thị 1-20 trong 1250 giao dịch</span>
            <div class="flex gap-2">
                <button class="btn btn-outline btn-sm" disabled>Trước</button>
                <button class="btn btn-outline btn-sm">Sau</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        loadTransactions();
    });

    function loadTransactions() {
        $.get('/api/admin_data.php?action=get_transactions', function (res) {
            if (res.success) {
                const list = $('#transactions-list');
                list.empty();
                res.data.forEach(t => {
                    const amountClass = t.type === 'income' ? 'text-success' : 'text-danger';
                    const sign = t.type === 'income' ? '+' : '-';
                    const statusBadge = t.status === 'completed'
                        ? '<span class="status-badge status-active">Hoàn thành</span>'
                        : '<span class="status-badge status-banned">Nghi ngờ</span>';

                    const html = `
                    <tr>
                        <td>#${t.id}</td>
                        <td>
                            <div class="font-medium">${t.user}</div>
                        </td>
                        <td>${t.type === 'income' ? 'Thu' : 'Chi'}</td>
                        <td>${t.category}</td>
                        <td class="${amountClass} font-bold">${sign}${formatMoney(t.amount)}</td>
                        <td>${t.date}</td>
                        <td>${statusBadge}</td>
                        <td>
                            <button class="text-danger" title="Xóa"><i class="fas fa-trash"></i></button>
                            <button class="text-warning ml-2" title="Đánh dấu nghi ngờ"><i class="fas fa-flag"></i></button>
                        </td>
                    </tr>
                `;
                    list.append(html);
                });
            }
        });
    }
</script>

<?php include 'partials/footer.php'; ?>