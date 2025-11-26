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
            <h2 class="text-xl font-bold">Mục tiêu tiết kiệm</h2>
            <p class="text-muted text-sm">Theo dõi tiến độ tiết kiệm của bạn</p>
        </div>
        <button class="btn btn-primary" id="addGoalBtn">
            <i class="fas fa-plus mr-2"></i> Thêm mục tiêu
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="card">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-bullseye text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-muted text-sm">Tổng mục tiêu</p>
                    <h3 class="text-xl font-bold" id="totalGoals">0</h3>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-orange-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-muted text-sm">Đang thực hiện</p>
                    <h3 class="text-xl font-bold" id="activeGoals">0</h3>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-muted text-sm">Hoàn thành</p>
                    <h3 class="text-xl font-bold text-success" id="completedGoals">0</h3>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-piggy-bank text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-muted text-sm">Tổng tiết kiệm</p>
                    <h3 class="text-xl font-bold" id="totalSaved">0 đ</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-6">
        <div class="flex gap-2 border-b">
            <button class="tab-btn active" data-filter="all">Tất cả</button>
            <button class="tab-btn" data-filter="active">Đang thực hiện</button>
            <button class="tab-btn" data-filter="completed">Hoàn thành</button>
            <button class="tab-btn" data-filter="archived">Đã lưu trữ</button>
        </div>
    </div>

    <!-- Goals Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="goalsGrid">
        <!-- Loaded via AJAX -->
    </div>
</div>

<!-- Add/Edit Goal Modal -->
<div class="modal" id="goalModal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h3 class="modal-title">Thêm mục tiêu</h3>
            <button class="modal-close">&times;</button>
        </div>
        <form id="goalForm">
            <input type="hidden" name="action" value="save_goal">
            <input type="hidden" name="id" id="goalId">
            <div class="form-group">
                <label class="form-label">Tên mục tiêu</label>
                <input type="text" name="name" id="goalName" class="form-control" placeholder="Mua xe, Du lịch..."
                    required>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Số tiền mục tiêu (VNĐ)</label>
                    <input type="number" name="target_amount" id="goalTarget" class="form-control"
                        placeholder="50000000" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Đã tiết kiệm (VNĐ)</label>
                    <input type="number" name="current_amount" id="goalCurrent" class="form-control" placeholder="0"
                        value="0">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Ngày bắt đầu</label>
                    <input type="date" name="start_date" id="goalStartDate" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Ngày kết thúc</label>
                    <input type="date" name="deadline" id="goalDeadline" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Mô tả</label>
                <textarea name="description" id="goalDescription" class="form-control" rows="3"
                    placeholder="Mô tả về mục tiêu..."></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" class="btn btn-outline modal-close">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Savings Modal -->
<div class="modal" id="savingsModal">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h3 class="modal-title">Thêm tiền tiết kiệm</h3>
            <button class="modal-close">&times;</button>
        </div>
        <form id="savingsForm">
            <input type="hidden" name="action" value="add_savings">
            <input type="hidden" name="goal_id" id="savingsGoalId">
            <div class="form-group">
                <label class="form-label">Số tiền thêm (VNĐ)</label>
                <input type="number" name="amount" id="savingsAmount" class="form-control" placeholder="1000000"
                    required>
            </div>
            <div class="form-group">
                <label class="form-label">Ghi chú</label>
                <input type="text" name="note" id="savingsNote" class="form-control" placeholder="Lương tháng 11...">
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" class="btn btn-outline modal-close">Hủy</button>
                <button type="submit" class="btn btn-primary">Thêm</button>
            </div>
        </form>
    </div>
</div>

<style>
    .tab-btn {
        padding: 0.75rem 1.5rem;
        background: none;
        border: none;
        border-bottom: 2px solid transparent;
        color: #6b7280;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .tab-btn:hover {
        color: var(--primary-color);
    }

    .tab-btn.active {
        color: var(--primary-color);
        border-bottom-color: var(--primary-color);
    }

    .goal-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        padding: 1.5rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .goal-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .goal-card.completed {
        border: 2px solid #10b981;
    }

    .goal-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1rem;
    }

    .goal-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-color), #059669);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    .goal-title {
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #111827;
    }

    .goal-description {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 1rem;
        line-height: 1.5;
    }

    .goal-progress {
        margin-bottom: 1rem;
    }

    .goal-amounts {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .goal-current {
        font-weight: 600;
        color: var(--primary-color);
    }

    .goal-target {
        color: #6b7280;
    }

    .goal-progress-bar {
        height: 8px;
        background-color: #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
    }

    .goal-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary-color), #059669);
        border-radius: 4px;
        transition: width 0.3s;
    }

    .goal-percentage {
        text-align: center;
        font-size: 0.75rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }

    .goal-deadline {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 1rem;
    }

    .goal-deadline.urgent {
        color: #ef4444;
        font-weight: 600;
    }

    .goal-actions {
        display: flex;
        gap: 0.5rem;
    }

    .goal-actions .btn {
        flex: 1;
    }

    .completed-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: #10b981;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .empty-state {
        grid-column: 1 / -1;
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
    let currentFilter = 'all';

    $(document).ready(function () {
        loadGoals();

        // Set default start date to today
        $('#goalStartDate').val(new Date().toISOString().split('T')[0]);

        $('.tab-btn').click(function () {
            $('.tab-btn').removeClass('active');
            $(this).addClass('active');
            currentFilter = $(this).data('filter');
            loadGoals();
        });

        $('#addGoalBtn').click(function () {
            $('#goalForm')[0].reset();
            $('#goalId').val('');
            $('#goalStartDate').val(new Date().toISOString().split('T')[0]);
            $('.modal-title').text('Thêm mục tiêu');
            $('#goalModal').fadeIn();
        });

        $('#goalForm').submit(function (e) {
            e.preventDefault();
            $.post('/api/data.php', $(this).serialize(), function (response) {
                if (response.success) {
                    showToast('success', response.message);
                    $('#goalModal').fadeOut();
                    loadGoals();
                } else {
                    showToast('error', response.message);
                }
            });
        });

        $('#savingsForm').submit(function (e) {
            e.preventDefault();
            $.post('/api/data.php', $(this).serialize(), function (response) {
                if (response.success) {
                    showToast('success', 'Đã thêm tiền tiết kiệm');
                    $('#savingsModal').fadeOut();
                    loadGoals();
                } else {
                    showToast('error', response.message);
                }
            });
        });
    });

    function loadGoals() {
        $.get('/api/data.php?action=get_goals&filter=' + currentFilter, function (response) {
            if (response.success) {
                renderGoals(response.data);
                updateSummary(response.summary);
            }
        });
    }

    function renderGoals(goals) {
        const grid = $('#goalsGrid');
        grid.empty();

        if (goals.length === 0) {
            grid.html(`
            <div class="empty-state">
                <i class="fas fa-bullseye"></i>
                <p class="text-lg font-medium mb-2">Chưa có mục tiêu</p>
                <p class="text-sm">Tạo mục tiêu tiết kiệm đầu tiên của bạn</p>
            </div>
        `);
            return;
        }

        goals.forEach(goal => {
            const percentage = (goal.current_amount / goal.target_amount) * 100;
            const isCompleted = percentage >= 100;
            const daysLeft = getDaysLeft(goal.deadline);
            const isUrgent = daysLeft <= 30 && daysLeft > 0;

            const html = `
            <div class="goal-card ${isCompleted ? 'completed' : ''}" style="position: relative;">
                ${isCompleted ? '<div class="completed-badge"><i class="fas fa-check mr-1"></i> Hoàn thành</div>' : ''}
                <div class="goal-header">
                    <div class="goal-icon">
                        <i class="fas fa-flag"></i>
                    </div>
                </div>
                <div class="goal-title">${goal.name}</div>
                ${goal.description ? `<div class="goal-description">${goal.description}</div>` : ''}
                <div class="goal-progress">
                    <div class="goal-amounts">
                        <span class="goal-current">${formatMoney(goal.current_amount)}</span>
                        <span class="goal-target">${formatMoney(goal.target_amount)}</span>
                    </div>
                    <div class="goal-progress-bar">
                        <div class="goal-progress-fill" style="width: ${Math.min(percentage, 100)}%"></div>
                    </div>
                    <div class="goal-percentage">${percentage.toFixed(1)}% hoàn thành</div>
                </div>
                <div class="goal-deadline ${isUrgent ? 'urgent' : ''}">
                    <i class="far fa-calendar"></i>
                    <span>${daysLeft > 0 ? `Còn ${daysLeft} ngày` : daysLeft === 0 ? 'Hết hạn hôm nay' : 'Đã quá hạn'}</span>
                </div>
                <div class="goal-actions">
                    ${!isCompleted ? `
                        <button class="btn btn-sm btn-primary" onclick="addSavings(${goal.id})">
                            <i class="fas fa-plus mr-1"></i> Thêm tiền
                        </button>
                    ` : ''}
                    <button class="btn btn-sm btn-outline" onclick="editGoal(${goal.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline text-danger" onclick="deleteGoal(${goal.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
            grid.append(html);
        });
    }

    function updateSummary(summary) {
        $('#totalGoals').text(summary.total);
        $('#activeGoals').text(summary.active);
        $('#completedGoals').text(summary.completed);
        $('#totalSaved').text(formatMoney(summary.total_saved));
    }

    function getDaysLeft(deadlineStr) {
        const deadline = new Date(deadlineStr);
        const today = new Date();
        const diffTime = deadline - today;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        return diffDays;
    }

    function addSavings(goalId) {
        $('#savingsGoalId').val(goalId);
        $('#savingsForm')[0].reset();
        $('#savingsModal').fadeIn();
    }

    function editGoal(id) {
        $.get('/api/data.php?action=get_goal&id=' + id, function (response) {
            if (response.success) {
                const goal = response.data;
                $('#goalId').val(goal.id);
                $('#goalName').val(goal.name);
                $('#goalTarget').val(goal.target_amount);
                $('#goalCurrent').val(goal.current_amount);
                $('#goalStartDate').val(goal.start_date);
                $('#goalDeadline').val(goal.deadline);
                $('#goalDescription').val(goal.description);
                $('.modal-title').text('Sửa mục tiêu');
                $('#goalModal').fadeIn();
            }
        });
    }

    function deleteGoal(id) {
        if (confirm('Bạn có chắc muốn xóa mục tiêu này?')) {
            $.post('/api/data.php', {
                action: 'delete_goal',
                id: id
            }, function (response) {
                if (response.success) {
                    showToast('success', 'Đã xóa mục tiêu');
                    loadGoals();
                }
            });
        }
    }
</script>

<?php include 'partials/footer.php'; ?>