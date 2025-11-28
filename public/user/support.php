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
            <h2 class="text-xl font-bold">Hỗ trợ</h2>
            <p class="text-muted text-sm">Liên hệ hỗ trợ và theo dõi yêu cầu của bạn</p>
        </div>
        <button class="btn btn-primary" onclick="openCreateTicketModal()">
            <i class="fas fa-plus mr-2"></i> Tạo yêu cầu hỗ trợ
        </button>
    </div>

    <!-- Filters -->
    <div class="card mb-6">
        <div class="p-4 border-b">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <select class="form-control" id="filterCategory">
                    <option value="">Tất cả danh mục</option>
                    <option value="bug">Lỗi hệ thống</option>
                    <option value="feature">Yêu cầu tính năng</option>
                    <option value="question">Câu hỏi</option>
                    <option value="other">Khác</option>
                </select>
                <select class="form-control" id="filterStatus">
                    <option value="">Tất cả trạng thái</option>
                    <option value="open">Mở</option>
                    <option value="answered">Đã trả lời</option>
                    <option value="closed">Đã đóng</option>
                </select>
                <select class="form-control" id="filterPriority">
                    <option value="">Tất cả ưu tiên</option>
                    <option value="low">Thấp</option>
                    <option value="medium">Trung bình</option>
                    <option value="high">Cao</option>
                </select>
                <button class="btn btn-primary" id="applyFilters">
                    <i class="fas fa-filter mr-2"></i> Lọc
                </button>
            </div>
        </div>
    </div>

    <!-- Tickets List -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Danh sách yêu cầu hỗ trợ</h3>
        </div>
        <div id="ticketsList">
            <!-- Loaded via AJAX -->
        </div>
    </div>
</div>

<!-- Create Ticket Modal -->
<div class="modal" id="createTicketModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Tạo yêu cầu hỗ trợ</h3>
            <button class="modal-close" onclick="closeModal('createTicketModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="createTicketForm">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="ticketSubject" required
                           placeholder="Mô tả ngắn gọn vấn đề của bạn">
                </div>
                <div class="form-group">
                    <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                    <select class="form-control" id="ticketCategory" required>
                        <option value="">Chọn danh mục</option>
                        <option value="bug">Lỗi hệ thống</option>
                        <option value="feature">Yêu cầu tính năng</option>
                        <option value="question">Câu hỏi</option>
                        <option value="other">Khác</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Ưu tiên</label>
                    <select class="form-control" id="ticketPriority">
                        <option value="medium">Trung bình</option>
                        <option value="low">Thấp</option>
                        <option value="high">Cao</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Nội dung <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="ticketMessage" rows="5" required
                              placeholder="Mô tả chi tiết vấn đề hoặc câu hỏi của bạn..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('createTicketModal')">Hủy</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane mr-2"></i> Gửi yêu cầu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Ticket Detail Modal -->
<div class="modal" id="ticketDetailModal">
    <div class="modal-content large">
        <div class="modal-header">
            <h3 class="modal-title">Chi tiết yêu cầu hỗ trợ</h3>
            <button class="modal-close" onclick="closeModal('ticketDetailModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="ticketDetail">
                <!-- Loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadTickets();

    $('#applyFilters').on('click', function() {
        loadTickets();
    });

    // Auto filter when select changes
    $('#filterCategory, #filterStatus, #filterPriority').on('change', function() {
        loadTickets();
    });

    $('#createTicketForm').on('submit', function(e) {
        e.preventDefault();
        createTicket();
    });
});

function openCreateTicketModal() {
    $('#createTicketModal').addClass('active').css('display', 'flex');
}

function closeModal(modalId) {
    $('#' + modalId).removeClass('active').css('display', 'none');
}

function loadTickets() {
    const filters = {
        category: $('#filterCategory').val(),
        status: $('#filterStatus').val(),
        priority: $('#filterPriority').val()
    };

    $('#ticketsList').html('<div class="text-center p-8"><div class="spinner"></div><p>Đang tải...</p></div>');

    $.get('/api/data.php', { action: 'get_my_tickets', ...filters }, function(res) {
        if (res.success && res.data) {
            renderTickets(res.data);
        } else {
            $('#ticketsList').html('<div class="text-center p-8 text-muted">Không có yêu cầu hỗ trợ nào</div>');
        }
    }).fail(function() {
        $('#ticketsList').html('<div class="text-center p-8 text-danger">Lỗi tải dữ liệu. Vui lòng thử lại.</div>');
    });
}

function renderTickets(tickets) {
    if (!tickets || tickets.length === 0) {
        $('#ticketsList').html('<div class="text-center p-8 text-muted">Không có yêu cầu hỗ trợ nào</div>');
        return;
    }

    let html = '<div class="divide-y">';
    tickets.forEach(ticket => {
        const statusClass = getStatusClass(ticket.status);
        const statusText = getStatusText(ticket.status);
        const priorityClass = getPriorityClass(ticket.priority);
        const priorityText = getPriorityText(ticket.priority);
        const categoryText = getCategoryText(ticket.category);

        html += `
            <div class="p-4 hover:bg-gray-50 cursor-pointer" onclick="viewTicketDetail(${ticket.id})">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900">${escapeHtml(ticket.subject)}</h4>
                        <p class="text-sm text-gray-600 mt-1">${escapeHtml(ticket.message ? ticket.message.substring(0, 100) + '...' : '')}</p>
                        <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                            <span><i class="fas fa-tag mr-1"></i>${categoryText}</span>
                            <span class="${priorityClass}"><i class="fas fa-exclamation-triangle mr-1"></i>${priorityText}</span>
                            <span>${formatDate(ticket.created_at)}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-2 py-1 text-xs font-medium rounded-full ${statusClass}">${statusText}</span>
                        ${ticket.is_read ? '' : '<span class="inline-block w-2 h-2 bg-blue-500 rounded-full ml-2"></span>'}
                    </div>
                </div>
            </div>
        `;
    });
    html += '</div>';

    $('#ticketsList').html(html);
}

function createTicket() {
    const submitBtn = $('#createTicketForm button[type="submit"]');
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Đang gửi...');

    const data = {
        action: 'create_ticket',
        subject: $('#ticketSubject').val().trim(),
        category: $('#ticketCategory').val(),
        priority: $('#ticketPriority').val(),
        message: $('#ticketMessage').val().trim()
    };

    $.post('/api/data.php', data, function(res) {
        if (res.success) {
            showToast('success', 'Yêu cầu hỗ trợ đã được gửi thành công!');
            $('#createTicketForm')[0].reset();
            closeModal('createTicketModal');
            loadTickets();
        } else {
            showToast('error', res.message || 'Có lỗi xảy ra. Vui lòng thử lại.');
        }
    }).fail(function() {
        showToast('error', 'Lỗi kết nối. Vui lòng thử lại.');
    }).always(function() {
        submitBtn.prop('disabled', false).html('<i class="fas fa-paper-plane mr-2"></i> Gửi yêu cầu');
    });
}

function viewTicketDetail(ticketId) {
    $('#ticketDetail').html('<div class="text-center p-8"><div class="spinner"></div><p>Đang tải...</p></div>');
    $('#ticketDetailModal').addClass('active').css('display', 'flex');

    // Note: Since we don't have a specific API for user ticket detail,
    // we'll use get_my_tickets and filter for this ticket
    // In a real implementation, you might want to add a get_ticket_detail_user API
    $.get('/api/data.php', { action: 'get_my_tickets' }, function(res) {
        if (res.success && res.data) {
            const ticket = res.data.find(t => t.id == ticketId);
            if (ticket) {
                renderTicketDetail(ticket);
            } else {
                $('#ticketDetail').html('<div class="text-center p-8 text-danger">Không tìm thấy yêu cầu hỗ trợ</div>');
            }
        } else {
            $('#ticketDetail').html('<div class="text-center p-8 text-danger">Lỗi tải dữ liệu</div>');
        }
    }).fail(function() {
        $('#ticketDetail').html('<div class="text-center p-8 text-danger">Lỗi kết nối</div>');
    });
}

function renderTicketDetail(ticket) {
    const statusClass = getStatusClass(ticket.status);
    const statusText = getStatusText(ticket.status);
    const priorityClass = getPriorityClass(ticket.priority);
    const priorityText = getPriorityText(ticket.priority);
    const categoryText = getCategoryText(ticket.category);

    let html = `
        <div class="border-b pb-4 mb-4">
            <h4 class="text-lg font-medium mb-2">${escapeHtml(ticket.subject)}</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div><strong>Danh mục:</strong> ${categoryText}</div>
                <div><strong>Ưu tiên:</strong> <span class="${priorityClass}">${priorityText}</span></div>
                <div><strong>Trạng thái:</strong> <span class="${statusClass}">${statusText}</span></div>
                <div><strong>Ngày tạo:</strong> ${formatDate(ticket.created_at)}</div>
            </div>
        </div>
        <div class="space-y-4">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                        B
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-medium">Bạn</span>
                            <span class="text-xs text-gray-500">${formatDate(ticket.created_at)}</span>
                        </div>
                        <p class="text-gray-700 whitespace-pre-line">${escapeHtml(ticket.message || 'Nội dung không có')}</p>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Show status-based message
    if (ticket.status === 'answered') {
        html += `
            <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center gap-2 text-green-800">
                    <i class="fas fa-info-circle"></i>
                    <span class="font-medium">Yêu cầu của bạn đã được trả lời</span>
                </div>
                <p class="text-sm text-green-700 mt-1">
                    Admin đã trả lời yêu cầu của bạn. Vui lòng kiểm tra <a href="/public/user/notifications.php" class="underline">thông báo</a> để xem chi tiết phản hồi.
                </p>
            </div>
        `;
    } else if (ticket.status === 'closed') {
        html += `
            <div class="mt-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <div class="flex items-center gap-2 text-gray-800">
                    <i class="fas fa-check-circle"></i>
                    <span class="font-medium">Yêu cầu đã được giải quyết</span>
                </div>
                <p class="text-sm text-gray-700 mt-1">
                    Yêu cầu hỗ trợ của bạn đã được xử lý và đóng. Cảm ơn bạn đã liên hệ với chúng tôi.
                </p>
            </div>
        `;
    } else {
        html += `
            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center gap-2 text-blue-800">
                    <i class="fas fa-clock"></i>
                    <span class="font-medium">Đang xử lý</span>
                </div>
                <p class="text-sm text-blue-700 mt-1">
                    Yêu cầu của bạn đang được xử lý. Chúng tôi sẽ thông báo khi có cập nhật.
                </p>
            </div>
        `;
    }

    $('#ticketDetail').html(html);
}

function getStatusClass(status) {
    switch(status) {
        case 'open': return 'bg-yellow-100 text-yellow-800';
        case 'answered': return 'bg-blue-100 text-blue-800';
        case 'closed': return 'bg-green-100 text-green-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function getStatusText(status) {
    switch(status) {
        case 'open': return 'Mở';
        case 'answered': return 'Đã trả lời';
        case 'closed': return 'Đã đóng';
        default: return status;
    }
}

function getPriorityClass(priority) {
    switch(priority) {
        case 'high': return 'text-red-600';
        case 'medium': return 'text-yellow-600';
        case 'low': return 'text-green-600';
        default: return 'text-gray-600';
    }
}

function getPriorityText(priority) {
    switch(priority) {
        case 'high': return 'Cao';
        case 'medium': return 'Trung bình';
        case 'low': return 'Thấp';
        default: return priority;
    }
}

function getCategoryText(category) {
    switch(category) {
        case 'bug': return 'Lỗi hệ thống';
        case 'feature': return 'Yêu cầu tính năng';
        case 'question': return 'Câu hỏi';
        case 'other': return 'Khác';
        default: return category;
    }
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('vi-VN') + ' ' + date.toLocaleTimeString('vi-VN', {hour: '2-digit', minute: '2-digit'});
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

$(document).ready(function() {
    loadTickets();

    $('#applyFilters').on('click', function() {
        loadTickets();
    });

    // Auto filter when select changes
    $('#filterCategory, #filterStatus, #filterPriority').on('change', function() {
        loadTickets();
    });
});
</script>

<style>
    .ticket-row {
        padding: 1.25rem;
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.2s;
        cursor: pointer;
        position: relative;
    }

    .ticket-row:hover {
        background-color: #f9fafb;
    }

    .ticket-row:last-child {
        border-bottom: none;
    }

    .ticket-row.unread {
        background-color: #eff6ff;
    }

    .ticket-row.unread::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background-color: var(--primary-color);
    }

    .modal.large .modal-content {
        max-width: 800px;
        width: 90%;
    }

    .divide-y > * + * {
        border-top: 1px solid #e5e7eb;
    }

    .spinner {
        width: 24px;
        height: 24px;
        border: 2px solid #e5e7eb;
        border-top: 2px solid var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 1rem;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<?php include 'partials/footer.php'; ?>