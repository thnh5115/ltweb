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
