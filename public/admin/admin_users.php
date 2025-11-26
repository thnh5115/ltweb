<?php
require_once '../../config.php';
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
            <button class="btn btn-primary" onclick="openModal('addUserModal')">
                <i class="fas fa-user-plus mr-2"></i> Thêm người dùng
            </button>
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
                <button class="btn btn-outline w-full justify-start">
                    <i class="fas fa-key mr-2"></i> Reset mật khẩu
                </button>
                <button class="btn btn-outline w-full justify-start text-warning">
                    <i class="fas fa-ban mr-2"></i> Khóa tài khoản
                </button>
                <button class="btn btn-outline w-full justify-start text-danger">
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

<script>
    $(document).ready(function () {
        loadUsers();
        loadStats();

        $('#searchInput, #filterStatus').on('change keyup', function () {
            loadUsers();
        });
    });

    function loadStats() {
        $.get('/api/admin_data.php?action=user_stats', function (res) {
            if (res.success) {
                $('#total-users').text(res.data.total);
                $('#active-users').text(res.data.active);
                $('#new-users').text(res.data.new);
                $('#banned-users').text(res.data.banned);
            }
        });
    }

    function loadUsers() {
        const filters = {
            action: 'get_users',
            search: $('#searchInput').val(),
            status: $('#filterStatus').val(),
            date: $('#filterDate').val()
        };

        $.get('/api/admin_data.php', filters, function (res) {
            if (res.success) {
                const list = $('#users-list');
                list.empty();

                if (res.data.length === 0) {
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

                $('#showing-count').text(res.data.length);

                res.data.forEach(u => {
                    const statusBadge = u.status === 'active'
                        ? '<span class="status-badge status-active"><i class="fas fa-check-circle mr-1"></i>Hoạt động</span>'
                        : '<span class="status-badge status-banned"><i class="fas fa-ban mr-1"></i>Bị khóa</span>';

                    const html = `
                    <tr class="transition-all">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-primary flex items-center justify-center text-white shadow-md">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <p class="font-medium">${u.name}</p>
                                    <p class="text-xs text-muted">ID: #${u.id}</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted">${u.email}</td>
                        <td class="font-semibold">${u.transactions} giao dịch</td>
                        <td class="font-mono font-bold text-danger">${formatMoney(u.expense)}</td>
                        <td class="text-sm">${u.created_at}</td>
                        <td>${statusBadge}</td>
                        <td class="text-center">
                            <div class="flex justify-center gap-2">
                                <button class="btn btn-sm btn-outline" onclick='openUserModal(${JSON.stringify(u)})' title="Chi tiết">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteUser(${u.id})" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                    list.append(html);
                });
            }
        });
    }

    function openUserModal(user) {
        $('#modal-user-id').text('#' + user.id);
        $('#modal-user-name').text(user.name);
        $('#modal-user-email').text(user.email);
        $('#modal-user-transactions').text(user.transactions);
        $('#modal-user-expense').text(formatMoney(user.expense));
        $('#modal-user-created').text(user.created_at);
        $('#userModal').addClass('active').css('display', 'flex');
    }

    function deleteUser(id) {
        if (confirm('Bạn có chắc muốn xóa người dùng này?')) {
            $.post('/api/admin_data.php', {
                action: 'delete_user',
                id: id
            }, function (res) {
                if (res.success) {
                    showToast('success', res.message);
                    loadUsers();
                    loadStats();
                } else {
                    showToast('error', res.message);
                }
            });
        }
    }

    function closeModal(id) {
        $('#' + id).removeClass('active').css('display', 'none');
    }
</script>

<?php include 'partials/footer.php'; ?>