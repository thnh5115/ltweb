<?php
require_once '../../config.php';
require_once '../../functions.php';
requireLogin();

include 'partials/header.php';
include 'partials/sidebar.php';
include 'partials/navbar.php';
?>

<div class="main-content">
    <div class="content-body">
        <!-- Page Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">
                <i class="fas fa-user-circle mr-3 text-primary-600"></i>
                Thông tin cá nhân
            </h2>
            <p class="text-muted">Quản lý thông tin tài khoản của bạn</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Sidebar -->
            <div class="lg:col-span-1">
                <!-- Avatar Card -->
                <div class="card text-center">
                    <div
                        class="w-32 h-32 mx-auto rounded-full bg-gradient-primary flex items-center justify-center text-white text-5xl shadow-xl mb-4">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-1"><?php echo $_SESSION['user_name'] ?? 'User'; ?></h3>
                    <p class="text-muted mb-4"><?php echo $_SESSION['user_email'] ?? 'user@example.com'; ?></p>
                    <button class="btn btn-outline w-full mb-3">
                        <i class="fas fa-camera mr-2"></i> Đổi ảnh đại diện
                    </button>
                    <button class="btn btn-outline w-full text-danger"
                        onclick="if(confirm('Bạn có chắc muốn đăng xuất?')) window.location.href='logout.php'">
                        <i class="fas fa-sign-out-alt mr-2"></i> Đăng xuất
                    </button>
                </div>

                <!-- Quick Stats -->
                <div class="card mt-6">
                    <h4 class="font-bold mb-4 flex items-center">
                        <i class="fas fa-chart-line mr-2 text-primary-600"></i>
                        Thống kê nhanh
                    </h4>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-muted">Tổng giao dịch</span>
                            <span class="font-bold text-lg" id="user-transaction-count">0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-muted">Danh mục</span>
                            <span class="font-bold text-lg" id="user-category-count">0</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-muted">Ngày tham gia</span>
                            <span class="font-bold text-sm" id="user-join-date">01/01/2024</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-muted">Trạng thái</span>
                            <span class="badge badge-success">Hoạt động</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Info Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-id-card mr-2 text-primary-600"></i>
                            Thông tin cá nhân
                        </h3>
                    </div>
                    <form id="updateProfileForm" class="p-6">
                        <input type="hidden" name="action" value="update_profile">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user mr-1"></i> Họ và tên
                                </label>
                                <input type="text" name="name" class="form-control"
                                    value="<?php echo $_SESSION['user_name'] ?? ''; ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-envelope mr-1"></i> Email
                                </label>
                                <input type="email" name="email" class="form-control"
                                    value="<?php echo $_SESSION['user_email'] ?? ''; ?>" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-phone mr-1"></i> Số điện thoại
                                </label>
                                <input type="tel" name="phone" class="form-control" placeholder="0123456789">
                            </div>
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-birthday-cake mr-1"></i> Ngày sinh
                                </label>
                                <input type="date" name="birthday" class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-map-marker-alt mr-1"></i> Địa chỉ
                            </label>
                            <textarea name="address" class="form-control" rows="2"
                                placeholder="Nhập địa chỉ của bạn"></textarea>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change Password Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-lock mr-2 text-primary-600"></i>
                            Đổi mật khẩu
                        </h3>
                    </div>
                    <form id="changePasswordForm" class="p-6">
                        <input type="hidden" name="action" value="change_password">

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-key mr-1"></i> Mật khẩu hiện tại
                            </label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-lock mr-1"></i> Mật khẩu mới
                                </label>
                                <input type="password" name="new_password" id="new-password" class="form-control"
                                    required>
                                <small class="text-muted">Tối thiểu 6 ký tự</small>
                            </div>
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-check-circle mr-1"></i> Xác nhận mật khẩu
                                </label>
                                <input type="password" name="confirm_password" id="confirm-password"
                                    class="form-control" required>
                            </div>
                        </div>

                        <!-- Password Strength Indicator -->
                        <div class="mb-4">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-sm text-muted">Độ mạnh:</span>
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div id="password-strength" class="h-2 rounded-full transition-all"
                                        style="width: 0%; background: var(--danger);"></div>
                                </div>
                                <span id="strength-text" class="text-sm font-semibold">Yếu</span>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-key mr-2"></i> Đổi mật khẩu
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Preferences Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-cog mr-2 text-primary-600"></i>
                            Tùy chọn
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium">Thông báo email</p>
                                <p class="text-sm text-muted">Nhận thông báo qua email</p>
                            </div>
                            <div class="toggle-switch">
                                <input type="checkbox" id="email-notifications" checked>
                                <span class="toggle-slider"></span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium">Nhắc nhở ngân sách</p>
                                <p class="text-sm text-muted">Cảnh báo khi vượt ngân sách</p>
                            </div>
                            <div class="toggle-switch">
                                <input type="checkbox" id="budget-alerts" checked>
                                <span class="toggle-slider"></span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium">Chế độ tối</p>
                                <p class="text-sm text-muted">Giao diện tối cho mắt</p>
                            </div>
                            <div class="toggle-switch">
                                <input type="checkbox" id="dark-mode">
                                <span class="toggle-slider"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        loadUserStats();

        // Update Profile
        $('#updateProfileForm').submit(function (e) {
            e.preventDefault();
            $.post('/api/data.php', $(this).serialize(), function (res) {
                if (res.success) {
                    showToast('success', res.message);
                } else {
                    showToast('error', res.message);
                }
            });
        });

        // Change Password
        $('#changePasswordForm').submit(function (e) {
            e.preventDefault();

            const newPass = $('#new-password').val();
            const confirmPass = $('#confirm-password').val();

            if (newPass !== confirmPass) {
                showToast('error', 'Mật khẩu xác nhận không khớp');
                return;
            }

            if (newPass.length < 6) {
                showToast('error', 'Mật khẩu phải có ít nhất 6 ký tự');
                return;
            }

            $.post('/api/data.php', $(this).serialize(), function (res) {
                if (res.success) {
                    showToast('success', res.message);
                    $('#changePasswordForm')[0].reset();
                    $('#password-strength').css('width', '0%');
                    $('#strength-text').text('Yếu');
                } else {
                    showToast('error', res.message);
                }
            });
        });

        // Password Strength Indicator
        $('#new-password').on('keyup', function () {
            const password = $(this).val();
            let strength = 0;

            if (password.length >= 6) strength += 25;
            if (password.length >= 10) strength += 25;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 25;
            if (/[0-9]/.test(password)) strength += 15;
            if (/[^a-zA-Z0-9]/.test(password)) strength += 10;

            let color = '#EF4444';
            let text = 'Yếu';

            if (strength >= 75) {
                color = '#10B981';
                text = 'Mạnh';
            } else if (strength >= 50) {
                color = '#F59E0B';
                text = 'Trung bình';
            }

            $('#password-strength').css({
                'width': strength + '%',
                'background': color
            });
            $('#strength-text').text(text).css('color', color);
        });

        // Toggle switches
        $('.toggle-switch input').on('change', function () {
            const setting = $(this).attr('id');
            const enabled = $(this).is(':checked');
            showToast('success', 'Đã cập nhật tùy chọn');
        });
    });

    function loadUserStats() {
        $.get('/api/data.php?action=user_stats', function (res) {
            if (res.success) {
                $('#user-transaction-count').text(res.data.transactions);
                $('#user-category-count').text(res.data.categories);
                $('#user-join-date').text(res.data.join_date);
            }
        });
    }
</script>

<?php include 'partials/footer.php'; ?>