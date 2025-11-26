<?php
require_once '../../config.php';
require_once '../../functions.php';
requireLogin();

include 'partials/header.php';
include 'partials/sidebar.php';
include 'partials/navbar.php';
?>

<div class="main-content">
    <div class="content-body">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-list mr-3 text-primary-600"></i>
                    Danh mục thu chi
                </h2>
                <p class="text-muted">Quản lý danh mục cho giao dịch của bạn</p>
            </div>
            <button class="btn btn-primary" onclick="openModal('addCategoryModal')">
                <i class="fas fa-plus mr-2"></i> Thêm danh mục
            </button>
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="category-list">
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
                Thêm danh mục mới
            </h3>
            <button class="modal-close" onclick="closeModal('addCategoryModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addCategoryForm">
                <input type="hidden" name="action" value="add_category">

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-tag mr-1"></i> Tên danh mục
                    </label>
                    <input type="text" name="name" class="form-control" placeholder="Ví dụ: Ăn uống" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-exchange-alt mr-1"></i> Loại
                    </label>
                    <select name="type" class="form-control" required>
                        <option value="expense">Chi tiêu</option>
                        <option value="income">Thu nhập</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-palette mr-1"></i> Màu sắc
                    </label>
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
                    <label class="form-label">
                        <i class="fas fa-icons mr-1"></i> Icon
                    </label>
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

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-dollar-sign mr-1"></i> Giới hạn chi tiêu (tùy chọn)
                    </label>
                    <input type="number" name="limit" class="form-control" placeholder="0">
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal('addCategoryModal')" class="btn btn-outline">
                        <i class="fas fa-times mr-2"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i> Lưu danh mục
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="editCategoryModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-edit mr-2 text-primary-600"></i>
                Chỉnh sửa danh mục
            </h3>
            <button class="modal-close" onclick="closeModal('editCategoryModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editCategoryForm">
                <input type="hidden" name="action" value="edit_category">
                <input type="hidden" name="id" id="edit-category-id">

                <div class="form-group">
                    <label class="form-label">Tên danh mục</label>
                    <input type="text" name="name" id="edit-name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Loại</label>
                    <select name="type" id="edit-type" class="form-control" required>
                        <option value="expense">Chi tiêu</option>
                        <option value="income">Thu nhập</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Giới hạn chi tiêu</label>
                    <input type="number" name="limit" id="edit-limit" class="form-control">
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal('editCategoryModal')" class="btn btn-outline">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        loadCategories();

        // Add Category
        $('#addCategoryForm').submit(function (e) {
            e.preventDefault();
            $.post('/api/data.php', $(this).serialize(), function (res) {
                if (res.success) {
                    showToast('success', res.message);
                    closeModal('addCategoryModal');
                    $('#addCategoryForm')[0].reset();
                    loadCategories();
                } else {
                    showToast('error', res.message);
                }
            });
        });

        // Edit Category
        $('#editCategoryForm').submit(function (e) {
            e.preventDefault();
            $.post('/api/data.php', $(this).serialize(), function (res) {
                if (res.success) {
                    showToast('success', res.message);
                    closeModal('editCategoryModal');
                    loadCategories();
                } else {
                    showToast('error', res.message);
                }
            });
        });

        // Filters
        $('#searchInput, #filterType').on('change keyup', function () {
            loadCategories();
        });

        // Color picker selection
        $('input[name="color"]').on('change', function () {
            $('label[for^="color"]').removeClass('border-primary-600');
            $('label[for="' + $(this).attr('id') + '"]').addClass('border-primary-600');
        });
    });

    function loadCategories() {
        const filters = {
            action: 'get_categories',
            search: $('#searchInput').val(),
            type: $('#filterType').val()
        };

        $.get('/api/data.php', filters, function (res) {
            if (res.success) {
                const list = $('#category-list');
                list.empty();

                if (res.data.length === 0) {
                    list.html(`
                    <div class="col-span-full">
                        <div class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <h3>Chưa có danh mục</h3>
                            <p>Thêm danh mục đầu tiên của bạn</p>
                        </div>
                    </div>
                `);
                    return;
                }

                res.data.forEach(c => {
                    const typeBadge = c.type === 'income'
                        ? '<span class="badge badge-success"><i class="fas fa-arrow-down mr-1"></i>Thu nhập</span>'
                        : '<span class="badge badge-danger"><i class="fas fa-arrow-up mr-1"></i>Chi tiêu</span>';

                    const html = `
                    <div class="card hover-lift cursor-pointer entrance-fade">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-16 h-16 rounded-xl flex items-center justify-center text-white shadow-lg" style="background: ${c.color}">
                                <i class="fas ${c.icon} text-2xl"></i>
                            </div>
                            <div class="flex gap-2">
                                <button class="btn btn-sm btn-outline" onclick="editCategory(${c.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteCategory(${c.id})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <h3 class="font-bold text-lg mb-3">${c.name}</h3>
                        <div class="flex items-center justify-between">
                            ${typeBadge}
                            <span class="text-sm text-muted">
                                ${c.limit > 0 ? 'Giới hạn: ' + formatMoney(c.limit) : 'Không giới hạn'}
                            </span>
                        </div>
                    </div>
                `;
                    list.append(html);
                });
            }
        });
    }

    function editCategory(id) {
        $.get('/api/data.php?action=get_category&id=' + id, function (res) {
            if (res.success) {
                const c = res.data;
                $('#edit-category-id').val(c.id);
                $('#edit-name').val(c.name);
                $('#edit-type').val(c.type);
                $('#edit-limit').val(c.limit);
                openModal('editCategoryModal');
            }
        });
    }

    function deleteCategory(id) {
        if (confirm('Bạn có chắc muốn xóa danh mục này?')) {
            $.post('/api/data.php', {
                action: 'delete_category',
                id: id
            }, function (res) {
                if (res.success) {
                    showToast('success', res.message);
                    loadCategories();
                } else {
                    showToast('error', res.message);
                }
            });
        }
    }

    function openModal(id) {
        $('#' + id).addClass('active').css('display', 'flex');
    }

    function closeModal(id) {
        $('#' + id).removeClass('active').css('display', 'none');
    }
</script>

<?php include 'partials/footer.php'; ?>