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
            <h2 class="text-xl font-bold">Kế hoạch ngân sách</h2>
            <p class="text-muted text-sm">Quản lý ngân sách theo từng danh mục</p>
        </div>
        <button class="btn btn-primary" id="addBudgetBtn">
            <i class="fas fa-plus mr-2"></i> Thêm ngân sách
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="card">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-wallet text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-muted text-sm">Tổng ngân sách</p>
                    <h3 class="text-xl font-bold" id="totalBudget">0 đ</h3>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                    <i class="fas fa-chart-line text-orange-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-muted text-sm">Đã chi tiêu</p>
                    <h3 class="text-xl font-bold" id="totalSpent">0 đ</h3>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="fas fa-piggy-bank text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-muted text-sm">Còn lại</p>
                    <h3 class="text-xl font-bold text-success" id="totalRemaining">0 đ</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Budget List -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ngân sách theo danh mục</h3>
        </div>
        <div id="budgetList">
            <!-- Loaded via AJAX -->
        </div>
    </div>
</div>

<!-- Add/Edit Budget Modal -->
<div class="modal" id="budgetModal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3 class="modal-title">Thêm ngân sách</h3>
            <button class="modal-close">&times;</button>
        </div>
        <form id="budgetForm">
            <input type="hidden" name="action" value="save_budget">
            <input type="hidden" name="id" id="budgetId">
            <div class="form-group">
                <label class="form-label">Danh mục</label>
                <select name="category_id" id="categorySelect" class="form-control" required>
                    <option value="">Chọn danh mục</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Ngân sách tháng (VNĐ)</label>
                <input type="number" name="amount" id="budgetAmount" class="form-control" placeholder="5000000"
                    required>
            </div>
            <div class="form-group">
                <label class="form-label">Ghi chú</label>
                <textarea name="note" id="budgetNote" class="form-control" rows="3"
                    placeholder="Ghi chú về ngân sách..."></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" class="btn btn-outline modal-close">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<style>
    .budget-item {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .budget-item:last-child {
        border-bottom: none;
    }

    .budget-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .budget-category {
        font-weight: 600;
        font-size: 1.125rem;
        color: #111827;
    }

    .budget-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .budget-stat {
        text-align: center;
    }

    .budget-stat-label {
        font-size: 0.75rem;
        color: #6b7280;
        margin-bottom: 0.25rem;
    }

    .budget-stat-value {
        font-size: 1rem;
        font-weight: 600;
    }

    .progress-bar-container {
        height: 8px;
        background-color: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }

    .progress-bar {
        height: 100%;
        transition: width 0.3s, background-color 0.3s;
        border-radius: 4px;
    }

    .progress-bar.ok {
        background-color: #10b981;
    }

    .progress-bar.warning {
        background-color: #f59e0b;
    }

    .progress-bar.exceeded {
        background-color: #ef4444;
    }

    .progress-label {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .budget-actions {
        display: flex;
        gap: 0.5rem;
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
        loadBudgets();
        loadCategories();

        $('#addBudgetBtn').click(function () {
            $('#budgetForm')[0].reset();
            $('#budgetId').val('');
            $('.modal-title').text('Thêm ngân sách');
            $('#budgetModal').fadeIn();
        });

        $('#budgetForm').submit(function (e) {
            e.preventDefault();
            $.post('/api/data.php', $(this).serialize(), function (response) {
                if (response.success) {
                    showToast('success', response.message);
                    $('#budgetModal').fadeOut();
                    loadBudgets();
                } else {
                    showToast('error', response.message);
                }
            });
        });
    });

    function loadBudgets() {
        $.get('/api/data.php?action=get_budgets', function (response) {
            if (response.success) {
                renderBudgets(response.data);
                updateSummary(response.summary);
            }
        });
    }

    function loadCategories() {
        $.get('/api/data.php?action=get_categories', function (response) {
            if (response.success) {
                const select = $('#categorySelect');
                select.empty().append('<option value="">Chọn danh mục</option>');
                response.data.forEach(cat => {
                    select.append(`<option value="${cat.id}">${cat.name}</option>`);
                });
            }
        });
    }

    function renderBudgets(budgets) {
        const container = $('#budgetList');
        container.empty();

        if (budgets.length === 0) {
            container.html(`
            <div class="empty-state">
                <i class="fas fa-chart-pie"></i>
                <p class="text-lg font-medium mb-2">Chưa có ngân sách</p>
                <p class="text-sm">Thêm ngân sách cho các danh mục chi tiêu</p>
            </div>
        `);
            return;
        }

        budgets.forEach(budget => {
            const percentage = (budget.spent / budget.amount) * 100;
            const remaining = budget.amount - budget.spent;
            let progressClass = 'ok';

            if (percentage >= 100) {
                progressClass = 'exceeded';
            } else if (percentage >= 80) {
                progressClass = 'warning';
            }

            const html = `
            <div class="budget-item">
                <div class="budget-header">
                    <div class="budget-category">
                        <i class="fas fa-tag mr-2" style="color: ${budget.color}"></i>
                        ${budget.category}
                    </div>
                    <div class="budget-actions">
                        <button class="btn btn-sm btn-outline" onclick="editBudget(${budget.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline text-danger" onclick="deleteBudget(${budget.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="budget-stats">
                    <div class="budget-stat">
                        <div class="budget-stat-label">Ngân sách</div>
                        <div class="budget-stat-value">${formatMoney(budget.amount)}</div>
                    </div>
                    <div class="budget-stat">
                        <div class="budget-stat-label">Đã chi</div>
                        <div class="budget-stat-value text-orange-600">${formatMoney(budget.spent)}</div>
                    </div>
                    <div class="budget-stat">
                        <div class="budget-stat-label">Còn lại</div>
                        <div class="budget-stat-value ${remaining >= 0 ? 'text-success' : 'text-danger'}">
                            ${formatMoney(remaining)}
                        </div>
                    </div>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar ${progressClass}" style="width: ${Math.min(percentage, 100)}%"></div>
                </div>
                <div class="progress-label">
                    ${percentage.toFixed(1)}% đã sử dụng
                    ${percentage >= 100 ? '<span class="text-danger ml-2"><i class="fas fa-exclamation-triangle"></i> Vượt ngân sách</span>' : ''}
                    ${percentage >= 80 && percentage < 100 ? '<span class="text-warning ml-2"><i class="fas fa-exclamation-circle"></i> Gần hết ngân sách</span>' : ''}
                </div>
            </div>
        `;
            container.append(html);
        });
    }

    function updateSummary(summary) {
        $('#totalBudget').text(formatMoney(summary.total_budget));
        $('#totalSpent').text(formatMoney(summary.total_spent));
        $('#totalRemaining').text(formatMoney(summary.total_remaining));
    }

    function editBudget(id) {
        $.get('/api/data.php?action=get_budget&id=' + id, function (response) {
            if (response.success) {
                const budget = response.data;
                $('#budgetId').val(budget.id);
                $('#categorySelect').val(budget.category_id);
                $('#budgetAmount').val(budget.amount);
                $('#budgetNote').val(budget.note);
                $('.modal-title').text('Sửa ngân sách');
                $('#budgetModal').fadeIn();
            }
        });
    }

    function deleteBudget(id) {
        if (confirm('Bạn có chắc muốn xóa ngân sách này?')) {
            $.post('/api/data.php', {
                action: 'delete_budget',
                id: id
            }, function (response) {
                if (response.success) {
                    showToast('success', 'Đã xóa ngân sách');
                    loadBudgets();
                }
            });
        }
    }
</script>

<?php include 'partials/footer.php'; ?>