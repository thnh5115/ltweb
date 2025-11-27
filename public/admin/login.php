<?php
require_once '../../config.php';
require_once 'admin_functions.php';

// Nếu đã đăng nhập admin thì chuyển luôn vào dashboard
if (isAdminLoggedIn()) {
    header('Location: admin_dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Admin - MoneyManager</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/design-system.css">
    <link rel="stylesheet" href="/assets/css/animations.css">
    <link rel="stylesheet" href="/assets/user/css/style.css">
    <link rel="stylesheet" href="/assets/admin/css/admin.css">
    <meta name="theme-color" content="#0F2744">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at 20% 20%, rgba(91, 138, 232, 0.12), transparent 35%), radial-gradient(circle at 80% 0%, rgba(108, 92, 231, 0.15), transparent 32%), var(--gray-100);
            padding: 1.5rem;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            background: white;
            padding: 2.25rem;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--gray-200);
        }

        .auth-card .form-label {
            font-weight: var(--font-semibold);
        }
    </style>
</head>

<body class="admin-body">
    <div class="auth-container">
        <div class="auth-card">
            <div class="text-center mb-6">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-primary text-white shadow-lg mb-3">
                    <i class="fas fa-shield-alt"></i>
                    <span class="font-semibold">Bảng điều khiển Admin</span>
                </div>
                <h1 class="text-2xl font-bold text-primary mb-2">MoneyManager</h1>
                <p class="text-muted">Đăng nhập tài khoản quản trị</p>
            </div>

            <form id="adminLoginForm" autocomplete="off">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="admin@example.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary w-full mt-2">
                    <i class="fas fa-sign-in-alt mr-2"></i> Đăng nhập
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-muted">
                Dành riêng cho quản trị viên. Người dùng thông thường vui lòng đăng nhập tại <a class="text-primary font-semibold" href="/public/user/login.php">trang người dùng</a>.
            </div>
        </div>
    </div>

    <div class="toast-container" aria-live="polite" aria-atomic="true"></div>

    <script src="/assets/user/js/main.js"></script>
    <script>
        $(document).ready(function () {
            $('#adminLoginForm').on('submit', function (e) {
                e.preventDefault();

                const email = $.trim($('#email').val());
                const password = $('#password').val();

                $.ajax({
                    url: '/api/auth.php',
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'login',
                        email: email,
                        password: password
                    },
                    success: function (res) {
                        const success = res && res.success;
                        const message = (res && res.message) ? res.message : 'Đăng nhập không thành công.';
                        const redirect = res && res.data && res.data.redirect ? res.data.redirect : '/public/admin/admin_dashboard.php';

                        if (success) {
                            showToast('success', message);
                            setTimeout(function () {
                                window.location.href = redirect;
                            }, 500);
                        } else {
                            showToast('error', message);
                        }
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Có lỗi xảy ra, vui lòng thử lại.';
                        showToast('error', msg);
                    }
                });
            });
        });
    </script>
</body>

</html>
