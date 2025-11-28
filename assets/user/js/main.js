$(document).ready(function () {
    const $themeToggle = $('#theme-toggle');
    const $sidebar = $('.sidebar');
    const $sidebarToggle = $('#sidebar-toggle, #admin-sidebar-toggle');
    const $toastContainer = $('.toast-container');

    $toastContainer.attr({ role: 'status', 'aria-live': 'polite' });

    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        $('body').addClass('theme-dark');
        updateThemeIcon(true);
    }

    $themeToggle.on('click keydown', function (event) {
        if (event.type === 'click' || event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            $('body').toggleClass('theme-dark');
            const isDark = $('body').hasClass('theme-dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            updateThemeIcon(isDark);
        }
    });

    // Sidebar Toggle
    $sidebarToggle.on('click', function () {
        $sidebar.toggleClass('open');
        $(this).attr('aria-expanded', $sidebar.hasClass('open'));
    });

    // Close sidebar when clicking outside on mobile
    $(document).click(function (event) {
        if (!$(event.target).closest('.sidebar, #sidebar-toggle, #admin-sidebar-toggle').length && $(window).width() <= 768) {
            $sidebar.removeClass('open');
            $sidebarToggle.attr('aria-expanded', 'false');
        }
    });

    // Allow closing toast with keyboard or click
    $(document).on('click keydown', '.toast-close', function (event) {
        if (event.type === 'click' || event.key === 'Enter' || event.key === ' ') {
            $(this).closest('.toast').fadeOut(200, function () {
                $(this).remove();
            });
        }
    });

    // Global AJAX Setup
    $.ajaxSetup({
        beforeSend: function () {
            showLoading();
        },
        complete: function () {
            hideLoading();
        },
        error: function (xhr, status, error) {
            showToast('error', 'Có lỗi xảy ra: ' + error);
        }
    });

    initNotificationDropdown();
    initAccountMenu();
    initAvatarFallbackHandler();
});

function updateThemeIcon(isDark) {
    const $icon = $('#theme-toggle i');
    if (!$icon.length) return;
    if (isDark) {
        $icon.removeClass('fa-moon').addClass('fa-sun');
    } else {
        $icon.removeClass('fa-sun').addClass('fa-moon');
    }
}

function showLoading() {
    $('#loading-overlay').attr('aria-hidden', 'false').fadeIn(180);
}

function hideLoading() {
    $('#loading-overlay').attr('aria-hidden', 'true').fadeOut(180);
}

function showToast(type, message) {
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };

    const titles = {
        success: 'Thành công',
        error: 'Có lỗi',
        warning: 'Chú ý',
        info: 'Thông báo'
    };

    const icon = icons[type] || icons.info;
    const title = titles[type] || titles.info;
    const toneClass = type === 'error' ? 'text-danger' : type === 'success' ? 'text-success' : 'text-primary-600';

    const toastHtml = `
        <div class="toast ${type}" role="alert" aria-live="polite">
            <i class="fas ${icon} ${toneClass}"></i>
            <div class="toast-body">
                <strong>${title}</strong>
                <p>${message}</p>
            </div>
            <button class="toast-close" aria-label="Đóng thông báo">&times;</button>
        </div>
    `;

    const $toast = $(toastHtml);
    $('.toast-container').append($toast);

    setTimeout(() => {
        $toast.fadeOut(300, function () {
            $(this).remove();
        });
    }, 3500);
}

function formatMoney(amount) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
}

function initNotificationDropdown() {
    const $toggle = $('#notifToggle');
    const $dropdown = $('#notifDropdown');
    const $list = $('#notifDropdownList');
    const $markAll = $('#notifMarkAllDropdown');

    if (!$toggle.length || !$dropdown.length) {
        return;
    }

    const closeDropdown = () => {
        $dropdown.removeClass('open');
        $dropdown.attr('aria-hidden', 'true');
        $toggle.attr('aria-expanded', 'false');
    };

    const openDropdown = () => {
        $dropdown.addClass('open');
        $dropdown.attr('aria-hidden', 'false');
        $toggle.attr('aria-expanded', 'true');
        loadDropdownNotifications();
    };

    const loadBadge = () => {
        $.get('/api/data.php?action=notifications_unread_count', function (res) {
            if (res.success && res.data) {
                refreshNotificationBadge(res.data.unread_count || 0);
            }
        });
    };

    const loadDropdownNotifications = () => {
        $list.html('<div class="notif-empty">Đang tải thông báo...</div>');
        $.get('/api/data.php', { action: 'notifications_list', limit: 5 }, function (res) {
            if (!res.success) {
                $list.html('<div class="notif-empty">Không thể tải thông báo</div>');
                return;
            }

            const items = res.data || [];
            if (!items.length) {
                $list.html('<div class="notif-empty">Không có thông báo mới</div>');
                return;
            }

            const html = items.map(item => createNotifItemHtml(item)).join('');
            $list.html(html);
        });
    };

    $toggle.on('click', function (e) {
        e.preventDefault();
        if ($dropdown.hasClass('open')) {
            closeDropdown();
        } else {
            openDropdown();
        }
    });

    $(document).on('click', function (e) {
        if (!$(e.target).closest('.notification-menu').length) {
            closeDropdown();
        }
    });

    $markAll.on('click', function (e) {
        e.preventDefault();
        $.post('/api/data.php', { action: 'notifications_mark_all_read' }, function (res) {
            if (res.success) {
                showToast('success', 'Đã đánh dấu tất cả thông báo là đã đọc');
                loadBadge();
                loadDropdownNotifications();
            } else {
                showToast('error', res.message || 'Không thể cập nhật thông báo');
            }
        });
    });

    $(document).on('click', '.notif-dropdown .notif-item', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        const link = $(this).data('link');
        markNotificationAsRead(id, () => {
            $(this).removeClass('unread');
            loadBadge();
            if (link) {
                window.location.href = link;
            }
        });
    });

    loadBadge();
}

function markNotificationAsRead(id, callback) {
    if (!id) {
        if (typeof callback === 'function') callback();
        return;
    }
    $.post('/api/data.php', { action: 'notifications_mark_read', id }, function (res) {
        if (res.success) {
            if (typeof callback === 'function') callback();
        }
    });
}

function refreshNotificationBadge(count) {
    const $badge = $('#notif-badge');
    if (!$badge.length) return;
    if (count > 0) {
        $badge.text(count > 99 ? '99+' : count);
        $badge.show();
    } else {
        $badge.hide();
    }
    updateAccountMenuBadge(count);
}

function createNotifItemHtml(item) {
    const isUnread = Number(item.is_read) === 0;
    const icon = getNotificationIcon(item.type);
    const safeTitle = escapeHtml(item.title || 'Thông báo');
    const safeMessage = escapeHtml(item.message || '');
    const timestamp = formatNotificationTime(item.created_at);
    const link = item.link_url ? escapeHtml(item.link_url) : '';

    return `
        <button type="button" class="notif-item ${isUnread ? 'unread' : ''}" data-id="${item.id}" data-link="${link}">
            <div class="notif-icon"><i class="${icon}"></i></div>
            <div class="notif-body">
                <h4>${safeTitle}</h4>
                <p>${safeMessage}</p>
                <div class="notif-time"><i class="far fa-clock mr-1"></i>${timestamp}</div>
            </div>
        </button>
    `;
}

function getNotificationIcon(type) {
    const map = {
        budget: 'fas fa-wallet',
        bill: 'fas fa-file-invoice-dollar',
        goal: 'fas fa-bullseye',
        system: 'fas fa-info-circle',
        success: 'fas fa-check-circle',
        warning: 'fas fa-exclamation-circle',
        error: 'fas fa-times-circle',
        reminder: 'fas fa-bell'
    };
    return map[type] || 'fas fa-bell';
}

function formatNotificationTime(value) {
    if (!value) return '';
    const date = new Date(value.replace(' ', 'T'));
    if (isNaN(date.getTime())) {
        return value;
    }
    return date.toLocaleString('vi-VN', { hour12: false });
}

function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function updateAccountAvatars(url, fullName) {
    const hasImage = typeof url === 'string' && /^https?:\/\//i.test(url);
    const $images = $('[data-avatar-type]');
    const $fallbacks = $('[data-avatar-fallback]');

    if (hasImage) {
        $images.each(function () {
            $(this).attr('src', url).removeClass('hidden');
        });
        $fallbacks.addClass('hidden');
    } else {
        $images.each(function () {
            $(this).attr('src', '').addClass('hidden');
        });
        $fallbacks.removeClass('hidden');
        if (fullName) {
            $('[data-avatar-initial]').text(getInitialFromName(fullName));
        }
    }
}

function getInitialFromName(name) {
    if (!name) {
        return 'U';
    }
    const trimmed = name.trim();
    if (!trimmed) {
        return 'U';
    }
    const parts = trimmed.split(/\s+/);
    const lastPart = parts[parts.length - 1];
    return (lastPart.charAt(0) || 'U').toUpperCase();
}

function initAccountMenu() {
    const $toggle = $('#accountMenuToggle');
    const $dropdown = $('#accountMenuDropdown');

    if (!$toggle.length || !$dropdown.length) {
        return;
    }

    const openMenu = () => {
        $dropdown.addClass('open');
        $dropdown.attr('aria-hidden', 'false');
        $toggle.attr('aria-expanded', 'true');
    };

    const closeMenu = () => {
        $dropdown.removeClass('open');
        $dropdown.attr('aria-hidden', 'true');
        $toggle.attr('aria-expanded', 'false');
    };

    $toggle.on('click keydown', function (event) {
        if (event.type === 'click' || event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            if ($dropdown.hasClass('open')) {
                closeMenu();
            } else {
                openMenu();
            }
        }
    });

    $(document).on('click.accountMenu', function (event) {
        if (!$(event.target).closest('.account-menu').length) {
            closeMenu();
        }
    });

    $(document).on('keydown.accountMenu', function (event) {
        if (event.key === 'Escape') {
            closeMenu();
        }
    });

    $dropdown.find('a').on('click', function () {
        closeMenu();
    });
}

function updateAccountMenuBadge(count) {
    const $pill = $('#accountMenuNotifCount');
    if (!$pill.length) return;
    if (count > 0) {
        $pill.text(count > 99 ? '99+' : count);
        $pill.show();
    } else {
        $pill.hide();
    }
}

function initAvatarFallbackHandler() {
    $(document).on('error', 'img[data-avatar-type]', function () {
        const $img = $(this);
        $img.addClass('hidden');
        const type = $img.data('avatar-type');
        $(`[data-avatar-fallback="${type}"]`).removeClass('hidden');
    });
}
