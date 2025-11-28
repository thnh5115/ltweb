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
            <h2 class="text-xl font-bold">Thông báo</h2>
            <p class="text-muted text-sm">Quản lý tất cả thông báo của bạn</p>
        </div>
        <button class="btn btn-outline" id="markAllRead">
            <i class="fas fa-check-double mr-2"></i> Đánh dấu tất cả đã đọc
        </button>
    </div>

    <!-- Filters -->
    <div class="card mb-6">
        <div class="p-4 border-b">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <select class="form-control" id="filterType">
                    <option value="">Tất cả loại</option>
                    <option value="budget">Ngân sách</option>
                    <option value="bill">Hóa đơn</option>
                    <option value="goal">Mục tiêu</option>
                    <option value="system">Hệ thống</option>
                    <option value="info">Thông tin</option>
                    <option value="success">Thành công</option>
                    <option value="warning">Cảnh báo</option>
                    <option value="error">Lỗi</option>
                    <option value="reminder">Nhắc nhở</option>
                </select>
                <select class="form-control" id="filterStatus">
                    <option value="">Tất cả trạng thái</option>
                    <option value="unread">Chưa đọc</option>
                    <option value="read">Đã đọc</option>
                </select>
                <input type="date" class="form-control" id="filterDate">
                <button class="btn btn-primary" id="applyFilters">
                    <i class="fas fa-filter mr-2"></i> Lọc
                </button>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="card">
        <div id="notificationsList">
            <!-- Loaded via AJAX -->
        </div>
    </div>
</div>

<style>
    .notification-item {
        padding: 1.25rem;
        border-bottom: 1px solid #e5e7eb;
        transition: background-color 0.2s;
        cursor: pointer;
        position: relative;
    }

    .notification-item:hover {
        background-color: #f9fafb;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-item.unread {
        background-color: #eff6ff;
    }

    .notification-item.unread::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background-color: var(--primary-color);
    }

    .notification-icon {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .notification-icon.budget {
        background-color: #fef3c7;
        color: #f59e0b;
    }

    .notification-icon.bill {
        background-color: #dbeafe;
        color: #3b82f6;
    }

    .notification-icon.goal {
        background-color: #d1fae5;
        color: #10b981;
    }

    .notification-icon.system {
        background-color: #e0e7ff;
        color: #6366f1;
    }

    .notification-content {
        flex: 1;
        min-width: 0;
    }

    .notification-title {
        font-weight: 600;
        margin-bottom: 0.25rem;
        color: #111827;
    }

    .notification-message {
        color: #6b7280;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }

    .notification-time {
        font-size: 0.75rem;
        color: #9ca3af;
    }

    .notification-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
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
        loadNotifications();

        $('#applyFilters').on('click', function () {
            loadNotifications();
        });

        $('#filterType, #filterStatus, #filterDate').on('change', function () {
            loadNotifications();
        });

        $('#markAllRead').click(function () {
            $.post('/api/data.php', {
                action: 'notifications_mark_all_read'
            }, function (response) {
                if (response.success) {
                    showToast('success', 'Đã đánh dấu tất cả thông báo là đã đọc');
                    loadNotifications();
                    loadUnreadCount();
                } else {
                    showToast('error', response.message || 'Có lỗi xảy ra');
                }
            });
        });

        loadUnreadCount();
    });

    function loadNotifications() {
        const filters = {
            action: 'notifications_list',
            status: $('#filterStatus').val(),
            type: $('#filterType').val(),
            date: $('#filterDate').val()
        };

        $.get('/api/data.php', filters, function (response) {
            if (response.success) {
                renderNotifications(response.data || []);
            } else {
                showToast('error', response.message || 'Có lỗi xảy ra');
            }
        });
    }

    function loadUnreadCount() {
        $.get('/api/data.php?action=notifications_unread_count', function (res) {
            if (res.success && res.data && typeof res.data.unread_count !== 'undefined') {
                if (typeof refreshNotificationBadge === 'function') {
                    refreshNotificationBadge(res.data.unread_count);
                } else {
                    $('#notif-badge').text(res.data.unread_count);
                }
            }
        });
    }

    function renderNotifications(notifications) {
        const container = $('#notificationsList');
        container.empty();

        if (notifications.length === 0) {
            container.html(`
            <div class="empty-state">
                <i class="fas fa-bell-slash"></i>
                <p class="text-lg font-medium mb-2">Không có thông báo</p>
                <p class="text-sm">Bạn chưa có thông báo nào</p>
            </div>
        `);
            return;
        }

        notifications.forEach(notif => {
            const isUnread = notif.is_read == 0;
            const iconClass = getIconClass(notif.type);
            const icon = getIcon(notif.type);
            const createdAt = notif.created_at || '';

            const html = `
            <div class="notification-item ${isUnread ? 'unread' : ''}" data-id="${notif.id}">
                <div class="flex gap-4">
                    <div class="notification-icon ${notif.type || 'info'}">
                        <i class="${icon}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${notif.title}</div>
                        <div class="notification-message">${notif.message}</div>
                        <div class="notification-time">
                            <i class="far fa-clock mr-1"></i> ${createdAt}
                        </div>
                    </div>
                    ${isUnread ? '<span class="notification-badge badge-primary">Mới</span>' : ''}
                    <div class="flex items-center gap-2">
                        <button class="btn btn-sm btn-outline text-danger" onclick="deleteNotification(${notif.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
            container.append(html);
        });

        // Click to mark as read
        $('.notification-item.unread').click(function () {
            const id = $(this).data('id');
            markAsRead(id, $(this));
        });
    }

    function markAsRead(id, element) {
        $.post('/api/data.php', {
            action: 'notifications_mark_read',
            id: id
        }, function (response) {
            if (response.success) {
                element.removeClass('unread');
                element.find('.notification-badge').remove();
                loadUnreadCount();
            } else {
                showToast('error', response.message || 'Có lỗi xảy ra');
            }
        });
    }

    function deleteNotification(id) {
        $.post('/api/data.php', {
            action: 'notifications_delete',
            id: id
        }, function (res) {
            if (res.success) {
                showToast('success', 'Đã xóa thông báo');
                loadNotifications();
                loadUnreadCount();
            } else {
                showToast('error', res.message || 'Có lỗi xảy ra');
            }
        });
    }

    function getIcon(type) {
        const icons = {
            'budget': 'fas fa-wallet',
            'bill': 'fas fa-file-invoice-dollar',
            'goal': 'fas fa-bullseye',
            'system': 'fas fa-info-circle',
            'info': 'fas fa-info-circle',
            'success': 'fas fa-check-circle',
            'warning': 'fas fa-exclamation-circle',
            'error': 'fas fa-times-circle',
            'reminder': 'fas fa-bell'
        };
        return icons[type] || 'fas fa-bell';
    }

    function getIconClass(type) {
        return type;
    }
</script>

<?php include 'partials/footer.php'; ?>
