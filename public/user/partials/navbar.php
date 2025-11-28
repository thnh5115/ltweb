<?php
$navbarUserName = $_SESSION['user_name'] ?? 'User';
$navbarUserEmail = $_SESSION['user_email'] ?? 'user@example.com';
$navbarAvatar = $_SESSION['user_avatar'] ?? '';
$navbarHasAvatar = !empty($navbarAvatar);
if (!empty($navbarUserName)) {
    if (function_exists('mb_substr')) {
        $navbarInitial = mb_strtoupper(mb_substr($navbarUserName, 0, 1, 'UTF-8'), 'UTF-8');
    } else {
        $navbarInitial = strtoupper(substr($navbarUserName, 0, 1));
    }
} else {
    $navbarInitial = 'U';
}
?>
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
                <div class="account-menu relative">
                    <button id="accountMenuToggle" class="user-info cursor-pointer px-3 py-2 rounded-lg transition-colors" type="button" aria-haspopup="true" aria-expanded="false" aria-controls="accountMenuDropdown">
                        <div class="user-meta hidden md:block text-right">
                            <p class="text-sm font-semibold text-gray-900" title="<?php echo htmlspecialchars($navbarUserName, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($navbarUserName, ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                            <p class="text-xs text-muted">Nguoi dung</p>
                        </div>
                        <div class="avatar-circle w-10 h-10 rounded-full bg-gradient-primary flex items-center justify-center text-white shadow-md">
                            <img src="<?php echo $navbarHasAvatar ? htmlspecialchars($navbarAvatar, ENT_QUOTES, 'UTF-8') : ''; ?>" alt="Avatar" class="avatar-image<?php echo $navbarHasAvatar ? '' : ' hidden'; ?>" data-avatar-type="navbar">
                            <i class="fas fa-user<?php echo $navbarHasAvatar ? ' hidden' : ''; ?>" data-avatar-fallback="navbar"></i>
                        </div>
                        <i class="fas fa-chevron-down text-xs text-muted ml-2"></i>
                    </button>
                    <div id="accountMenuDropdown" class="account-dropdown" role="menu" aria-hidden="true">
                        <div class="account-card">
                            <div class="account-user flex items-center gap-3">
                                <div class="avatar-circle w-12 h-12 rounded-full bg-gradient-primary flex items-center justify-center text-white shadow-md">
                                    <img src="<?php echo $navbarHasAvatar ? htmlspecialchars($navbarAvatar, ENT_QUOTES, 'UTF-8') : ''; ?>" alt="Avatar" class="avatar-image<?php echo $navbarHasAvatar ? '' : ' hidden'; ?>" data-avatar-type="dropdown">
                                    <div class="avatar-fallback-text<?php echo $navbarHasAvatar ? ' hidden' : ''; ?>" data-avatar-fallback="dropdown">
                                        <span data-avatar-initial><?php echo htmlspecialchars($navbarInitial, ENT_QUOTES, 'UTF-8'); ?></span>
                                    </div>
                                </div>
                                <div>
                                    <p class="account-name" title="<?php echo htmlspecialchars($navbarUserName, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($navbarUserName, ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p class="account-email" title="<?php echo htmlspecialchars($navbarUserEmail, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($navbarUserEmail, ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="account-menu-list">
                            <a href="/public/user/profile.php" class="account-menu-item" role="menuitem">
                                <span><i class="fas fa-user-circle mr-2"></i> Trang ca nhan</span>
                                <i class="fas fa-chevron-right text-xs text-muted"></i>
                            </a>
                            <a href="/public/user/notifications.php" class="account-menu-item" role="menuitem">
                                <span><i class="fas fa-bell mr-2"></i> Thong bao</span>
                                <span class="account-pill" id="accountMenuNotifCount" style="display:none;">0</span>
                            </a>
                        </div>
                        <div class="account-menu-footer">
                            <a href="/public/user/logout.php" class="account-menu-item danger" role="menuitem">
                                <span><i class="fas fa-sign-out-alt mr-2"></i> Dang xuat</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="content-body">
