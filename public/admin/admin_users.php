<?php
require_once '../../config.php';
require_once '../../functions.php';
require_once 'admin_functions.php';
requireAdminLogin();

include 'partials/header.php';
include 'partials/sidebar.php';
include 'partials/navbar.php';
?>



<!-- Page Header -->
<div class="flex justify-between items-center mb-8">
    <div>
        <h2 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="fas fa-users mr-3 text-primary-600"></i>
            Quản lý người dùng
        </h2>
        <p class="text-muted">Quản lý tất cả người dùng trong hệ thống</p>
    </div>
    <div class="flex gap-3">
        <button class="btn btn-outline" onclick="openModal('broadcastModal')">
            <i class="fas fa-bullhorn mr-2"></i> Gửi thông báo chung
        </button>
        <button class="btn btn-primary" onclick="openModal('addUserModal')">
            <i class="fas fa-user-plus mr-2"></i> Thêm người dùng
        </button>
    </div>
</div>

<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 stagger-children">
    <div class="admin-stat-card">
        <div class="admin-stat-icon blue">
            <i class="fas fa-users"></i>
        </div>
        <div class="admin-stat-content">
            <span class="admin-stat-label">Tổng người dùng</span>
            <span class="admin-stat-value" id="total-users">0</span>
            <span class="admin-stat-change positive">
                <i class="fas fa-arrow-up"></i> +12% tháng này
            </span>
        </div>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-icon green">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="admin-stat-content">
            <span class="admin-stat-label">Hoạt động</span>
            <span class="admin-stat-value" id="active-users">0</span>
            <span class="admin-stat-change positive">
                <i class="fas fa-check"></i> Online
            </span>
        </div>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-icon orange">
            <i class="fas fa-user-clock"></i>
        </div>
        <div class="admin-stat-content">
            <span class="admin-stat-label">Mới tháng này</span>
            <span class="admin-stat-value" id="new-users">0</span>
            <span class="admin-stat-change positive">
                <i class="fas fa-plus"></i> Tăng trưởng
            </span>
        </div>
    </div>

    <div class="admin-stat-card">
        <div class="admin-stat-icon red">
            <i class="fas fa-user-slash"></i>
        </div>
        <div class="admin-stat-content">
            <span class="admin-stat-label">Bị khóa</span>
            <span class="admin-stat-value" id="banned-users">0</span>
            <span class="admin-stat-change negative">
                <i class="fas fa-ban"></i> Cảnh báo
            </span>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-6">
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="md:col-span-2 relative">
                <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="searchInput" class="form-control" style="padding-left: 2.75rem;"
                    placeholder="Tìm kiếm người dùng...">
            </div>
            <select id="filterStatus" class="form-control">
                <option value="">Tất cả trạng thái</option>
                <option value="active">Hoạt động</option>
                <option value="banned">Bị khóa</option>
                <option value="inactive">Chưa kích hoạt</option>
            </select>
            <input type="date" id="filterDate" class="form-control" placeholder="Từ ngày">
            <button class="btn btn-primary" onclick="loadUsers()">
                <i class="fas fa-filter mr-2"></i> Lọc
            </button>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="admin-card entrance-fade">
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Người dùng</th>
                    <th>Email</th>
                    <th>Giao dịch</th>
                    <th>Chi tiêu</th>
                    <th>Ngày tạo</th>
                    <th>Trạng thái</th>
                    <th class="text-center">Hành động</th>
                </tr>
            </thead>
            <tbody id="users-list">
                <tr>
                    <td colspan="7">
                        <div class="flex justify-center py-12">
                            <div class="spinner"></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="p-6 border-t">
        <div class="flex justify-between items-center">
            <span class="text-sm text-muted">Hiển thị <span id="showing-count">0</span> người dùng</span>
            <div class="pagination">
                <button class="pagination-btn" id="prevPage" disabled>
                    <i class="fas fa-chevron-left"></i> Trước
                </button>
                <button class="pagination-btn active">1</button>
                <button class="pagination-btn">2</button>
                <button class="pagination-btn">3</button>
                <button class="pagination-btn" id="nextPage">
                    Sau <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<!-- User Detail Modal -->
<div id="userModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-user-circle mr-2 text-primary-600"></i>
                Chi tiết người dùng
            </h3>
            <button class="modal-close" onclick="closeModal('userModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="text-center mb-6">
                <div
                    class="w-24 h-24 mx-auto rounded-full bg-gradient-primary flex items-center justify-center text-white text-4xl shadow-xl mb-4">
                    <i class="fas fa-user"></i>
                </div>
                <h4 class="text-xl font-bold mb-1" id="modal-user-name"></h4>
                <p class="text-muted" id="modal-user-email"></p>
            </div>

            <div class="space-y-3 mb-6">
                <div class="flex justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-muted">ID người dùng</span>
                    <span class="font-semibold" id="modal-user-id">#0000</span>
                </div>
                <div class="flex justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-muted">Tổng giao dịch</span>
                    <span class="font-semibold" id="modal-user-transactions">0</span>
                </div>
                <div class="flex justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-muted">Tổng chi tiêu</span>
                    <span class="font-semibold font-mono" id="modal-user-expense">0 đ</span>
                </div>
                <div class="flex justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-muted">Ngày tham gia</span>
                    <span class="font-semibold" id="modal-user-created">-</span>
                </div>
            </div>

            <div class="space-y-2">
                <button class="btn btn-outline w-full justify-start" onclick="resetPassword()">
                    <i class="fas fa-key mr-2"></i> Reset mật khẩu
                </button>
                <button id="banUnbanBtn" class="btn btn-outline w-full justify-start text-warning" onclick="toggleUserStatus()">
                    <i class="fas fa-ban mr-2"></i> Khóa tài khoản
                </button>
                <button class="btn btn-outline w-full justify-start text-danger" onclick="deleteUser()">
                    <i class="fas fa-trash mr-2"></i> Xóa người dùng
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div id="addUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-user-plus mr-2 text-primary-600"></i>
                Thêm người dùng mới
            </h3>
            <button class="modal-close" onclick="closeModal('addUserModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addUserForm">
                <div class="form-group">
                    <label class="form-label">Tên người dùng</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal('addUserModal')" class="btn btn-outline">Hủy</button>
                    <button type="submit" class="btn btn-primary">Tạo tài khoản</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Broadcast Notification Modal -->
<div id="broadcastModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-bullhorn mr-2 text-primary-600"></i>
                Gửi thông báo đến người dùng
            </h3>
            <button class="modal-close" onclick="closeModal('broadcastModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="broadcastForm">
                <div class="form-group">
                    <label class="form-label">Loại thông báo</label>
                    <select id="broadcastType" name="type" class="form-control">
                        <option value="info">Thông tin</option>
                        <option value="success">Thành công</option>
                        <option value="warning">Cảnh báo</option>
                        <option value="error">Lỗi</option>
                        <option value="reminder">Nhắc nhở</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tiêu đề</label>
                    <input type="text" id="broadcastTitle" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nội dung</label>
                    <textarea id="broadcastMessage" name="message" class="form-control" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Link đính kèm (tùy chọn)</label>
                    <input type="url" id="broadcastLink" name="link_url" class="form-control" placeholder="https://...">
                </div>
                <p class="text-sm text-muted mb-4">
                    Thông báo sẽ được gửi đến toàn bộ người dùng đang hoạt động.
                </p>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeModal('broadcastModal')" class="btn btn-outline">Hủy</button>
                    <button type="submit" class="btn btn-primary">Gửi thông báo</button>
                </div>
            </form>
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

    $(document).ready(function () {
        loadUsers();

        $('#searchInput, #filterStatus, #filterDate').on('change keyup', function () {
            loadUsers();
        });

        $('#addUserForm').on('submit', function (e) {
            e.preventDefault();
            addUser();
        });

        $('#broadcastForm').on('submit', function (e) {
            e.preventDefault();
            sendBroadcastNotification();
        });
    });

    function renderStats(summary) {
        if (!summary) return;
        $('#total-users').text(summary.total ?? 0);
        $('#active-users').text(summary.active ?? 0);
        $('#new-users').text(summary.new ?? 0);
        $('#banned-users').text(summary.banned ?? 0);
    }

    function loadUsers() {
        const filters = {
            action: 'admin_get_users',
            search: $('#searchInput').val(),
            status: $('#filterStatus').val(),
            date: $('#filterDate').val(),
            page: 1,
            limit: 50
        };

        $.ajax({
            url: '/api/admin_data.php',
            method: 'POST',
            dataType: 'json',
            data: filters
        }).done(function (res) {
            if (res.success) {
                const list = $('#users-list');
                list.empty();

                renderStats(res.data.summary);

                const items = res.data.items || [];
                if (items.length === 0) {
                    list.html(`
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-users-slash"></i>
                                <h3>Không tìm thấy người dùng</h3>
                                <p>Thử thay đổi bộ lọc</p>
                            </div>
                        </td>
                    </tr>
                `);
                    return;
                }

                $('#showing-count').text(items.length);

                items.forEach(u => {
                    const status = (u.status || '').toUpperCase();
                    let statusBadge = '<span class="status-badge"><i class="fas fa-question mr-1"></i>Không xác định</span>';
                    if (status === 'ACTIVE') {
                        statusBadge = '<span class="status-badge status-active"><i class="fas fa-check-circle mr-1"></i>Hoạt động</span>';
                    } else if (status === 'BANNED') {
                        statusBadge = '<span class="status-badge status-banned"><i class="fas fa-ban mr-1"></i>Bị khóa</span>';
                    } else if (status === 'INACTIVE') {
                        statusBadge = '<span class="status-badge status-pending"><i class="fas fa-clock mr-1"></i>Chưa kích hoạt</span>';
                    }

                    const nextStatus = status === 'ACTIVE' ? 'BANNED' : 'ACTIVE';
                    const toggleLabel = status === 'ACTIVE' ? 'Khóa' : 'Mở khóa';

                    const html = `
                    <tr class="transition-all">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-primary flex items-center justify-center text-white shadow-md">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <p class="font-medium">${escapeHtml(u.fullname || u.name)}</p>
                                    <p class="text-xs text-muted">ID: #${u.id}</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted">${escapeHtml(u.email)}</td>
                        <td class="font-semibold">${u.transactions || 0} giao dịch</td>
                        <td class="font-mono font-bold text-danger">${formatMoney(u.expense || 0)}</td>
                        <td class="text-sm">${u.created_at}</td>
                        <td>${statusBadge}</td>
                        <td class="text-center">
                            <div class="flex justify-center gap-2">
                                <button class="btn btn-sm btn-outline" onclick='openUserModal(${u.id})' title="Chi tiết">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline" onclick="updateUserRole(${u.id}, '${u.role === 'ADMIN' ? 'USER' : 'ADMIN'}')" title="Đổi quyền">
                                    <i class="fas fa-user-shield"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="updateUserStatus(${u.id}, '${nextStatus}')" title="${toggleLabel}">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                    list.append(html);
                });
            } else {
                alert(res.message || 'Tải danh sách thất bại');
            }
        }).fail(function () {
            alert('Lỗi hệ thống khi tải danh sách');
        });
    }

    function openUserModal(userId) {
        // Show loading in modal
        $('#modal-user-id').text('...');
        $('#modal-user-name').text('Đang tải...');
        $('#modal-user-email').text('');
        $('#modal-user-transactions').text('...');
        $('#modal-user-expense').text('...');
        $('#modal-user-created').text('...');
        $('#userModal').data('user-id', userId).addClass('active').css('display', 'flex');

        $.ajax({
            url: '/api/admin_data.php',
            method: 'POST',
            dataType: 'json',
            data: { action: 'admin_get_user_detail', id: userId }
        }).done(function (res) {
            if (res.success) {
                const user = res.data;
                $('#modal-user-id').text('#' + user.id);
                $('#modal-user-name').text(user.fullname || user.name);
                $('#modal-user-email').text(user.email);
                $('#modal-user-transactions').text(user.transactions);
                $('#modal-user-expense').text(formatMoney(user.expense));
                $('#modal-user-created').text(user.created_at);
                
                // Update ban/unban button based on status
                const status = user.status || 'ACTIVE';
                $('#userModal').data('user-status', status);
                updateBanUnbanButton(status);
            } else {
                $('#modal-user-name').text('Lỗi tải dữ liệu');
                $('#modal-user-email').text(res.message || 'Không thể tải thông tin người dùng');
                alert(res.message || 'Không lấy được thông tin người dùng');
            }
        }).fail(function () {
            $('#modal-user-name').text('Lỗi hệ thống');
            $('#modal-user-email').text('Không thể kết nối đến server');
            alert('Lỗi hệ thống');
        });
    }

    function updateUserStatus(id, status) {
        if (!id || !status) return;
        const message = status === 'BANNED' ? 'Xác nhận khóa tài khoản này?' : 'Mở khóa tài khoản này?';
        if (!confirm(message)) {
            return;
        }

        $.ajax({
            url: '/api/admin_data.php',
            method: 'POST',
            dataType: 'json',
            data: { action: 'admin_update_user_status', id, status }
        }).done(function (res) {
            if (res.success) {
                loadUsers();
                // Update modal status if modal is open
                const modalUserId = $('#userModal').data('user-id');
                if (modalUserId == id) {
                    $('#userModal').data('user-status', status);
                    updateBanUnbanButton(status);
                }
            } else {
                alert(res.message || 'Cập nhật trạng thái thất bại');
            }
        }).fail(function () {
            alert('Lỗi hệ thống');
        });
    }

    function updateUserRole(id, role) {
        $.ajax({
            url: '/api/admin_data.php',
            method: 'POST',
            dataType: 'json',
            data: { action: 'admin_update_user_role', id, role }
        }).done(function (res) {
            if (res.success) {
                loadUsers();
            } else {
                alert(res.message || 'Cập nhật quyền thất bại');
            }
        }).fail(function () {
            alert('Lỗi hệ thống');
        });
    }

    function addUser() {
        const submitBtn = $('#addUserForm button[type="submit"]');
        submitBtn.prop('disabled', true).text('Đang tạo...');

        const payload = {
            action: 'admin_create_user',
            name: $('#addUserForm input[name="name"]').val().trim(),
            email: $('#addUserForm input[name="email"]').val().trim(),
            password: $('#addUserForm input[name="password"]').val()
        };

        $.ajax({
            url: '/api/admin_data.php',
            method: 'POST',
            dataType: 'json',
            data: payload
        }).done(function (res) {
            if (res.success) {
                alert('Tạo người dùng thành công!');
                $('#addUserForm')[0].reset();
                closeModal('addUserModal');
                loadUsers(); // Reload list
            } else {
                alert(res.message || 'Không thể tạo người dùng');
            }
        }).fail(function () {
            alert('Lỗi hệ thống khi tạo người dùng');
        }).always(function () {
            submitBtn.prop('disabled', false).text('Tạo tài khoản');
        });
    }

    function sendBroadcastNotification() {
        const submitBtn = $('#broadcastForm button[type="submit"]');
        submitBtn.prop('disabled', true).text('Đang gửi...');

        const payload = {
            action: 'admin_send_notification',
            scope: 'all',
            type: $('#broadcastType').val(),
            title: $('#broadcastTitle').val(),
            message: $('#broadcastMessage').val(),
            link_url: $('#broadcastLink').val()
        };

        $.ajax({
            url: '/api/admin_data.php',
            method: 'POST',
            dataType: 'json',
            data: payload
        }).done(function (res) {
            if (res.success) {
                const count = res.data && res.data.recipients ? res.data.recipients : 0;
                showToast('success', `Đã gửi thông báo tới ${count} người dùng`);
                $('#broadcastForm')[0].reset();
                closeModal('broadcastModal');
            } else {
                showToast('error', res.message || 'Không thể gửi thông báo');
            }
        }).fail(function () {
            showToast('error', 'Lỗi hệ thống khi gửi thông báo');
        }).always(function () {
            submitBtn.prop('disabled', false).text('Gửi thông báo');
        });
    }

    function openModal(id) {
        $('#' + id).addClass('active').css('display', 'flex');
    }

    function closeModal(id) {
        $('#' + id).removeClass('active').css('display', 'none');
    }

    function resetPassword() {
        const id = $('#userModal').data('user-id');
        if (!id) return;
        if (!confirm('Xác nhận reset mật khẩu cho người dùng này? Mật khẩu mới sẽ được hiển thị.')) return;

        $.ajax({
            url: '/api/admin_data.php',
            method: 'POST',
            dataType: 'json',
            data: { action: 'admin_reset_password', id }
        }).done(function (res) {
            if (res.success) {
                alert('Mật khẩu mới: ' + (res.data.new_password || 'Không có'));
                closeModal('userModal');
                loadUsers();
            } else {
                alert(res.message || 'Reset mật khẩu thất bại');
            }
        }).fail(function () {
            alert('Lỗi hệ thống');
        });
    }

    function updateBanUnbanButton(status) {
        const btn = $('#banUnbanBtn');
        if (status === 'BANNED') {
            btn.html('<i class="fas fa-unlock mr-2"></i> Mở khóa tài khoản');
            btn.removeClass('text-warning').addClass('text-success');
        } else {
            btn.html('<i class="fas fa-ban mr-2"></i> Khóa tài khoản');
            btn.removeClass('text-success').addClass('text-warning');
        }
    }

    function toggleUserStatus() {
        const id = $('#userModal').data('user-id');
        const currentStatus = $('#userModal').data('user-status');
        if (!id) return;
        
        const newStatus = currentStatus === 'BANNED' ? 'ACTIVE' : 'BANNED';
        const actionText = newStatus === 'BANNED' ? 'khóa' : 'mở khóa';
        
        if (!confirm(`Xác nhận ${actionText} tài khoản này?`)) return;
        
        updateUserStatus(id, newStatus);
    }

    function banUser() {
        const id = $('#userModal').data('user-id');
        if (!id) return;
        updateUserStatus(id, 'BANNED');
    }

    function deleteUser() {
        const id = $('#userModal').data('user-id');
        if (!id) return;
        if (!confirm('Xác nhận xóa người dùng này? Hành động không thể hoàn tác.')) return;

        $.ajax({
            url: '/api/admin_data.php',
            method: 'POST',
            dataType: 'json',
            data: { action: 'admin_delete_user', id }
        }).done(function (res) {
            if (res.success) {
                alert('Xóa người dùng thành công');
                closeModal('userModal');
                loadUsers();
            } else {
                alert(res.message || 'Xóa thất bại');
            }
        }).fail(function () {
            alert('Lỗi hệ thống');
        });
    }
</script>


<?php include 'partials/footer.php'; ?>