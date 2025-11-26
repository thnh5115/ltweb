<div class="main-content">
    <header class="top-navbar admin-header">
        <button id="admin-sidebar-toggle" class="btn btn-outline mr-4 md:hidden">
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
            <div class="user-info flex items-center gap-2">
                <span class="text-sm font-medium hidden md:block">
                    <?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin'; ?>
                </span>
                <div class="w-8 h-8 rounded-full bg-blue-800 text-white flex items-center justify-center">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
        </div>
    </header>
    <div class="content-body">