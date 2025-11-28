$(document).ready(function () {
    // Admin specific hooks can be added here when needed.
});

function confirmDelete(id, type) {
    return confirm(`Bạn có chắc chắn muốn xóa ${type} này không?`);
}

function formatMoney(amount) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
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
