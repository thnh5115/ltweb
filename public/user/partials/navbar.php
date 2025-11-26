<div id="main-content" class="main-content" role="main">
    <header class="top-navbar">
        <button id="sidebar-toggle" class="btn btn-outline mr-4 md:hidden" type="button" aria-label="Mo/ dong menu" aria-expanded="false" aria-controls="user-sidebar">
            <i class="fas fa-bars"></i>
        </button>
        <div class="flex justify-between w-full items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    <?php
                    $titles = [
                        'dashboard.php' => 'Tong quan',
                        'transactions.php' => 'Quan ly giao dich',
                        'categories.php' => 'Danh muc thu chi',
                        'statistics.php' => 'Bao cao thong ke',
                        'profile.php' => 'Thong tin ca nhan',
                        'budget_planner.php' => 'Ke hoach ngan sach',
                        'recurring_transactions.php' => 'Giao dich dinh ky',
                        'goals.php' => 'Muc tieu tiet kiem',
                        'bill_calendar.php' => 'Lich thanh toan',
                        'notifications.php' => 'Thong bao'
                    ];
                    echo isset($titles[basename($_SERVER['PHP_SELF'])]) ? $titles[basename($_SERVER['PHP_SELF'])] : 'Quan ly chi tieu';
                    ?>
                </h2>
            </div>
            <div class="flex items-center gap-4">
                <button id="theme-toggle" class="btn btn-ghost btn-icon theme-toggle" type="button" aria-label="Chuyen che do sang/toi">
                    <i class="fas fa-moon"></i>
                </button>
                <button class="relative p-2 hover:bg-gray-100 rounded-lg transition-colors btn-ghost btn-icon" type="button" aria-label="Xem thong bao">
                    <i class="fas fa-bell text-gray-600 text-lg"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-danger rounded-full" aria-hidden="true"></span>
                </button>
                <div class="user-info cursor-pointer px-3 py-2 rounded-lg transition-colors" role="button" tabindex="0" aria-label="Tai khoan">
                    <div class="user-meta hidden md:block text-right">
                        <p class="text-sm font-semibold text-gray-900" title="<?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User'; ?>">
                            <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'User'; ?>
                        </p>
                        <p class="text-xs text-muted">Nguoi dung</p>
                    </div>
                    <div class="avatar-circle w-10 h-10 rounded-full bg-gradient-primary flex items-center justify-center text-white shadow-md">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="content-body">
