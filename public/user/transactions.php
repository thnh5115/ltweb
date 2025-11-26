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
                    <i class="fas fa-exchange-alt mr-3 text-primary-600"></i>
                    Quản lý giao dịch
                </h2>
                <p class="text-muted">Theo dõi và quản lý tất cả giao dịch của bạn</p>
            </div>
            <button class="btn btn-primary" onclick="openModal('addTransactionModal')">
                <i class="fas fa-plus mr-2"></i> Thêm giao dịch
            </button>
        </div>

        <!-- Filter Bar -->
        <div class="card mb-6">
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="md:col-span-2">
                        <div class="relative">
                            <i
                                class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="searchInput" class="form-control" style="padding-left: 2.75rem;"
                                placeholder="Tìm kiếm giao dịch...">
                        </div>
                    </div>
                    <select id="filterCategory" class="form-control">
                        <option value="">Tất cả danh mục</option>
                        <!-- Populate via JS -->
                    </select>
                    <select id="filterType" class="form-control">
                        <option value="">Tất cả loại</option>
                        <option value="income">Thu nhập</option>
                        <option value="expense">Chi tiêu</option>
                    </select>
                    <input type="date" id="filterDate" class="form-control">
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card entrance-fade">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Ngày</th>
                            <th>Danh mục</th>
                            <th>Ghi chú</th>
                            <th style="width: 100px;">Loại</th>
                            <th class="text-right" style="width: 180px;">Số tiền</th>
                            <th class="text-center" style="width: 120px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody id="transaction-list">
                        <!-- Loaded via AJAX -->
                        <tr>
                            <td colspan="6">
                                <div class="flex justify-center items-center py-12">
                                    <div class="spinner"></div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-6 border-t flex justify-between items-center">
                <span class="text-sm text-muted">Hiển thị <span id="showing-count">0</span> giao dịch</span>
                <div class="flex gap-2">
                    <button class="btn btn-outline btn-sm" id="prevPage" disabled>
                        <i class="fas fa-chevron-left mr-1"></i> Trước
                    </button>
                    <button class="btn btn-outline btn-sm" id="nextPage">
                        Sau <i class="fas fa-chevron-right ml-1"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Transaction Modal -->
<div id="addTransactionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-plus-circle mr-2 text-primary-600"></i>
                Thêm giao dịch mới
            </h3>
            <button class="modal-close" onclick="closeModal('addTransactionModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addTransactionForm">
                <input type="hidden" name="action" value="add_transaction">

                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-tag mr-1"></i> Loại giao dịch
                        </label>
                        <select name="type" class="form-control" required>
                            <option value="expense">Chi tiêu</option>
                            <option value="income">Thu nhập</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-money-bill-wave mr-1"></i> Số tiền
                        </label>
                        <input type="number" name="amount" class="form-control" placeholder="0" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-list mr-1"></i> Danh mục
                    </label>
                    <select name="category_id" class="form-control" required>
                        <option value="">Chọn danh mục</option>
                        <!-- Populate via JS -->
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-calendar mr-1"></i> Ngày
                    </label>
                    <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-sticky-note mr-1"></i> Ghi chú
                    </label>
                    <textarea name="note" class="form-control" rows="3"
                        placeholder="Thêm ghi chú (tùy chọn)"></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal('addTransactionModal')" class="btn btn-outline">
                        <i class="fas fa-times mr-2"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i> Lưu giao dịch
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Transaction Modal -->
<div id="editTransactionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">
                <i class="fas fa-edit mr-2 text-primary-600"></i>
                Chỉnh sửa giao dịch
            </h3>
            <button class="modal-close" onclick="closeModal('editTransactionModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editTransactionForm">
                <input type="hidden" name="action" value="edit_transaction">
                <input type="hidden" name="id" id="edit-transaction-id">

                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Loại giao dịch</label>
                        <select name="type" id="edit-type" class="form-control" required>
                            <option value="expense">Chi tiêu</option>
                            <option value="income">Thu nhập</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Số tiền</label>
                        <input type="number" name="amount" id="edit-amount" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Danh mục</label>
                    <select name="category_id" id="edit-category" class="form-control" required>
                        <!-- Populate via JS -->
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Ngày</label>
                    <input type="date" name="date" id="edit-date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Ghi chú</label>
                    <textarea name="note" id="edit-note" class="form-control" rows="3"></textarea>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModal('editTransactionModal')" class="btn btn-outline">
                        Hủy
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        loadTransactions();
        loadCategoriesForSelect();

        // Add Transaction
        $('#addTransactionForm').submit(function (e) {
            e.preventDefault();
            $.post('/api/data.php', $(this).serialize(), function (res) {
                if (res.success) {
                    showToast('success', res.message);
                    closeModal('addTransactionModal');
                    $('#addTransactionForm')[0].reset();
                    loadTransactions();
                } else {
                    showToast('error', res.message);
                }
            });
        });

        // Edit Transaction
        $('#editTransactionForm').submit(function (e) {
            e.preventDefault();
            $.post('/api/data.php', $(this).serialize(), function (res) {
                if (res.success) {
                    showToast('success', res.message);
                    closeModal('editTransactionModal');
                    loadTransactions();
                } else {
                    showToast('error', res.message);
                }
            });
        });

        // Filters
        $('#searchInput, #filterCategory, #filterType, #filterDate').on('change keyup', function () {
            loadTransactions();
        });
    });

    function loadTransactions() {
        const filters = {
            action: 'get_transactions',
            search: $('#searchInput').val(),
            category: $('#filterCategory').val(),
            type: $('#filterType').val(),
            date: $('#filterDate').val()
        };

        $.get('/api/data.php', filters, function (res) {
            if (res.success) {
                const list = $('#transaction-list');
                list.empty();

                if (res.data.length === 0) {
                    list.html(`
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <h3>Không tìm thấy giao dịch</h3>
                                <p>Thử thay đổi bộ lọc hoặc thêm giao dịch mới</p>
                            </div>
                        </td>
                    </tr>
                `);
                    $('#showing-count').text('0');
                    return;
                }

                $('#showing-count').text(res.data.length);

                res.data.forEach(t => {
                    const amountClass = t.type === 'income' ? 'text-success' : 'text-danger';
                    const sign = t.type === 'income' ? '+' : '-';
                    const typeBadge = t.type === 'income'
                        ? '<span class="badge badge-success"><i class="fas fa-arrow-down mr-1"></i>Thu nhập</span>'
                        : '<span class="badge badge-danger"><i class="fas fa-arrow-up mr-1"></i>Chi tiêu</span>';

                    const html = `
                    <tr class="transition-all">
                        <td class="text-sm font-medium">${t.date}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white shadow-sm" style="background: ${t.category_color}">
                                    <i class="fas ${t.category_icon}"></i>
                                </div>
                                <span class="font-medium">${t.category_name}</span>
                            </div>
                        </td>
                        <td class="text-muted">${t.note || '-'}</td>
                        <td>${typeBadge}</td>
                        <td class="text-right">
                            <span class="font-mono font-bold text-lg ${amountClass}">
                                ${sign}${formatMoney(t.amount)}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="flex justify-center gap-2">
                                <button class="btn btn-sm btn-outline" onclick="editTransaction(${t.id})" title="Chỉnh sửa">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteTransaction(${t.id})" title="Xóa">
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

    function loadCategoriesForSelect() {
        $.get('/api/data.php?action=get_categories', function (res) {
            if (res.success) {
                const selects = $('select[name="category_id"], #filterCategory, #edit-category');
                res.data.forEach(c => {
                    $('select[name="category_id"]').append(`<option value="${c.id}">${c.name}</option>`);
                    $('#edit-category').append(`<option value="${c.id}">${c.name}</option>`);
                    $('#filterCategory').append(`<option value="${c.id}">${c.name}</option>`);
                });
            }
        });
    }

    function editTransaction(id) {
        $.get('/api/data.php?action=get_transaction&id=' + id, function (res) {
            if (res.success) {
                const t = res.data;
                $('#edit-transaction-id').val(t.id);
                $('#edit-type').val(t.type);
                $('#edit-amount').val(t.amount);
                $('#edit-category').val(t.category_id);
                $('#edit-date').val(t.date);
                $('#edit-note').val(t.note);
                openModal('editTransactionModal');
            }
        });
    }

    function deleteTransaction(id) {
        if (confirm('Bạn có chắc muốn xóa giao dịch này?')) {
            $.post('/api/data.php', {
                action: 'delete_transaction',
                id: id
            }, function (res) {
                if (res.success) {
                    showToast('success', res.message);
                    loadTransactions();
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