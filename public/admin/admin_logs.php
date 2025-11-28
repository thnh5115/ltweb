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
        <div class="p-4 border-b grid grid-cols-1 md:grid-cols-5 gap-4" id="logFilterForm">
            <input type="text" class="form-control" id="logSearch" placeholder="Tìm theo admin, IP, ghi chú">
            <select class="form-control" id="logAction">
                <option value="">Tất cả hành động</option>
                <option value="ADMIN_LOGIN">Đăng nhập</option>
                <option value="CREATE_USER">Tạo user</option>
                <option value="UPDATE_USER_STATUS">Đổi trạng thái user</option>
                <option value="UPDATE_USER_ROLE">Đổi quyền</option>
                <option value="UPDATE_SETTINGS">Đổi cấu hình</option>
                <option value="REPLY_TICKET">Trả lời ticket</option>
            </select>
            <input type="date" class="form-control" id="dateFrom">
            <input type="date" class="form-control" id="dateTo">
            <button class="btn btn-primary w-full" id="btnFilter"><i class="fas fa-filter mr-2"></i> Lọc</button>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Admin</th>
                        <th>Hành động</th>
                        <th>Đối tượng</th>
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
            <span class="text-sm text-muted" id="logSummary">-</span>
            <div class="flex gap-2">
                <button class="btn btn-outline btn-sm" id="btnPrev">Trước</button>
                <button class="btn btn-outline btn-sm" id="btnNext">Sau</button>
            </div>
        </div>
    </div>
</div>

<script>
    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    let currentPage = 1;
    const pageLimit = 20;

    $(document).ready(function () {
        loadLogs();

        $('#btnFilter').on('click', function (e) {
            e.preventDefault();
            currentPage = 1;
            loadLogs();
        });

        $('#logFilterForm input, #logFilterForm select').on('keyup change', function (e) {
            if (e.keyCode === 13) {
                currentPage = 1;
                loadLogs();
            }
        });

        $('#btnPrev').on('click', function () {
            if (currentPage > 1) {
                currentPage--;
                loadLogs();
            }
        });

        $('#btnNext').on('click', function () {
            currentPage++;
            loadLogs(true);
        });
    });

    function loadLogs(isNext = false) {
        const params = {
            action: 'admin_get_logs',
            page: currentPage,
            limit: pageLimit,
            search: $('#logSearch').val(),
            log_action: $('#logAction').val(),
            date_from: $('#dateFrom').val(),
            date_to: $('#dateTo').val()
        };

        $.ajax({
            url: '/api/admin_data.php',
            method: 'GET',
            dataType: 'json',
            data: params
        }).done(function (res) {
            if (!res.success) {
                alert(res.message || 'Không tải được log');
                if (isNext) {
                    currentPage--;
                }
                return;
            }

            const list = $('#logs-list');
            list.empty();
            const items = res.data.items || [];
            const pagination = res.data.pagination || { page: currentPage, total: 0 };

            if (!items.length) {
                list.html(`
                    <tr>
                        <td colspan="6" class="text-center text-muted py-8">
                            <i class="fas fa-inbox mb-2 text-2xl"></i>
                            <div>Không có log nào</div>
                        </td>
                    </tr>
                `);
            } else {
                items.forEach(log => {
                    const target = log.target_type ? `${escapeHtml(log.target_type)} #${escapeHtml(log.target_id || '-')}` : '-';
                    const html = `
                    <tr>
                        <td class="text-muted text-sm">${escapeHtml(log.time)}</td>
                        <td>
                            <div class="font-medium">${escapeHtml(log.user)}</div>
                            <div class="text-xs text-muted">${escapeHtml(log.email || '')}</div>
                        </td>
                        <td><span class="badge badge-warning">${escapeHtml(log.action)}</span></td>
                        <td class="text-sm">${target}</td>
                        <td class="text-sm font-mono">${escapeHtml(log.ip)}</td>
                        <td>${escapeHtml(log.note)}</td>
                    </tr>
                `;
                    list.append(html);
                });
            }

            const total = pagination.total || 0;
            const start = total ? ((pagination.page - 1) * pageLimit + 1) : 0;
            const end = Math.min(total, pagination.page * pageLimit);
            $('#logSummary').text(`Hiển thị ${start}-${end} / ${total} log`);

            $('#btnPrev').prop('disabled', pagination.page <= 1);
            $('#btnNext').prop('disabled', end >= total);
        }).fail(function () {
            alert('Lỗi kết nối');
            if (isNext) {
                currentPage--;
            }
        });
    }
</script>

<?php include 'partials/footer.php'; ?>