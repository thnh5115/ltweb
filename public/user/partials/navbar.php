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
                <div class="notification-menu relative">
                    <button id="notifToggle" class="btn btn-ghost btn-icon" type="button" aria-label="Xem thong bao" aria-expanded="false">
                        <i class="fas fa-bell text-gray-600 text-lg"></i>
                        <span id="notif-badge" class="notif-count" aria-live="polite" aria-atomic="true" style="display:none;">0</span>
                    </button>
                    <div id="notifDropdown" class="notif-dropdown" aria-hidden="true">
                        <div class="notif-dropdown-header">
                            <div>
                                <p class="font-semibold text-gray-900">Thông báo</p>
                                <p class="text-xs text-muted">Cập nhật mới nhất</p>
                            </div>
                            <button type="button" class="notif-clear" id="notifMarkAllDropdown">
                                <i class="fas fa-check-double mr-1"></i>
                                Đánh dấu đã đọc
                            </button>
                        </div>
                        <div id="notifDropdownList" class="notif-dropdown-list">
                            <div class="notif-empty">Không có thông báo</div>
                        </div>
                        <a href="/public/user/notifications.php" class="notif-view-all">Xem tất cả</a>
                    </div>
                </div>
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
