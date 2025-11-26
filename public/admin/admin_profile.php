<?php
require_once '../../config.php';
require_once 'admin_functions.php';
requireAdminLogin();

include 'partials/header.php';
include 'partials/sidebar.php';
include 'partials/navbar.php';
?>

<div class="container">
    <h2 class="text-xl font-bold mb-6">Hồ sơ Admin</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Profile Card -->
        <div class="card text-center">
            <div
                class="w-24 h-24 rounded-full bg-blue-800 text-white flex items-center justify-center text-3xl mx-auto mb-4">
                <i class="fas fa-user-shield"></i>
            </div>
            <h3 class="text-lg font-bold"><?php echo $_SESSION['admin_name'] ?? 'Admin'; ?></h3>
            <p class="text-muted mb-2">Super Admin</p>
            <p class="text-sm text-muted">Tham gia: 01/01/2023</p>
        </div>

        <!-- Edit Info -->
        <div class="card md:col-span-2">
            <div class="card-header">
                <h3 class="card-title">Cập nhật thông tin</h3>
            </div>
            <form id="adminProfileForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="form-group">
                        <label class="form-label">Tên hiển thị</label>
                        <input type="text" class="form-control"
                            value="<?php echo $_SESSION['admin_name'] ?? 'Super Admin'; ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" value="admin@example.com" readonly>
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary" style="background-color: #1e40af;">Lưu thay
                        đổi</button>
                </div>
            </form>

            <hr class="my-6 border-gray-200">

            <div class="card-header">
                <h3 class="card-title">Đổi mật khẩu</h3>
            </div>
            <form id="adminPasswordForm">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="form-group">
                        <label class="form-label">Mật khẩu hiện tại</label>
                        <input type="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mật khẩu mới</label>
                        <input type="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Xác nhận mật khẩu mới</label>
                        <input type="password" class="form-control">
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary" style="background-color: #1e40af;">Đổi mật
                        khẩu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#adminProfileForm').submit(function (e) {
            e.preventDefault();
            showToast('success', 'Cập nhật thông tin thành công!');
        });

        $('#adminPasswordForm').submit(function (e) {
            e.preventDefault();
            showToast('success', 'Đổi mật khẩu thành công!');
        });
    });
</script>

<?php include 'partials/footer.php'; ?>