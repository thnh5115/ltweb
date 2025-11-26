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
    <title>Đăng ký - MoneyManager</title>
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
            padding: 1rem;
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
                <p class="text-muted">Tạo tài khoản mới</p>
            </div>

            <form id="registerForm">
                <input type="hidden" name="action" value="register">
                <div class="form-group">
                    <label class="form-label">Họ và tên</label>
                    <input type="text" name="fullname" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Xác nhận mật khẩu</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-full">Đăng ký</button>
            </form>

            <div class="mt-6 text-center text-sm">
                Đã có tài khoản? <a href="login.php" class="text-primary font-bold">Đăng nhập</a>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container"></div>

    <script src="/assets/user/js/main.js"></script>
    <script>
        $(document).ready(function () {
            $('#registerForm').submit(function (e) {
                e.preventDefault();

                const password = $('input[name="password"]').val();
                const confirm = $('input[name="confirm_password"]').val();

                if (password !== confirm) {
                    showToast('error', 'Mật khẩu xác nhận không khớp!');
                    return;
                }

                $.ajax({
                    url: '/api/auth.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            showToast('success', response.message);
                            setTimeout(function () {
                                window.location.href = 'login.php';
                            }, 1500);
                        } else {
                            showToast('error', response.message);
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>
