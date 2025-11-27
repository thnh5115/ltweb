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
        <div class="p-4 border-b grid grid-cols-1 md:grid-cols-5 gap-4">
            <input type="text" id="searchInput" class="form-control" placeholder="Tìm theo user/email/ghi chú">
            <select id="typeFilter" class="form-control">
                <option value="">Tất cả loại</option>
                <option value="INCOME">Thu nhập</option>
                <option value="EXPENSE">Chi tiêu</option>
            </select>
            <select id="statusFilter" class="form-control">
                <option value="">Tất cả trạng thái</option>
                <option value="COMPLETED">Hoàn thành</option>
                <option value="PENDING">Đang xử lý</option>
                <option value="CANCELED">Đã hủy</option>
                <option value="FLAGGED">Cờ</option>
            </select>
            <input type="date" id="dateFrom" class="form-control" placeholder="Từ ngày">
            <input type="date" id="dateTo" class="form-control" placeholder="Đến ngày">
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
        $('#searchInput, #typeFilter, #statusFilter, #dateFrom, #dateTo').on('change keyup', function () {
            loadTransactions();
        });
    });

    function loadTransactions(page = 1) {
        const params = {
            action: 'admin_get_transactions',
            search: $('#searchInput').val(),
            type: $('#typeFilter').val(),
            status: $('#statusFilter').val(),
            date_from: $('#dateFrom').val(),
            date_to: $('#dateTo').val(),
            page: page,
            limit: 50
        };

        $.ajax({
            url: '/api/admin_data.php',
            method: 'POST',
            dataType: 'json',
            data: params
        }).done(function (res) {
            if (res.success) {
                const list = $('#transactions-list');
                list.empty();
                const items = res.data.items || [];

                if (items.length === 0) {
                    list.html('<tr><td colspan="8" class="text-center text-muted py-4">Không có giao dịch</td></tr>');
                    return;
                }

                items.forEach(t => {
                    const amountClass = t.type === 'INCOME' ? 'text-success' : 'text-danger';
                    const sign = t.type === 'INCOME' ? '+' : '-';
                    const status = (t.status || '').toUpperCase();
                    const statusBadge = status === 'COMPLETED'
                        ? '<span class="status-badge status-active">Hoàn thành</span>'
                        : status === 'PENDING'
                            ? '<span class="status-badge status-warning">Đang xử lý</span>'
                            : status === 'CANCELED'
                                ? '<span class="status-badge status-banned">Đã hủy</span>'
                                : '<span class="status-badge status-banned">Cờ</span>';

                    const html = `
                    <tr>
                        <td>#${t.id}</td>
                        <td>
                            <div class="font-medium">${t.user_name || ''}</div>
                            <div class="text-xs text-muted">${t.user_email || ''}</div>
                        </td>
                        <td>${t.type === 'INCOME' ? 'Thu' : 'Chi'}</td>
                        <td>${t.category_name || '-'}</td>
                        <td class="${amountClass} font-bold">${sign}${formatMoney(t.amount || 0)}</td>
                        <td>${t.transaction_date || t.created_at || ''}</td>
                        <td>${statusBadge}</td>
                        <td>
                            <button class="text-warning ml-2" title="Gắn cờ" onclick="updateTxnStatus(${t.id}, 'FLAGGED')"><i class="fas fa-flag"></i></button>
                        </td>
                    </tr>
                `;
                    list.append(html);
                });
            } else {
                alert(res.message || 'Tải giao dịch thất bại');
            }
        }).fail(function () {
            alert('Lỗi hệ thống');
        });
    }

    function updateTxnStatus(id, status) {
        $.ajax({
            url: '/api/admin_data.php',
            method: 'POST',
            dataType: 'json',
            data: { action: 'admin_update_transaction_status', id, status }
        }).done(function (res) {
            if (res.success) {
                loadTransactions();
            } else {
                alert(res.message || 'Cập nhật thất bại');
            }
        }).fail(function () {
            alert('Lỗi hệ thống');
        });
    }
</script>


<?php include 'partials/footer.php'; ?>
