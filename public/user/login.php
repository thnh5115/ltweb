<?php
require_once '../../config.php';
require_once '../../functions.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - MoneyManager</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/design-system.css">
    <link rel="stylesheet" href="/assets/css/animations.css">
    <link rel="stylesheet" href="/assets/user/css/style.css">
    <meta name="theme-color" content="#0F2744">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bg-color);
        }

        .auth-card {
            width: 100%;
            max-width: 400px;
            background: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }
    </style>
</head>

<body>

    <div class="auth-container">
        <div class="auth-card">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-primary mb-2">MoneyManager</h1>
                <p class="text-muted">Đăng nhập để quản lý chi tiêu</p>
            </div>

            <form id="loginForm">
                <input type="hidden" name="action" value="login">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <div class="relative">
                        <input type="text" name="email" class="form-control" placeholder="Email hoặc Username" required
                            value="user@example.com">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••" required
                        value="123456">
                </div>
                <div class="form-group flex justify-between items-center text-sm">
                    <label class="flex items-center gap-2">
                        <input type="checkbox"> Ghi nhớ đăng nhập
                    </label>
                    <a href="#" class="text-primary">Quên mật khẩu?</a>
                </div>
                <button type="submit" class="btn btn-primary w-full">Đăng nhập</button>
            </form>

            <div class="mt-6 text-center text-sm">
                Chưa có tài khoản? <a href="register.php" class="text-primary font-bold">Đăng ký ngay</a>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container"></div>

    <script src="/assets/user/js/main.js"></script>
    <script>
        $(document).ready(function () {
            $('#loginForm').submit(function (e) {
                e.preventDefault();

                $.ajax({
                    url: '/api/auth.php',
                    type: 'POST',
                    dataType: 'json',
                    data: $(this).serialize(),
                    success: function (response) {
                        const success = response && response.success;
                        const message = response && response.message ? response.message : 'Đăng nhập không thành công.';
                        const redirect = response && response.data && response.data.redirect ? response.data.redirect : null;

                        if (success) {
                            showToast('success', message);
                            setTimeout(function () {
                                if (redirect) {
                                    window.location.href = redirect;
                                }
                            }, 800);
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
