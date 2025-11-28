<?php
require_once '../../config.php';
require_once '../../functions.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

// Get token from URL
$token = $_GET['token'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - MoneyManager</title>
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

        .password-strength {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            margin-top: 0.5rem;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s;
            border-radius: 2px;
        }

        .strength-weak {
            background: #ef4444;
            width: 33%;
        }

        .strength-medium {
            background: #f59e0b;
            width: 66%;
        }

        .strength-strong {
            background: #10b981;
            width: 100%;
        }

        .password-requirements {
            font-size: 0.875rem;
            margin-top: 0.5rem;
            color: #6b7280;
        }

        .password-requirements li {
            padding: 0.25rem 0;
        }

        .password-requirements li.met {
            color: #10b981;
        }

        .password-requirements li.met i {
            color: #10b981;
        }
    </style>
</head>

<body>

    <div class="auth-container">
        <div class="auth-card">
            <div class="text-center mb-6">
                <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-4"
                    style="width: 64px; height: 64px; background: rgba(16, 185, 129, 0.1); margin: 0 auto 1rem;">
                    <i class="fas fa-lock text-primary text-2xl"
                        style="font-size: 1.5rem; color: var(--primary-color);"></i>
                </div>
                <h1 class="text-2xl font-bold mb-2">Đặt lại mật khẩu</h1>
                <p class="text-muted">Nhập mật khẩu mới của bạn</p>
            </div>

            <form id="resetPasswordForm">
                <input type="hidden" name="action" value="reset_password">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <div class="form-group">
                    <label class="form-label">Mật khẩu mới</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••"
                        required>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                    <ul class="password-requirements">
                        <li id="req-length"><i class="fas fa-circle" style="font-size: 6px;"></i> Ít nhất 8 ký tự</li>
                        <li id="req-upper"><i class="fas fa-circle" style="font-size: 6px;"></i> Có chữ hoa</li>
                        <li id="req-number"><i class="fas fa-circle" style="font-size: 6px;"></i> Có số</li>
                    </ul>
                </div>

                <div class="form-group">
                    <label class="form-label">Xác nhận mật khẩu</label>
                    <input type="password" name="confirm_password" id="confirmPassword" class="form-control"
                        placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-primary w-full mb-4">
                    <i class="fas fa-check mr-2"></i> Đặt lại mật khẩu
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
            // Password strength checker
            $('#password').on('input', function () {
                const password = $(this).val();
                let strength = 0;

                // Check requirements
                const hasLength = password.length >= 8;
                const hasUpper = /[A-Z]/.test(password);
                const hasNumber = /[0-9]/.test(password);

                $('#req-length').toggleClass('met', hasLength);
                $('#req-upper').toggleClass('met', hasUpper);
                $('#req-number').toggleClass('met', hasNumber);

                if (hasLength) strength++;
                if (hasUpper) strength++;
                if (hasNumber) strength++;

                // Update strength bar
                const strengthBar = $('#strengthBar');
                strengthBar.removeClass('strength-weak strength-medium strength-strong');

                if (strength === 1) {
                    strengthBar.addClass('strength-weak');
                } else if (strength === 2) {
                    strengthBar.addClass('strength-medium');
                } else if (strength === 3) {
                    strengthBar.addClass('strength-strong');
                }
            });

            $('#resetPasswordForm').submit(function (e) {
                e.preventDefault();

                const password = $('#password').val();
                const confirmPassword = $('#confirmPassword').val();

                if (password !== confirmPassword) {
                    showToast('error', 'Mật khẩu xác nhận không khớp!');
                    return;
                }

                if (password.length < 8) {
                    showToast('error', 'Mật khẩu phải có ít nhất 8 ký tự!');
                    return;
                }

                $.ajax({
                    url: '/api/auth.php',
                    type: 'POST',
                    dataType: 'json',
                    data: $(this).serialize(),
                    success: function (response) {
                        if (response.success) {
                            showToast('success', response.message);
                            setTimeout(function () {
                                window.location.href = 'login.php';
                            }, 2000);
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
