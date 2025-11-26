<?php
require_once '../../config.php';
require_once '../../functions.php';
requireLogin();

include 'partials/header.php';
include 'partials/sidebar.php';
include 'partials/navbar.php';
?>

<div class="container">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold">Giao dịch định kỳ</h2>
            <p class="text-muted text-sm">Quản lý các khoản chi tiêu lặp lại hàng tháng</p>
        </div>
        <button class="btn btn-primary" id="addRecurringBtn">
            <i class="fas fa-plus mr-2"></i> Thêm giao dịch định kỳ
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="card">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-sync text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-muted text-sm">Tổng giao dịch</p>
                    <h3 class="text-xl font-bold" id="totalRecurring">0</h3>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-muted text-sm">Đang hoạt động</p>
                    <h3 class="text-xl font-bold text-success" id="activeRecurring">0</h3>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-orange-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-muted text-sm">Chi phí hàng tháng</p>
                    <h3 class="text-xl font-bold" id="monthlyTotal">0 đ</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Recurring Transactions Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách giao dịch định kỳ</h3>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tên</th>
                        <th>Số tiền</th>
                        <th>Danh mục</th>
                        <th>Tần suất</th>
                        <th>Ngày tiếp theo</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="recurringList">
                    <!-- Loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Recurring Transaction Modal -->
<div class="modal" id="recurringModal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3 class="modal-title">Thêm giao dịch định kỳ</h3>
            <button class="modal-close">&times;</button>
        </div>
        <form id="recurringForm">
            <input type="hidden" name="action" value="save_recurring">
            <input type="hidden" name="id" id="recurringId">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Tên giao dịch</label>
                    <input type="text" name="name" id="recurringName" class="form-control"
                        placeholder="Netflix, Tiền điện..." required>
                </div>
                <div class="form-group">
                    <label class="form-label">Số tiền (VNĐ)</label>
                    <input type="number" name="amount" id="recurringAmount" class="form-control" placeholder="200000"
                        required>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Danh mục</label>
                    <select name="category_id" id="recurringCategory" class="form-control" required>
                        <option value="">Chọn danh mục</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tần suất</label>
                    <select name="frequency" id="recurringFrequency" class="form-control" required>
                        <option value="weekly">Hàng tuần</option>
                        <option value="monthly" selected>Hàng tháng</option>
                        <option value="quarterly">Hàng quý</option>
                        <option value="yearly">Hàng năm</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Ngày bắt đầu</label>
                    <input type="date" name="start_date" id="recurringStartDate" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" id="recurringStatus" class="form-control">
                        <option value="active">Hoạt động</option>
                        <option value="inactive">Tạm dừng</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Ghi chú</label>
                <textarea name="note" id="recurringNote" class="form-control" rows="3"
                    placeholder="Ghi chú về giao dịch..."></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" class="btn btn-outline modal-close">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<style>
    .status-toggle {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 24px;
    }

    .status-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .status-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: 0.3s;
        border-radius: 24px;
    }

    .status-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }

    input:checked+.status-slider {
        background-color: var(--primary-color);
    }

    input:checked+.status-slider:before {
        transform: translateX(24px);
    }

    .frequency-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        background-color: #e0e7ff;
        color: #4f46e5;
    }

    .next-date {
        color: #6b7280;
        font-size: 0.875rem;
    }

    .next-date.upcoming {
        color: #f59e0b;
        font-weight: 600;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
</style>

<script>
    $(document).ready(function () {
        loadRecurringTransactions();
        loadCategories();

        // Set default start date to today
        $('#recurringStartDate').val(new Date().toISOString().split('T')[0]);

        $('#addRecurringBtn').click(function () {
            $('#recurringForm')[0].reset();
            $('#recurringId').val('');
            $('#recurringStartDate').val(new Date().toISOString().split('T')[0]);
            $('.modal-title').text('Thêm giao dịch định kỳ');
            $('#recurringModal').fadeIn();
        });

        $('#recurringForm').submit(function (e) {
            e.preventDefault();
            $.post('/api/data.php', $(this).serialize(), function (response) {
                if (response.success) {
                    showToast('success', response.message);
                    $('#recurringModal').fadeOut();
                    loadRecurringTransactions();
                } else {
                    showToast('error', response.message);
                }
            });
        });
    });

    function loadRecurringTransactions() {
        $.get('/api/data.php?action=get_recurring_transactions', function (response) {
            if (response.success) {
                renderRecurringTransactions(response.data);
                updateSummary(response.summary);
            }
        });
    }

    function loadCategories() {
        $.get('/api/data.php?action=get_categories', function (response) {
            if (response.success) {
                const select = $('#recurringCategory');
                select.empty().append('<option value="">Chọn danh mục</option>');
                response.data.forEach(cat => {
                    select.append(`<option value="${cat.id}">${cat.name}</option>`);
                });
            }
        });
    }

    function renderRecurringTransactions(transactions) {
        const tbody = $('#recurringList');
        tbody.empty();

        if (transactions.length === 0) {
            tbody.html(`
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <i class="fas fa-sync-alt"></i>
                        <p class="text-lg font-medium mb-2">Chưa có giao dịch định kỳ</p>
                        <p class="text-sm">Thêm các khoản chi tiêu lặp lại hàng tháng</p>
                    </div>
                </td>
            </tr>
        `);
            return;
        }

        transactions.forEach(item => {
            const isUpcoming = isDateUpcoming(item.next_date);
            const frequencyText = getFrequencyText(item.frequency);

            const html = `
            <tr>
                <td class="font-medium">${item.name}</td>
                <td class="font-bold text-danger">${formatMoney(item.amount)}</td>
                <td>
                    <span class="badge badge-outline">${item.category}</span>
                </td>
                <td>
                    <span class="frequency-badge">${frequencyText}</span>
                </td>
                <td class="next-date ${isUpcoming ? 'upcoming' : ''}">
                    ${item.next_date}
                    ${isUpcoming ? '<i class="fas fa-exclamation-circle ml-1"></i>' : ''}
                </td>
                <td>
                    <label class="status-toggle">
                        <input type="checkbox" ${item.status === 'active' ? 'checked' : ''} 
                               onchange="toggleStatus(${item.id}, this.checked)">
                        <span class="status-slider"></span>
                    </label>
                </td>
                <td>
                    <div class="flex gap-2">
                        <button class="btn btn-sm btn-outline" onclick="editRecurring(${item.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline text-danger" onclick="deleteRecurring(${item.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
            tbody.append(html);
        });
    }

    function updateSummary(summary) {
        $('#totalRecurring').text(summary.total);
        $('#activeRecurring').text(summary.active);
        $('#monthlyTotal').text(formatMoney(summary.monthly_total));
    }

    function getFrequencyText(frequency) {
        const texts = {
            'weekly': 'Hàng tuần',
            'monthly': 'Hàng tháng',
            'quarterly': 'Hàng quý',
            'yearly': 'Hàng năm'
        };
        return texts[frequency] || frequency;
    }

    function isDateUpcoming(dateStr) {
        const date = new Date(dateStr);
        const today = new Date();
        const diffDays = Math.ceil((date - today) / (1000 * 60 * 60 * 24));
        return diffDays >= 0 && diffDays <= 7;
    }

    function toggleStatus(id, isActive) {
        $.post('/api/data.php', {
            action: 'toggle_recurring_status',
            id: id,
            status: isActive ? 'active' : 'inactive'
        }, function (response) {
            if (response.success) {
                showToast('success', isActive ? 'Đã kích hoạt' : 'Đã tạm dừng');
                loadRecurringTransactions();
            }
        });
    }

    function editRecurring(id) {
        $.get('/api/data.php?action=get_recurring&id=' + id, function (response) {
            if (response.success) {
                const item = response.data;
                $('#recurringId').val(item.id);
                $('#recurringName').val(item.name);
                $('#recurringAmount').val(item.amount);
                $('#recurringCategory').val(item.category_id);
                $('#recurringFrequency').val(item.frequency);
                $('#recurringStartDate').val(item.start_date);
                $('#recurringStatus').val(item.status);
                $('#recurringNote').val(item.note);
                $('.modal-title').text('Sửa giao dịch định kỳ');
                $('#recurringModal').fadeIn();
            }
        });
    }

    function deleteRecurring(id) {
        if (confirm('Bạn có chắc muốn xóa giao dịch định kỳ này?')) {
            $.post('/api/data.php', {
                action: 'delete_recurring',
                id: id
            }, function (response) {
                if (response.success) {
                    showToast('success', 'Đã xóa giao dịch định kỳ');
                    loadRecurringTransactions();
                }
            });
        }
    }
</script>

<?php include 'partials/footer.php'; ?>