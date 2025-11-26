<div id="main-content" class="main-content" role="main">
    <header class="top-navbar admin-navbar">
        <button id="admin-sidebar-toggle" class="btn btn-outline btn-icon mr-4 md:hidden" type="button"
            aria-label="Mở/đóng menu quản trị" aria-expanded="false" aria-controls="admin-sidebar">
            <i class="fas fa-bars"></i>
        </button>
        <div class="flex justify-between w-full items-center">
            <h2 class="text-lg font-bold">
                <?php
                $titles = [
                    'admin_dashboard.php' => 'Tổng quan hệ thống',
                    'admin_users.php' => 'Quản lý người dùng',
                    'admin_categories.php' => 'Quản lý danh mục',
                    'admin_transactions.php' => 'Quản lý giao dịch',
                    'admin_statistics.php' => 'Báo cáo thống kê',
                    'admin_logs.php' => 'Nhật ký hệ thống',
                    'admin_profile.php' => 'Hồ sơ Admin'
                ];
                echo isset($titles[basename($_SERVER['PHP_SELF'])]) ? $titles[basename($_SERVER['PHP_SELF'])] : 'Admin Panel';
                ?>
            </h2>
            <div class="user-info flex items-center gap-2" role="button" tabindex="0"
                aria-label="Tài khoản quản trị">
                <span class="text-sm font-medium hidden md:block">
                    <?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin'; ?>
                </span>
                <div class="w-8 h-8 rounded-full bg-blue-800 text-white flex items-center justify-center">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
            <button id="theme-toggle" class="btn btn-ghost btn-icon theme-toggle" type="button"
                aria-label="Chuyển đổi chế độ sáng/tối">
                <i class="fas fa-moon"></i>
            </button>
        </div>
    </header>
    <div class="content-body">
