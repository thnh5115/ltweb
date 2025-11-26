<div class="main-content">
    <header class="top-navbar">
        <button id="sidebar-toggle" class="btn btn-outline mr-4 md:hidden">
            <i class="fas fa-bars"></i>
        </button>
        <div class="flex justify-between w-full items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    <?php
                    $titles = [
                        'dashboard.php' => 'Tổng quan',
                        'transactions.php' => 'Quản lý giao dịch',
                        'categories.php' => 'Danh mục thu chi',
                        'statistics.php' => 'Báo cáo thống kê',
                        'profile.php' => 'Thông tin cá nhân',
                        'budget_planner.php' => 'Kế hoạch ngân sách',
                        'recurring_transactions.php' => 'Giao dịch định kỳ',
                        'goals.php' => 'Mục tiêu tiết kiệm',
                        'bill_calendar.php' => 'Lịch thanh toán',
                        'notifications.php' => 'Thông báo'
                    ];
                    echo isset($titles[basename($_SERVER['PHP_SELF'])]) ? $titles[basename($_SERVER['PHP_SELF'])] : 'Quản lý chi tiêu';
                    ?>
                </h2>
            </div>
            <div class="flex items-center gap-4">
                <!-- Notifications -->
                <button class="relative p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-bell text-gray-600 text-lg"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-danger rounded-full"></span>
                </button>

                <!-- User Menu -->
                <div
                    class="user-info flex items-center gap-3 cursor-pointer hover:bg-gray-50 px-3 py-2 rounded-lg transition-colors">
                    <div class="hidden md:block text-right">
                        <p class="text-sm font-semibold text-gray-900">
                            <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User'; ?>
                        </p>
                        <p class="text-xs text-muted">Người dùng</p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-full bg-gradient-primary flex items-center justify-center text-white shadow-md">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="content-body">