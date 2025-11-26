<?php
require_once '../../config.php';
require_once '../../functions.php';

// If already logged in, redirect to dashboard
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
    <title>Quên mật khẩu - MoneyManager</title>
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
            max-width: 450px;
            background: white;
            padding: 2.5rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .success-message {
            background: #d1fae5;
            border: 1px solid #10b981;
            color: #065f46;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: none;
        }

        .success-message i {
            margin-right: 0.5rem;
        }
    </style>
</head>

<body>

    <div class="auth-container">
        <div class="auth-card">
            <div class="text-center mb-6">
                <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-4"
                    style="width: 64px; height: 64px; background: rgba(16, 185, 129, 0.1); margin: 0 auto 1rem;">
                    <i class="fas fa-key text-primary text-2xl"
                        style="font-size: 1.5rem; color: var(--primary-color);"></i>
                </div>
                <h1 class="text-2xl font-bold mb-2">Quên mật khẩu?</h1>
                <p class="text-muted">Nhập email của bạn để nhận link đặt lại mật khẩu</p>
            </div>

            <div class="success-message" id="successMessage">
                <i class="fas fa-check-circle"></i>
                <span>Link đặt lại mật khẩu đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư!</span>
            </div>

            <form id="forgotPasswordForm">
                <input type="hidden" name="action" value="forgot_password">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <div class="relative">
                        <input type="email" name="email" class="form-control" placeholder="your@email.com" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-full mb-4">
                    <i class="fas fa-paper-plane mr-2"></i> Gửi link đặt lại
                </button>

                <div class="text-center text-sm">
                    <a href="login.php" class="text-primary">
                        <i class="fas fa-arrow-left mr-1"></i> Quay lại đăng nhập
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container"></div>

    <script src="/assets/user/js/main.js"></script>
    <script>
        $(document).ready(function () {
            $('#forgotPasswordForm').submit(function (e) {
                e.preventDefault();

                $.ajax({
                    url: '/api/auth.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            $('#forgotPasswordForm').hide();
                            $('#successMessage').fadeIn();
                            showToast('success', response.message);
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
