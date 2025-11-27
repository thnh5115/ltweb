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
            <h2 class="text-xl font-bold">Lịch hóa đơn</h2>
            <p class="text-muted text-sm">Quản lý và theo dõi các hóa đơn sắp tới</p>
        </div>
        <button class="btn btn-primary" id="addBillBtn">
            <i class="fas fa-plus mr-2"></i> Thêm hóa đơn
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Calendar -->
        <div class="lg:col-span-2">
            <div class="card">
                <div class="card-header flex justify-between items-center">
                    <button class="btn btn-sm btn-outline" id="prevMonth">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <h3 class="card-title" id="currentMonth">Tháng 11, 2024</h3>
                    <button class="btn btn-sm btn-outline" id="nextMonth">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div id="calendar"></div>
            </div>
        </div>

        <!-- Upcoming Bills -->
        <div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Hóa đơn sắp tới</h3>
                </div>
                <div id="upcomingBills" style="max-height: 600px; overflow-y: auto;">
                    <!-- Loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Bill Modal -->
<div class="modal modal-small" id="billModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Thêm hóa đơn</h3>
            <button class="modal-close">&times;</button>
        </div>
        <form id="billForm">
            <input type="hidden" name="action" value="save_bill">
            <input type="hidden" name="id" id="billId">
            <div class="form-group">
                <label class="form-label">Tên hóa đơn</label>
                <input type="text" name="name" id="billName" class="form-control" placeholder="Tiền điện, nước..."
                    required>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Số tiền (VNĐ)</label>
                    <input type="number" name="amount" id="billAmount" class="form-control" placeholder="500000"
                        required>
                </div>
                <div class="form-group">
                    <label class="form-label">Danh mục</label>
                    <select name="category_id" id="billCategory" class="form-control" required>
                        <option value="">Chọn danh mục</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Ngày đến hạn</label>
                <input type="date" name="due_date" id="billDueDate" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Ghi chú</label>
                <textarea name="note" id="billNote" class="form-control" rows="2" placeholder="Ghi chú..."></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" class="btn btn-outline modal-close">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<style>
    .calendar {
        padding: 1rem;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.5rem;
    }

    .calendar-day-header {
        text-align: center;
        font-weight: 600;
        font-size: 0.875rem;
        color: #6b7280;
        padding: 0.5rem;
    }

    .calendar-day {
        aspect-ratio: 1;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
        min-height: 80px;
    }

    .calendar-day:hover {
        background-color: #f9fafb;
        border-color: var(--primary-color);
    }

    .calendar-day.other-month {
        color: #d1d5db;
        background-color: #f9fafb;
    }

    .calendar-day.today {
        background-color: #eff6ff;
        border-color: #3b82f6;
    }

    .calendar-day-number {
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .calendar-bill-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
        margin: 2px;
    }

    .calendar-bill-dot.unpaid {
        background-color: #ef4444;
    }

    .calendar-bill-dot.paid {
        background-color: #10b981;
    }

    .bill-item {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .bill-item:hover {
        background-color: #f9fafb;
    }

    .bill-item:last-child {
        border-bottom: none;
    }

    .bill-item.paid {
        opacity: 0.6;
    }

    .bill-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 0.5rem;
    }

    .bill-name {
        font-weight: 600;
        color: #111827;
    }

    .bill-amount {
        font-weight: 700;
        color: #ef4444;
    }

    .bill-meta {
        display: flex;
        gap: 1rem;
        font-size: 0.875rem;
        color: #6b7280;
    }

    .bill-due {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .bill-due.overdue {
        color: #ef4444;
        font-weight: 600;
    }

    .bill-due.upcoming {
        color: #f59e0b;
        font-weight: 600;
    }

    .bill-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 0.5rem;
        opacity: 0.5;
    }
</style>

<script>
    let currentDate = new Date();
    let bills = [];

    $(document).ready(function () {
        loadCategories();
        loadBills();

        $('#prevMonth').click(function () {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });

        $('#nextMonth').click(function () {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });

        $('#addBillBtn').click(function () {
            $('#billForm')[0].reset();
            $('#billId').val('');
            $('.modal-title').text('Thêm hóa đơn');
            $('#billModal').fadeIn();
        });

        $('#billForm').submit(function (e) {
            e.preventDefault();
            $.post('/api/data.php', $(this).serialize(), function (response) {
                if (response.success) {
                    showToast('success', response.message);
                    $('#billModal').fadeOut();
                    loadBills();
                } else {
                    showToast('error', response.message);
                }
            });
        });
    });

    function loadCategories() {
        $.get('/api/data.php?action=get_categories', function (response) {
            if (response.success) {
                const select = $('#billCategory');
                select.empty().append('<option value="">Chọn danh mục</option>');
                response.data.forEach(cat => {
                    select.append(`<option value="${cat.id}">${cat.name}</option>`);
                });
            }
        });
    }

    function loadBills() {
        $.get('/api/data.php?action=get_bills', function (response) {
            if (response.success) {
                bills = response.data;
                renderCalendar();
                renderUpcomingBills();
            }
        });
    }

    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        // Update month title
        const monthNames = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
            'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
        $('#currentMonth').text(`${monthNames[month]}, ${year}`);

        // Get first day of month and number of days
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const daysInPrevMonth = new Date(year, month, 0).getDate();

        let html = '<div class="calendar"><div class="calendar-grid">';

        // Day headers
        const dayHeaders = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
        dayHeaders.forEach(day => {
            html += `<div class="calendar-day-header">${day}</div>`;
        });

        // Previous month days
        for (let i = firstDay - 1; i >= 0; i--) {
            const day = daysInPrevMonth - i;
            html += `<div class="calendar-day other-month"><div class="calendar-day-number">${day}</div></div>`;
        }

        // Current month days
        const today = new Date();
        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const isToday = today.getDate() === day && today.getMonth() === month && today.getFullYear() === year;
            const dayBills = bills.filter(b => b.due_date === dateStr);

            html += `<div class="calendar-day ${isToday ? 'today' : ''}" data-date="${dateStr}">`;
            html += `<div class="calendar-day-number">${day}</div>`;

            if (dayBills.length > 0) {
                html += '<div>';
                dayBills.forEach(bill => {
                    html += `<span class="calendar-bill-dot ${bill.status}"></span>`;
                });
                html += '</div>';
            }

            html += '</div>';
        }

        // Next month days
        const remainingDays = 42 - (firstDay + daysInMonth);
        for (let day = 1; day <= remainingDays; day++) {
            html += `<div class="calendar-day other-month"><div class="calendar-day-number">${day}</div></div>`;
        }

        html += '</div></div>';
        $('#calendar').html(html);

        // Click handler for calendar days
        $('.calendar-day').click(function () {
            const date = $(this).data('date');
            if (date) {
                $('#billDueDate').val(date);
                $('#billModal').fadeIn();
            }
        });
    }

    function renderUpcomingBills() {
        const container = $('#upcomingBills');
        container.empty();

        // Filter upcoming bills (next 30 days)
        const today = new Date();
        const thirtyDaysLater = new Date(today.getTime() + 30 * 24 * 60 * 60 * 1000);

        const upcomingBills = bills.filter(bill => {
            const dueDate = new Date(bill.due_date);
            return dueDate >= today && dueDate <= thirtyDaysLater;
        }).sort((a, b) => new Date(a.due_date) - new Date(b.due_date));

        if (upcomingBills.length === 0) {
            container.html(`
            <div class="empty-state">
                <i class="fas fa-calendar-check"></i>
                <p class="text-sm">Không có hóa đơn sắp tới</p>
            </div>
        `);
            return;
        }

        upcomingBills.forEach(bill => {
            const daysUntilDue = getDaysUntilDue(bill.due_date);
            const isPaid = bill.status === 'paid';

            const html = `
            <div class="bill-item ${isPaid ? 'paid' : ''}">
                <div class="bill-header">
                    <div class="bill-name">${bill.name}</div>
                    <div class="bill-amount">${formatMoney(bill.amount)}</div>
                </div>
                <div class="bill-meta">
                    <div class="bill-due ${daysUntilDue < 0 ? 'overdue' : daysUntilDue <= 3 ? 'upcoming' : ''}">
                        <i class="far fa-calendar"></i>
                        <span>${daysUntilDue < 0 ? 'Quá hạn' : daysUntilDue === 0 ? 'Hôm nay' : `${daysUntilDue} ngày nữa`}</span>
                    </div>
                    <div>
                        <span class="badge badge-outline">${bill.category}</span>
                    </div>
                </div>
                <div class="bill-actions">
                    ${!isPaid ? `
                        <button class="btn btn-sm btn-success" onclick="markAsPaid(${bill.id})">
                            <i class="fas fa-check mr-1"></i> Đánh dấu đã trả
                        </button>
                    ` : `
                        <span class="text-success text-sm">
                            <i class="fas fa-check-circle mr-1"></i> Đã thanh toán
                        </span>
                    `}
                    <button class="btn btn-sm btn-outline" onclick="editBill(${bill.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline text-danger" onclick="deleteBill(${bill.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
            container.append(html);
        });
    }

    function getDaysUntilDue(dueDateStr) {
        const dueDate = new Date(dueDateStr);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        dueDate.setHours(0, 0, 0, 0);
        const diffTime = dueDate - today;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        return diffDays;
    }

    function markAsPaid(id) {
        $.post('/api/data.php', {
            action: 'mark_bill_paid',
            id: id
        }, function (response) {
            if (response.success) {
                showToast('success', 'Đã đánh dấu thanh toán');
                loadBills();
            }
        });
    }

    function editBill(id) {
        const bill = bills.find(b => b.id === id);
        if (bill) {
            $('#billId').val(bill.id);
            $('#billName').val(bill.name);
            $('#billAmount').val(bill.amount);
            $('#billCategory').val(bill.category_id);
            $('#billDueDate').val(bill.due_date);
            $('#billNote').val(bill.note);
            $('.modal-title').text('Sửa hóa đơn');
            $('#billModal').fadeIn();
        }
    }

    function deleteBill(id) {
        if (confirm('Bạn có chắc muốn xóa hóa đơn này?')) {
            $.post('/api/data.php', {
                action: 'delete_bill',
                id: id
            }, function (response) {
                if (response.success) {
                    showToast('success', 'Đã xóa hóa đơn');
                    loadBills();
                }
            });
        }
    }
</script>

<?php include 'partials/footer.php'; ?>