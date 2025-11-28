<?php
require_once '../../config.php';
require_once 'admin_functions.php';
requireAdminLogin();

include 'partials/header.php';
include 'partials/sidebar.php';
include 'partials/navbar.php';
?>

<div class="container">
    <div class="mb-6">
        <h2 class="text-xl font-bold">Cài đặt hệ thống</h2>
        <p class="text-muted text-sm">Quản lý cấu hình toàn hệ thống</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Settings Menu -->
        <div class="card" style="height: fit-content;">
            <div class="p-4">
                <div class="settings-menu">
                    <button class="settings-menu-item active" data-section="general">
                        <i class="fas fa-cog mr-2"></i> Cài đặt chung
                    </button>
                    <button class="settings-menu-item" data-section="budget">
                        <i class="fas fa-wallet mr-2"></i> Ngân sách
                    </button>
                    <button class="settings-menu-item" data-section="notifications">
                        <i class="fas fa-bell mr-2"></i> Thông báo
                    </button>
                    <button class="settings-menu-item" data-section="features">
                        <i class="fas fa-toggle-on mr-2"></i> Tính năng
                    </button>
                    <button class="settings-menu-item" data-section="security">
                        <i class="fas fa-shield-alt mr-2"></i> Bảo mật
                    </button>
                </div>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="lg:col-span-2">
            <!-- General Settings -->
            <div class="settings-section active" id="section-general">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Cài đặt chung</h3>
                    </div>
                    <form class="settings-form" data-section="general">
                        <div class="form-group">
                            <label class="form-label">Tên ứng dụng</label>
                            <input type="text" name="app_name" class="form-control" value="MoneyManager">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Múi giờ</label>
                            <select name="timezone" class="form-control">
                                <option value="Asia/Ho_Chi_Minh" selected>Việt Nam (GMT+7)</option>
                                <option value="Asia/Bangkok">Bangkok (GMT+7)</option>
                                <option value="Asia/Singapore">Singapore (GMT+8)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Định dạng tiền tệ</label>
                            <select name="currency_format" class="form-control">
                                <option value="vnd" selected>VNĐ</option>
                                <option value="usd">USD</option>
                                <option value="eur">EUR</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ngôn ngữ</label>
                            <select name="language" class="form-control">
                                <option value="vi" selected>Tiếng Việt</option>
                                <option value="en">English</option>
                            </select>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary" style="background-color: #1e40af;">
                                <i class="fas fa-save mr-2"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Budget Settings -->
            <div class="settings-section" id="section-budget">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Cài đặt ngân sách</h3>
                    </div>
                    <form class="settings-form" data-section="budget">
                        <div class="form-group">
                            <label class="form-label">Ngân sách mặc định (VNĐ)</label>
                            <input type="number" name="default_budget" class="form-control" value="10000000">
                            <small class="text-muted">Ngân sách mặc định cho người dùng mới</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ngưỡng cảnh báo (%)</label>
                            <input type="number" name="warning_threshold" class="form-control" value="80" min="0"
                                max="100">
                            <small class="text-muted">Cảnh báo khi chi tiêu đạt % này của ngân sách</small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ngưỡng vượt ngân sách (%)</label>
                            <input type="number" name="exceeded_threshold" class="form-control" value="100" min="0"
                                max="200">
                            <small class="text-muted">Đánh dấu vượt ngân sách khi đạt % này</small>
                        </div>
                        <div class="form-group">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="auto_reset_budget" value="1" checked>
                                <span>Tự động reset ngân sách hàng tháng</span>
                            </label>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary" style="background-color: #1e40af;">
                                <i class="fas fa-save mr-2"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notification Settings -->
            <div class="settings-section" id="section-notifications">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Cài đặt thông báo</h3>
                    </div>
                    <form class="settings-form" data-section="notifications">
                        <div class="form-group">
                            <label class="form-label font-bold">Email thông báo</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="email_budget_alert" value="1" checked>
                                    <span>Cảnh báo vượt ngân sách</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="email_bill_reminder" value="1" checked>
                                    <span>Nhắc nhở hóa đơn sắp đến hạn</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="email_goal_achieved" value="1" checked>
                                    <span>Thông báo đạt mục tiêu</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="email_monthly_report" value="1">
                                    <span>Báo cáo tháng</span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Thời gian nhắc nhở hóa đơn (ngày trước)</label>
                            <input type="number" name="bill_reminder_days" class="form-control" value="3" min="1"
                                max="30">
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary" style="background-color: #1e40af;">
                                <i class="fas fa-save mr-2"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Feature Settings -->
            <div class="settings-section" id="section-features">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Quản lý tính năng</h3>
                    </div>
                    <form class="settings-form" data-section="features">
                        <div class="form-group">
                            <label class="form-label font-bold">Tính năng người dùng</label>
                            <div class="space-y-3">
                                <div class="feature-toggle">
                                    <div>
                                        <div class="font-medium">Kế hoạch ngân sách</div>
                                        <small class="text-muted">Cho phép người dùng tạo kế hoạch ngân sách</small>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="feature_budget_planner" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="feature-toggle">
                                    <div>
                                        <div class="font-medium">Giao dịch định kỳ</div>
                                        <small class="text-muted">Quản lý các khoản chi định kỳ</small>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="feature_recurring" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="feature-toggle">
                                    <div>
                                        <div class="font-medium">Mục tiêu tiết kiệm</div>
                                        <small class="text-muted">Theo dõi mục tiêu tiết kiệm</small>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="feature_goals" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="feature-toggle">
                                    <div>
                                        <div class="font-medium">Lịch hóa đơn</div>
                                        <small class="text-muted">Quản lý và nhắc nhở hóa đơn</small>
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="feature_bill_calendar" checked>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary" style="background-color: #1e40af;">
                                <i class="fas fa-save mr-2"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="settings-section" id="section-security">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Cài đặt bảo mật</h3>
                    </div>
                    <form class="settings-form" data-section="security">
                        <div class="form-group">
                            <label class="form-label">Thời gian hết hạn phiên (phút)</label>
                            <input type="number" name="session_timeout" class="form-control" value="60" min="15"
                                max="1440">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Độ dài mật khẩu tối thiểu</label>
                            <input type="number" name="min_password_length" class="form-control" value="8" min="6"
                                max="20">
                        </div>
                        <div class="form-group">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="require_strong_password" value="1" checked>
                                <span>Yêu cầu mật khẩu mạnh (chữ hoa, số, ký tự đặc biệt)</span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="enable_2fa" value="1">
                                <span>Bật xác thực 2 yếu tố (2FA)</span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="log_failed_logins" value="1" checked>
                                <span>Ghi log đăng nhập thất bại</span>
                            </label>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary" style="background-color: #1e40af;">
                                <i class="fas fa-save mr-2"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .settings-menu {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .settings-menu-item {
        width: 100%;
        padding: 0.75rem 1rem;
        text-align: left;
        background: none;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        color: #6b7280;
        font-weight: 500;
    }

    .settings-menu-item:hover {
        background-color: #f3f4f6;
        color: #1e40af;
    }

    .settings-menu-item.active {
        background-color: #1e40af;
        color: white;
    }

    .settings-section {
        display: none;
    }

    .settings-section.active {
        display: block;
    }

    .feature-toggle {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background-color: #f9fafb;
        border-radius: 8px;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 52px;
        height: 28px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: 0.3s;
        border-radius: 28px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 22px;
        width: 22px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }

    input:checked+.toggle-slider {
        background-color: #1e40af;
    }

    input:checked+.toggle-slider:before {
        transform: translateX(24px);
    }

    .space-y-2>*+* {
        margin-top: 0.5rem;
    }

    .space-y-3>*+* {
        margin-top: 0.75rem;
    }
</style>

<script>
    $(document).ready(function () {
        loadSettings();

        // Section navigation
        $('.settings-menu-item').click(function () {
            const section = $(this).data('section');
            $('.settings-menu-item').removeClass('active');
            $(this).addClass('active');
            $('.settings-section').removeClass('active');
            $('#section-' + section).addClass('active');
        });

        // Form submissions
        $('.settings-form').submit(function (e) {
            e.preventDefault();
            const section = $(this).data('section');

            // Collect all form data including checkboxes
            const settings = {};

            // Get all named inputs within this form
            $(this).find('[name]').each(function() {
                const $input = $(this);
                const name = $input.attr('name');

                if ($input.attr('type') === 'checkbox') {
                    settings[name] = $input.is(':checked') ? 1 : 0;
                } else {
                    settings[name] = $input.val() || '';
                }
            });

            console.log('Collected settings:', settings); // Debug log

            // Prepare form data
            const formData = new FormData();
            formData.append('action', 'admin_update_settings');

            // Add settings as individual form fields
            Object.keys(settings).forEach(key => {
                formData.append('settings[' + key + ']', settings[key]);
            });

            $.ajax({
                url: '/api/admin_data.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    console.log('API response:', response); // Debug log
                    if (response.success) {
                        showToast('success', 'Đã lưu cài đặt thành công');
                        // Reload settings to update UI
                        loadSettings();
                    } else {
                        showToast('error', response.message || 'Lỗi khi lưu cài đặt');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error); // Debug log
                    showToast('error', 'Lỗi kết nối khi lưu cài đặt');
                }
            });
        });
    });

    function loadSettings() {
        $.get('/api/admin_data.php?action=admin_get_settings', function (response) {
            if (response.success) {
                // Populate form fields with saved settings
                const settings = response.data;
                Object.keys(settings).forEach(key => {
                    const input = $(`[name="${key}"]`);
                    if (input.attr('type') === 'checkbox') {
                        input.prop('checked', settings[key] == 1);
                    } else {
                        input.val(settings[key]);
                    }
                });
            } else {
                showToast('error', 'Không thể tải cài đặt: ' + (response.message || 'Lỗi không xác định'));
            }
        }).fail(function() {
            showToast('error', 'Lỗi kết nối khi tải cài đặt');
        });
    }
</script>

<?php include 'partials/footer.php'; ?>