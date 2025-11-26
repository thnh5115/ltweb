<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar admin-sidebar" id="admin-sidebar">
    <div class="sidebar-header">
        <div class="brand">
            <i class="fas fa-shield-alt"></i>
            <span>AdminPanel</span>
        </div>
    </div>
    <nav class="sidebar-menu" aria-label="Điều hướng quản trị">
        <ul>
            <li>
                <a href="admin_dashboard.php"
                    class="menu-item <?php echo $current_page == 'admin_dashboard.php' ? 'active' : ''; ?>"
                    aria-current="<?php echo $current_page == 'admin_dashboard.php' ? 'page' : 'false'; ?>">
                    <i class="fas fa-tachometer-alt"></i> Tổng quan
                </a>
            </li>
            <li>
                <a href="admin_users.php"
                    class="menu-item <?php echo $current_page == 'admin_users.php' ? 'active' : ''; ?>"
                    aria-current="<?php echo $current_page == 'admin_users.php' ? 'page' : 'false'; ?>">
                    <i class="fas fa-users"></i> Người dùng
                </a>
            </li>
            <li>
                <a href="admin_categories.php"
                    class="menu-item <?php echo $current_page == 'admin_categories.php' ? 'active' : ''; ?>"
                    aria-current="<?php echo $current_page == 'admin_categories.php' ? 'page' : 'false'; ?>">
                    <i class="fas fa-list"></i> Danh mục
                </a>
            </li>
            <li>
                <a href="admin_transactions.php"
                    class="menu-item <?php echo $current_page == 'admin_transactions.php' ? 'active' : ''; ?>"
                    aria-current="<?php echo $current_page == 'admin_transactions.php' ? 'page' : 'false'; ?>">
                    <i class="fas fa-exchange-alt"></i> Giao dịch
                </a>
            </li>
            <li>
                <a href="admin_statistics.php"
                    class="menu-item <?php echo $current_page == 'admin_statistics.php' ? 'active' : ''; ?>"
                    aria-current="<?php echo $current_page == 'admin_statistics.php' ? 'page' : 'false'; ?>">
                    <i class="fas fa-chart-bar"></i> Thống kê
                </a>
            </li>
            <li>
                <a href="admin_logs.php"
                    class="menu-item <?php echo $current_page == 'admin_logs.php' ? 'active' : ''; ?>"
                    aria-current="<?php echo $current_page == 'admin_logs.php' ? 'page' : 'false'; ?>">
                    <i class="fas fa-history"></i> Nhật ký
                </a>
            </li>
            <li>
                <a href="admin_support.php"
                    class="menu-item <?php echo $current_page == 'admin_support.php' ? 'active' : ''; ?>"
                    aria-current="<?php echo $current_page == 'admin_support.php' ? 'page' : 'false'; ?>">
                    <i class="fas fa-headset"></i> Hỗ trợ
                </a>
            </li>
            <li>
                <a href="admin_reports.php"
                    class="menu-item <?php echo $current_page == 'admin_reports.php' ? 'active' : ''; ?>"
                    aria-current="<?php echo $current_page == 'admin_reports.php' ? 'page' : 'false'; ?>">
                    <i class="fas fa-file-alt"></i> Báo cáo
                </a>
            </li>
            <li>
                <a href="admin_system_health.php"
                    class="menu-item <?php echo $current_page == 'admin_system_health.php' ? 'active' : ''; ?>"
                    aria-current="<?php echo $current_page == 'admin_system_health.php' ? 'page' : 'false'; ?>">
                    <i class="fas fa-heartbeat"></i> Hệ thống
                </a>
            </li>
            <li>
                <a href="admin_settings.php"
                    class="menu-item <?php echo $current_page == 'admin_settings.php' ? 'active' : ''; ?>"
                    aria-current="<?php echo $current_page == 'admin_settings.php' ? 'page' : 'false'; ?>">
                    <i class="fas fa-cog"></i> Cài đặt
                </a>
            </li>
            <li>
                <a href="admin_profile.php"
                    class="menu-item <?php echo $current_page == 'admin_profile.php' ? 'active' : ''; ?>"
                    aria-current="<?php echo $current_page == 'admin_profile.php' ? 'page' : 'false'; ?>">
                    <i class="fas fa-user-shield"></i> Hồ sơ
                </a>
            </li>
        </ul>
    </nav>
    <div class="p-4 border-t border-gray-700">
        <a href="admin_logout.php" class="menu-item text-danger">
            <i class="fas fa-sign-out-alt"></i> Đăng xuất
        </a>
    </div>
</aside>
