$(document).ready(function() {
    // Sidebar Toggle
    $('#sidebar-toggle').click(function() {
        $('.sidebar').toggleClass('open');
    });

    // Close sidebar when clicking outside on mobile
    $(document).click(function(event) {
        if (!$(event.target).closest('.sidebar, #sidebar-toggle').length && $(window).width() <= 768) {
            $('.sidebar').removeClass('open');
        }
    });

    // Global AJAX Setup
    $.ajaxSetup({
        beforeSend: function() {
            showLoading();
        },
        complete: function() {
            hideLoading();
        },
        error: function(xhr, status, error) {
            showToast('error', 'Có lỗi xảy ra: ' + error);
        }
    });
});

function showLoading() {
    $('#loading-overlay').fadeIn(200);
}

function hideLoading() {
    $('#loading-overlay').fadeOut(200);
}

function showToast(type, message) {
    const icon = type === 'success' ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-exclamation-circle text-danger"></i>';
    const toastHtml = `
        <div class="toast ${type}">
            ${icon}
            <div>${message}</div>
        </div>
    `;
    
    const $toast = $(toastHtml);
    $('.toast-container').append($toast);
    
    setTimeout(() => {
        $toast.fadeOut(300, function() {
            $(this).remove();
        });
    }, 3000);
}

function formatMoney(amount) {
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
}
