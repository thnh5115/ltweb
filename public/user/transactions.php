<?php
require_once '../../config.php';
require_once '../../functions.php';
requireLogin();

include 'partials/header.php';
include 'partials/sidebar.php';
include 'partials/navbar.php';
?>


    
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
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <div class="relative">
                            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="searchInput" class="form-control" style="padding-left: 2.75rem;"
                                placeholder="Tìm kiếm giao dịch, ghi chú..." aria-label="Tìm kiếm giao dịch">
                        </div>
                    </div>
                    <select id="filterCategory" class="form-control" aria-label="Lọc theo danh mục">
                        <option value="">Tất cả danh mục</option>
                        <!-- Populate via JS -->
                    </select>
                    <select id="filterType" class="form-control" aria-label="Lọc theo loại">
                        <option value="">Tất cả loại</option>
                        <option value="income">Thu nhập</option>
                        <option value="expense">Chi tiêu</option>
                    </select>
                    <select id="filterStatus" class="form-control" aria-label="Lọc theo trạng thái">
                        <option value="">Tất cả trạng thái</option>
                        <option value="completed">Hoàn tất</option>
                        <option value="pending">Chờ duyệt</option>
                        <option value="flagged">Cần xem</option>
                        <option value="canceled">Đã hủy</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input type="date" id="filterDateFrom" class="form-control" aria-label="Ngày bắt đầu">
                    <input type="date" id="filterDateTo" class="form-control" aria-label="Ngày kết thúc">
                    <input type="number" id="filterMinAmount" class="form-control" placeholder="Số tiền tối thiểu"
                        aria-label="Số tiền tối thiểu">
                    <input type="number" id="filterMaxAmount" class="form-control" placeholder="Số tiền tối đa"
                        aria-label="Số tiền tối đa">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                    <select id="sortField" class="form-control" aria-label="Sắp xếp theo">
                        <option value="date">Ngày giao dịch</option>
                        <option value="amount">Số tiền</option>
                        <option value="created_at">Ngày tạo</option>
                        <option value="category">Danh mục</option>
                    </select>
                    <select id="sortDir" class="form-control" aria-label="Thứ tự sắp xếp">
                        <option value="DESC">Giảm dần</option>
                        <option value="ASC">Tăng dần</option>
                    </select>
                    <select id="pageSize" class="form-control" aria-label="Số dòng mỗi trang">
                        <option value="10">10 dòng/trang</option>
                        <option value="20">20 dòng/trang</option>
                        <option value="50">50 dòng/trang</option>
                    </select>
                    <div class="flex justify-start md:justify-end">
                        <button id="resetFilters" class="btn btn-outline" type="button">
                            <i class="fas fa-undo mr-2"></i>Đặt lại bộ lọc
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div id="transactionsSummary" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 hidden">
            <div class="stat-card">
                <p class="text-sm text-muted mb-1">Tổng thu</p>
                <h3 class="text-2xl font-bold text-success" id="summaryIncome">0đ</h3>
            </div>
            <div class="stat-card">
                <p class="text-sm text-muted mb-1">Tổng chi</p>
                <h3 class="text-2xl font-bold text-danger" id="summaryExpense">0đ</h3>
            </div>
            <div class="stat-card">
                <p class="text-sm text-muted mb-1">Cân đối</p>
                <h3 class="text-2xl font-bold" id="summaryBalance">0đ</h3>
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
                    <tbody id="transaction-list" aria-live="polite">
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
            <button class="modal-close" onclick="closeModal('addTransactionModal')" aria-label="Đóng">&times;</button>
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
            <button class="modal-close" onclick="closeModal('editTransactionModal')" aria-label="Đóng">&times;</button>
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
    let currentPage = 1;
    let totalPages = 1;

    $(document).ready(function () {
        initTransactionFilters();
        initTransactionForms();
        loadCategoriesForSelect();
        loadTransactions();
    });

    function initTransactionForms() {
        $('#addTransactionForm').on('submit', function (e) {
            e.preventDefault();
            $.post('/api/data.php', $(this).serialize(), function (res) {
                if (res.success) {
                    showToast('success', res.message);
                    closeModal('addTransactionModal');
                    $('#addTransactionForm')[0].reset();
                    loadTransactions(currentPage);
                } else {
                    showToast('error', res.message);
                }
            });
        });

        $('#editTransactionForm').on('submit', function (e) {
            e.preventDefault();
            $.post('/api/data.php', $(this).serialize(), function (res) {
                if (res.success) {
                    showToast('success', res.message);
                    closeModal('editTransactionModal');
                    loadTransactions(currentPage);
                } else {
                    showToast('error', res.message);
                }
            });
        });
    }

    function initTransactionFilters() {
        let searchTimer;
        $('#searchInput').on('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => loadTransactions(1), 300);
        });

        $('#filterCategory, #filterType, #filterStatus, #filterDateFrom, #filterDateTo, #sortField, #sortDir').on('change', function () {
            loadTransactions(1);
        });

        let amountTimer;
        $('#filterMinAmount, #filterMaxAmount').on('input', function () {
            clearTimeout(amountTimer);
            amountTimer = setTimeout(() => loadTransactions(1), 400);
        });

        $('#pageSize').on('change', function () {
            loadTransactions(1);
        });

        $('#resetFilters').on('click', function () {
            resetTransactionFilters();
            loadTransactions(1);
        });

        $('#prevPage').on('click', function () {
            if (currentPage > 1) {
                loadTransactions(currentPage - 1);
            }
        });

        $('#nextPage').on('click', function () {
            if (currentPage < totalPages) {
                loadTransactions(currentPage + 1);
            }
        });
    }

    function resetTransactionFilters() {
        $('#searchInput').val('');
        $('#filterCategory').val('');
        $('#filterType').val('');
        $('#filterStatus').val('');
        $('#filterDateFrom').val('');
        $('#filterDateTo').val('');
        $('#filterMinAmount').val('');
        $('#filterMaxAmount').val('');
        $('#sortField').val('date');
        $('#sortDir').val('DESC');
        $('#pageSize').val('10');
    }

    function buildTransactionParams(page) {
        return {
            action: 'get_transactions',
            page: page,
            limit: $('#pageSize').val(),
            search: $('#searchInput').val(),
            category: $('#filterCategory').val(),
            type: $('#filterType').val(),
            status: $('#filterStatus').val(),
            date_from: $('#filterDateFrom').val(),
            date_to: $('#filterDateTo').val(),
            min_amount: $('#filterMinAmount').val(),
            max_amount: $('#filterMaxAmount').val(),
            sort_field: $('#sortField').val(),
            sort_dir: $('#sortDir').val()
        };
    }

    function loadTransactions(page = 1) {
        currentPage = page;
        const list = $('#transaction-list');
        list.html(`
            <tr>
                <td colspan="6">
                    <div class="flex justify-center items-center py-12">
                        <div class="spinner"></div>
                    </div>
                </td>
            </tr>
        `);

        const params = buildTransactionParams(currentPage);

        $.get('/api/data.php', params, function (res) {
            if (!res.success) {
                showToast('error', res.message || 'Không thể tải giao dịch');
                list.html(renderEmptyTransactions());
                updateSummary();
                updatePagination({ page: currentPage, limit: params.limit, total: 0, total_pages: 1 }, 0);
                return;
            }

            const data = res.data || {};
            const items = data.items || [];
            renderTransactions(items);
            updateSummary(data.summary || {});
            updatePagination(data.pagination || {}, items.length);
        });
    }

    function renderTransactions(items) {
        const list = $('#transaction-list');

        if (!items.length) {
            list.html(renderEmptyTransactions());
            return;
        }

        const rows = items.map(t => {
            const amountClass = t.type === 'income' ? 'text-success' : 'text-danger';
            const sign = t.type === 'income' ? '+' : '-';
            const typeBadge = t.type === 'income'
                ? '<span class="badge badge-success"><i class="fas fa-arrow-down mr-1"></i>Thu nhập</span>'
                : '<span class="badge badge-danger"><i class="fas fa-arrow-up mr-1"></i>Chi tiêu</span>';
            const statusBadge = renderStatusBadge(t.status);

            return `
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
                    <td class="text-muted">
                        <div>${t.note || '-'}</div>
                        <div class="mt-2">${statusBadge}</div>
                    </td>
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
        }).join('');

        list.html(rows);
    }

    function renderEmptyTransactions() {
        return `
            <tr>
                <td colspan="6">
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>Không tìm thấy giao dịch</h3>
                        <p>Thử thay đổi bộ lọc hoặc thêm giao dịch mới</p>
                    </div>
                </td>
            </tr>
        `;
    }

    function renderStatusBadge(status) {
        const state = (status || 'completed').toLowerCase();
        const map = {
            completed: { label: 'Hoàn tất', className: 'badge badge-success' },
            pending: { label: 'Chờ duyệt', className: 'badge badge-warning' },
            flagged: { label: 'Cần xem', className: 'badge badge-danger' },
            canceled: { label: 'Đã hủy', className: 'badge badge-outline' }
        };
        const conf = map[state] || map.completed;
        return `<span class="${conf.className}"><i class="fas fa-circle mr-1"></i>${conf.label}</span>`;
    }

    function updateSummary(summary = {}) {
        const income = summary.total_income || 0;
        const expense = summary.total_expense || 0;
        const balance = summary.balance || (income - expense);
        $('#summaryIncome').text(formatMoney(income));
        $('#summaryExpense').text(formatMoney(expense));
        $('#summaryBalance').text(formatMoney(balance));
        $('#summaryBalance').toggleClass('text-success', balance >= 0).toggleClass('text-danger', balance < 0);
        $('#transactionsSummary').removeClass('hidden');
    }

    function updatePagination(pagination = {}, renderedCount = 0) {
        const page = Number(pagination.page || 1);
        const limit = Number(pagination.limit || $('#pageSize').val() || 10);
        const total = Number(pagination.total || 0);
        totalPages = Math.max(1, Number(pagination.total_pages || Math.ceil(total / limit) || 1));
        currentPage = Math.min(page, totalPages);

        const start = total === 0 ? 0 : ((currentPage - 1) * limit) + 1;
        const end = total === 0 ? 0 : Math.min(total, start + Math.max(renderedCount - 1, 0));
        const showingText = total === 0 ? '0' : `${start}-${end} / ${total}`;
        $('#showing-count').text(showingText);

        $('#prevPage').prop('disabled', currentPage <= 1);
        $('#nextPage').prop('disabled', currentPage >= totalPages);
    }

    function loadCategoriesForSelect() {
        $.get('/api/data.php?action=get_categories', function (res) {
            if (res.success) {
                const categories = res.data || [];
                const hydrateSelect = selector => {
                    $(selector).each(function () {
                        const $select = $(this);
                        const firstOption = $select.find('option').first().clone();
                        $select.empty();
                        if (firstOption.length) {
                            $select.append(firstOption);
                        }
                        categories.forEach(c => {
                            $select.append(`<option value="${c.id}">${c.name}</option>`);
                        });
                    });
                };

                hydrateSelect('#addTransactionForm select[name="category_id"]');
                hydrateSelect('#editTransactionForm select[name="category_id"]');
                hydrateSelect('#filterCategory');
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
