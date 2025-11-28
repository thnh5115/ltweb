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
                    <i class="fas fa-list mr-3 text-primary-600"></i>
                    Quản lý danh mục hệ thống
                </h2>
                <p class="text-muted">Quản lý danh mục cho toàn bộ người dùng</p>
            </div>
            <button class="btn btn-primary" onclick="openModal('addCategoryModal')">
                <i class="fas fa-plus mr-2"></i> Thêm danh mục
            </button>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 stagger-children">
            <div class="admin-stat-card">
                <div class="admin-stat-icon purple">
                    <i class="fas fa-list"></i>
                </div>
                <div class="admin-stat-content">
                    <span class="admin-stat-label">Tổng danh mục</span>
                    <span class="admin-stat-value" id="total-categories">0</span>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-icon green">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="admin-stat-content">
                    <span class="admin-stat-label">Thu nhập</span>
                    <span class="admin-stat-value" id="income-categories">0</span>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-icon red">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div class="admin-stat-content">
                    <span class="admin-stat-label">Chi tiêu</span>
                    <span class="admin-stat-value" id="expense-categories">0</span>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-icon blue">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="admin-stat-content">
                    <span class="admin-stat-label">Đang sử dụng</span>
                    <span class="admin-stat-value" id="used-categories">0</span>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2 relative">
                        <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="searchInput" class="form-control" style="padding-left: 2.75rem;"
                            placeholder="Tìm kiếm danh mục...">
                    </div>
                    <select id="filterType" class="form-control">
                        <option value="">Tất cả loại</option>
                        <option value="income">Thu nhập</option>
                        <option value="expense">Chi tiêu</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" id="category-list">
            <!-- Loaded via AJAX -->
            <div class="col-span-full flex justify-center py-12">
                <div class="spinner"></div>
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-plus-circle mr-2 text-primary-600"></i>
                Thêm danh mục hệ thống
            </h3>
            <button class="modal-close" onclick="closeModal('addCategoryModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addCategoryForm">
                <input type="hidden" name="action" value="add_category">

                <div class="form-group">
                    <label class="form-label">Tên danh mục</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Loại</label>
                    <select name="type" class="form-control" required>
                        <option value="expense">Chi tiêu</option>
                        <option value="income">Thu nhập</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Màu sắc</label>
                    <div class="grid grid-cols-8 gap-2">
                        <input type="radio" name="color" value="#EF4444" id="color1" class="hidden" checked>
                        <label for="color1"
                            class="w-10 h-10 rounded-lg cursor-pointer border-2 border-transparent hover:border-gray-400"
                            style="background: #EF4444;"></label>

                        <input type="radio" name="color" value="#F59E0B" id="color2" class="hidden">
                        <label for="color2"
                            class="w-10 h-10 rounded-lg cursor-pointer border-2 border-transparent hover:border-gray-400"
                            style="background: #F59E0B;"></label>

                        <input type="radio" name="color" value="#10B981" id="color3" class="hidden">
                        <label for="color3"
                            class="w-10 h-10 rounded-lg cursor-pointer border-2 border-transparent hover:border-gray-400"
                            style="background: #10B981;"></label>

                        <input type="radio" name="color" value="#3B82F6" id="color4" class="hidden">
                        <label for="color4"
                            class="w-10 h-10 rounded-lg cursor-pointer border-2 border-transparent hover:border-gray-400"
                            style="background: #3B82F6;"></label>

                        <input type="radio" name="color" value="#8B5CF6" id="color5" class="hidden">
                        <label for="color5"
                            class="w-10 h-10 rounded-lg cursor-pointer border-2 border-transparent hover:border-gray-400"
                            style="background: #8B5CF6;"></label>

                        <input type="radio" name="color" value="#EC4899" id="color6" class="hidden">
                        <label for="color6"
                            class="w-10 h-10 rounded-lg cursor-pointer border-2 border-transparent hover:border-gray-400"
                            style="background: #EC4899;"></label>

                        <input type="radio" name="color" value="#6B7280" id="color7" class="hidden">
                        <label for="color7"
                            class="w-10 h-10 rounded-lg cursor-pointer border-2 border-transparent hover:border-gray-400"
                            style="background: #6B7280;"></label>

                        <input type="radio" name="color" value="#14B8A6" id="color8" class="hidden">
                        <label for="color8"
                            class="w-10 h-10 rounded-lg cursor-pointer border-2 border-transparent hover:border-gray-400"
                            style="background: #14B8A6;"></label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Icon</label>
                    <select name="icon" class="form-control" required>
                        <option value="fa-shopping-cart">🛒 Mua sắm</option>
                        <option value="fa-utensils">🍽️ Ăn uống</option>
                        <option value="fa-home">🏠 Nhà cửa</option>
                        <option value="fa-car">🚗 Di chuyển</option>
                        <option value="fa-heart">❤️ Sức khỏe</option>
                        <option value="fa-graduation-cap">🎓 Giáo dục</option>
                        <option value="fa-film">🎬 Giải trí</option>
                        <option value="fa-gift">🎁 Quà tặng</option>
                        <option value="fa-briefcase">💼 Công việc</option>
                        <option value="fa-wallet">💰 Khác</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal('addCategoryModal')" class="btn btn-outline">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu danh mục</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        loadCategoryStats();
        loadCategories();

        $('#searchInput, #filterType').on('change keyup', function () {
            loadCategories();
        });

        $('#addCategoryForm').on('submit', function (e) {
            e.preventDefault();
            const payload = $(this).serializeArray().reduce((acc, cur) => {
                acc[cur.name] = cur.value;
                return acc;
            }, {});
            payload.action = 'admin_create_category';
            $.ajax({
                url: '/api/admin_data.php',
                method: 'POST',
                dataType: 'json',
                data: payload
            }).done(function (res) {
                if (res.success) {
                    showToast('success', res.message || 'Thêm danh mục thành công');
                    closeModal('addCategoryModal');
                    $('#addCategoryForm')[0].reset();
                    loadCategoryStats();
                    loadCategories();
                } else {
                    showToast('error', res.message || 'Không thành công');
                }
            }).fail(function () {
                showToast('error', 'Lỗi hệ thống');
            });
        });
    });

    function loadCategoryStats() {
        $.post('/api/admin_data.php', { action: 'admin_category_stats' }, function (res) {
            if (res.success) {
                $('#total-categories').text(res.data.total ?? 0);
                $('#income-categories').text(res.data.income ?? 0);
                $('#expense-categories').text(res.data.expense ?? 0);
                $('#used-categories').text(res.data.used ?? 0);
            }
        }).fail(function () {
            console.error('Failed to load category stats');
        });
    }

    function loadCategories() {
        const filters = {
            action: 'admin_get_categories',
            search: $('#searchInput').val(),
            type: $('#filterType').val()
        };

        $.ajax({
            url: '/api/admin_data.php',
            method: 'POST',
            dataType: 'json',
            data: filters
        }).done(function (res) {
            if (res.success) {
                const list = $('#category-list');
                list.empty();

                const items = (res.data && res.data.items) ? res.data.items : res.data || [];
                if (items.length === 0) {
                    list.html(`
                    <div class="col-span-full">
                        <div class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <h3>Chưa có danh mục</h3>
                            <p>Thêm danh mục hệ thống</p>
                        </div>
                    </div>
                `);
                    return;
                }

                items.forEach(c => {
                    const typeBadge = (c.type || '').toUpperCase() === 'INCOME'
                        ? '<span class="badge badge-success"><i class="fas fa-arrow-down mr-1"></i>Thu nhập</span>'
                        : '<span class="badge badge-danger"><i class="fas fa-arrow-up mr-1"></i>Chi tiêu</span>';

                    const statusLabel = (c.status || '').toUpperCase();
                    const statusBadge = statusLabel === 'ACTIVE'
                        ? '<span class="status-badge status-active">Đang dùng</span>'
                        : '<span class="status-badge status-banned">Ẩn</span>';

                    const html = `
                    <div class="card hover-lift entrance-fade">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white shadow-lg" style="background: ${c.color || '#3B6FD8'}">
                                <i class="fas ${c.icon || 'fa-tag'} text-2xl"></i>
                            </div>
                            <div class="flex gap-2">
                                <button class="btn btn-sm btn-outline" onclick="openEditCategory(${c.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="updateCategoryStatus(${c.id}, 'DELETED')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <h3 class="font-bold text-lg mb-2">${c.name}</h3>
                        <div class="flex items-center justify-between mb-3">
                            ${typeBadge}
                            ${statusBadge}
                        </div>
                        <div class="text-sm text-muted">
                            <i class="fas fa-users mr-1"></i> ${c.user_count || 0} người dùng
                        </div>
                    </div>
                `;
                    list.append(html);
                });
            } else {
                showToast('error', res.message || 'Tải danh mục thất bại');
            }
        }).fail(function () {
            showToast('error', 'Lỗi hệ thống');
        });
    }

    function openEditCategory(id) {
        // Optional: call detail API; tạm thời chỉ đổi trạng thái
        alert('Chức năng chỉnh sửa chưa được triển khai.');
    }

    function updateCategoryStatus(id, status) {
        if (!confirm('Bạn có chắc muốn cập nhật trạng thái danh mục?')) return;
        $.ajax({
            url: '/api/admin_data.php',
            method: 'POST',
            dataType: 'json',
            data: { action: 'admin_update_category_status', id, status }
        }).done(function (res) {
            if (res.success) {
                showToast('success', res.message || 'Cập nhật thành công');
                loadCategoryStats();
                loadCategories();
            } else {
                showToast('error', res.message || 'Cập nhật thất bại');
            }
        }).fail(function () {
            showToast('error', 'Lỗi hệ thống');
        });
    }

    function openModal(id) {
        $('#' + id).addClass('active').css('display', 'flex');
    }

    function closeModal(id) {
        $('#' + id).removeClass('active').css('display', 'none');
    }
</script>


<?php include 'partials/footer.php'; ?>
