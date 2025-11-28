<?php
require_once '../../config.php';
require_once 'admin_functions.php';
requireAdminLogin();

include 'partials/header.php';
include 'partials/sidebar.php';
include 'partials/navbar.php';
?>

<div class="container">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold">Hỗ trợ & Phản hồi</h2>
            <p class="text-muted text-sm">Quản lý yêu cầu hỗ trợ từ người dùng</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="card">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-inbox text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-muted text-sm">Tổng tickets</p>
                    <h3 class="text-xl font-bold" id="totalTickets">0</h3>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-muted text-sm">Chờ xử lý</p>
                    <h3 class="text-xl font-bold text-orange-600" id="openTickets">0</h3>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-reply text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-muted text-sm">Đã trả lời</p>
                    <h3 class="text-xl font-bold" id="answeredTickets">0</h3>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-muted text-sm">Đã đóng</p>
                    <h3 class="text-xl font-bold text-success" id="closedTickets">0</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-6">
        <div class="p-4 border-b">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <input type="text" class="form-control" id="searchTicket" placeholder="Tìm kiếm...">
                <select class="form-control" id="filterStatus">
                    <option value="">Tất cả trạng thái</option>
                    <option value="open">Chờ xử lý</option>
                    <option value="answered">Đã trả lời</option>
                    <option value="closed">Đã đóng</option>
                </select>
                <select class="form-control" id="filterCategory">
                    <option value="">Tất cả danh mục</option>
                    <option value="bug">Lỗi</option>
                    <option value="feature">Tính năng</option>
                    <option value="question">Câu hỏi</option>
                    <option value="other">Khác</option>
                </select>
                <input type="date" class="form-control" id="filterDate">
                <button class="btn btn-primary" id="applyFilters">
                    <i class="fas fa-filter mr-2"></i> Lọc
                </button>
            </div>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Người gửi</th>
                        <th>Tiêu đề</th>
                        <th>Danh mục</th>
                        <th>Ngày tạo</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="ticketsList">
                    <!-- Loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Ticket Detail Modal -->
<div class="modal" id="ticketModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Chi tiết ticket</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
            <div id="ticketDetail">
                <!-- Loaded via AJAX -->
            </div>

            <!-- Reply Form -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <h4 class="font-bold mb-3">Trả lời</h4>
                <form id="replyForm">
                    <input type="hidden" name="action" value="reply_ticket">
                    <input type="hidden" name="ticket_id" id="replyTicketId">
                    <div class="form-group">
                        <textarea name="message" id="replyMessage" class="form-control" rows="4"
                            placeholder="Nhập câu trả lời..." required></textarea>
                    </div>
                    <div class="flex gap-3 justify-end">
                        <button type="button" class="btn btn-outline" onclick="closeTicket()">
                            <i class="fas fa-check mr-2"></i> Đóng ticket
                        </button>
                        <button type="submit" class="btn btn-primary" style="background-color: #1e40af;">
                            <i class="fas fa-paper-plane mr-2"></i> Gửi trả lời
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .ticket-row {
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .ticket-row:hover {
        background-color: #f9fafb;
    }

    .ticket-row.unread {
        background-color: #eff6ff;
        font-weight: 600;
    }

    .category-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .category-bug {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .category-feature {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .category-question {
        background-color: #fef3c7;
        color: #92400e;
    }

    .category-other {
        background-color: #e5e7eb;
        color: #374151;
    }

    .ticket-message {
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 8px;
        border-left: 4px solid #e5e7eb;
    }

    .ticket-message.user {
        background-color: #f9fafb;
        border-left-color: #3b82f6;
    }

    .ticket-message.admin {
        background-color: #eff6ff;
        border-left-color: #1e40af;
    }

    .ticket-message-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .ticket-message-author {
        font-weight: 600;
        color: #111827;
    }

    .ticket-message-time {
        font-size: 0.75rem;
        color: #9ca3af;
    }

    .ticket-message-content {
        color: #374151;
        line-height: 1.6;
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
    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    $(document).ready(function () {
        loadTickets();

        $('#applyFilters').on('click', function () {
            loadTickets();
        });

        $('#filterStatus, #filterCategory, #filterDate').on('change', function () {
            loadTickets();
        });

        $('#searchTicket').on('keyup', debounce(function () {
            loadTickets();
        }, 500));

        $('#replyForm').submit(function (e) {
            e.preventDefault();
            $.post('/api/admin_data.php', $(this).serialize(), function (response) {
                if (response.success) {
                    showToast('success', 'Đã gửi trả lời');
                    $('#replyMessage').val('');
                    loadTicketDetail($('#replyTicketId').val());
                    loadTickets();
                } else {
                    showToast('error', response.message);
                }
            });
        });
    });

    function loadTickets() {
        const tbody = $('#ticketsList');
        tbody.html('<tr><td colspan="6" class="text-center p-8"><div class="spinner"></div><p>Đang tải...</p></td></tr>');

        const search = $('#searchTicket').val();
        const status = $('#filterStatus').val();
        const category = $('#filterCategory').val();
        const date = $('#filterDate').val();

        const params = {
            action: 'get_support_tickets',
            search: search,
            status: status,
            category: category,
            date: date
        };

        $.get('/api/admin_data.php', params, function (response) {
            if (response.success) {
                updateSummary(response.data.summary);
                renderTickets(response.data.items);
            } else {
                tbody.html('<tr><td colspan="6" class="text-center p-8 text-danger">Không thể tải danh sách ticket</td></tr>');
                showToast('error', 'Không thể tải danh sách ticket');
            }
        }).fail(function(xhr, status, error) {
            tbody.html('<tr><td colspan="6" class="text-center p-8 text-danger">Lỗi kết nối khi tải danh sách ticket</td></tr>');
            showToast('error', 'Lỗi kết nối khi tải danh sách ticket');
        });
    }

    function renderTickets(tickets) {
        const tbody = $('#ticketsList');
        tbody.empty();

        if (tickets.length === 0) {
            tbody.html(`
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p class="text-lg font-medium mb-2">Không có ticket</p>
                            <p class="text-sm">Chưa có yêu cầu hỗ trợ nào</p>
                        </div>
                    </td>
                </tr>
            `);
            return;
        }

        tickets.forEach(ticket => {
            const categoryClass = `category-${ticket.category}`;
            const statusBadge = getStatusBadge(ticket.status);

            const html = `
                <tr class="ticket-row ${ticket.is_read ? '' : 'unread'}" onclick="viewTicket(${ticket.id})">
                    <td class="font-mono">#${ticket.id}</td>
                    <td>
                        <div class="font-medium">${escapeHtml(ticket.user_name || 'Người dùng đã xóa')}</div>
                        <div class="text-sm text-muted">${escapeHtml(ticket.user_email || 'N/A')}</div>
                    </td>
                    <td class="font-medium">${escapeHtml(ticket.subject)}</td>
                    <td><span class="category-badge ${categoryClass}">${getCategoryText(ticket.category)}</span></td>
                    <td class="text-sm text-muted">${ticket.created_at}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <button class="btn btn-sm btn-outline" onclick="event.stopPropagation(); viewTicket(${ticket.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(html);
        });
    }

    function updateSummary(summary) {
        $('#totalTickets').text(summary.total);
        $('#openTickets').text(summary.open);
        $('#answeredTickets').text(summary.answered);
        $('#closedTickets').text(summary.closed);
    }

    function getStatusBadge(status) {
        const badges = {
            'open': '<span class="badge badge-warning">Chờ xử lý</span>',
            'answered': '<span class="badge badge-info">Đã trả lời</span>',
            'closed': '<span class="badge badge-success">Đã đóng</span>'
        };
        return badges[status] || '';
    }

    function getCategoryText(category) {
        const texts = {
            'bug': 'Lỗi',
            'feature': 'Tính năng',
            'question': 'Câu hỏi',
            'other': 'Khác'
        };
        return texts[category] || category;
    }

    function viewTicket(id) {
        loadTicketDetail(id);
        $('#ticketModal').fadeIn();
    }

    function loadTicketDetail(id) {
        $.get('/api/admin_data.php?action=get_ticket_detail&id=' + id, function (response) {
            if (response.success) {
                renderTicketDetail(response.data);
                $('#replyTicketId').val(id);
            }
        });
    }

    function renderTicketDetail(ticket) {
        const container = $('#ticketDetail');

        let html = `
        <div class="mb-4">
            <div class="flex justify-between items-start mb-2">
                <h3 class="text-xl font-bold">${escapeHtml(ticket.subject)}</h3>
                ${getStatusBadge(ticket.status)}
            </div>
            <div class="flex gap-4 text-sm text-muted">
                <span><i class="fas fa-user mr-1"></i> ${escapeHtml(ticket.user_name)}</span>
                <span><i class="fas fa-envelope mr-1"></i> ${escapeHtml(ticket.user_email)}</span>
                <span><i class="fas fa-clock mr-1"></i> ${ticket.created_at}</span>
            </div>
        </div>
        <hr class="my-4">
    `;

        // Messages
        ticket.messages.forEach(msg => {
            const isAdmin = msg.sender_type === 'admin';
            html += `
            <div class="ticket-message ${isAdmin ? 'admin' : 'user'}">
                <div class="ticket-message-header">
                    <span class="ticket-message-author">
                        ${isAdmin ? '<i class="fas fa-user-shield mr-1"></i>' : '<i class="fas fa-user mr-1"></i>'}
                        ${escapeHtml(msg.sender_name)}
                    </span>
                    <span class="ticket-message-time">${msg.created_at}</span>
                </div>
                <div class="ticket-message-content">${escapeHtml(msg.message)}</div>
            </div>
        `;
        });

        container.html(html);
    }

    function closeTicket() {
        const ticketId = $('#replyTicketId').val();
        if (confirm('Bạn có chắc muốn đóng ticket này?')) {
            $.post('/api/admin_data.php', {
                action: 'close_ticket',
                ticket_id: ticketId
            }, function (response) {
                if (response.success) {
                    showToast('success', 'Đã đóng ticket');
                    $('#ticketModal').fadeOut();
                    loadTickets();
                }
            });
        }
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
</script>

<?php include 'partials/footer.php'; ?>