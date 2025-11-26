<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar" id="user-sidebar">
    <div class="sidebar-header">
        <div class="brand">
            <i class="fas fa-wallet"></i>
            <span>MoneyManager</span>
        </div>
    </div>
    <nav class="sidebar-menu" aria-label="Điều hướng người dùng">
        <ul>
            <li>
                <a href="dashboard.php"
                    class="menu-item <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>"
                    aria-current="<?php echo $current_page == 'dashboard.php' ? 'page' : 'false'; ?>">
                    <i class="fas fa-home"></i> Tổng quan
                </a>
            </li>
            <li>
                <a href="transactions.php"
                    class="menu-item <?php echo $current_page == 'transactions.php' ? 'active' : ''; ?>"
                    aria-current="<?php echo $current_page == 'transactions.php' ? 'page' : 'false'; ?>">
                    <i class="fas fa-exchange-alt"></i> Giao dịch
                </a>
            </li>
            <li>
                <a href="categories.php"
                    class="menu-item <?php echo $current_page == 'categories.php' ? 'active' : ''; ?>"
                    aria-current="<?php echo $current_page == 'categories.php' ? 'page' : 'false'; ?>">
                    <i class="fas fa-list"></i> Danh mục
                </a>
            </li>
            <li>
                <a href="budget_planner.php"
                    class="menu-item <?php echo $current_page == 'budget_planner.php' ? 'active' : ''; ?>"
                    aria-current="<?php echo $current_page == 'budget_planner.php' ? 'page' : 'false'; ?>">
                    <i class="fas fa-wallet"></i> Ngân sách
                </a>
            </li>
            <li>
                <a href="recurring_transactions.php"
                    class="menu-item <?php echo $current_page == 'recurring_transactions.php' ? 'active' : ''; ?>"
                    aria-current="<?php echo $current_page == 'recurring_transactions.php' ? 'page' : 'false'; ?>">
                    <i class="fas fa-sync-alt"></i> Định kỳ
                </a>
            </li>
            <li>
                <a href="goals.php" class="menu-item <?php echo $current_page == 'goals.php' ? 'active' : ''; ?>"
                    aria-current="<?php echo $current_page == 'goals.php' ? 'page' : 'false'; ?>">
                    <i class="fas fa-bullseye"></i> Mục tiêu
                </a>
            </li>
            <li>
                <a href="bill_calendar.php"
                    class="menu-item <?php echo $current_page == 'bill_calendar.php' ? 'active' : ''; ?>"
                    aria-current="<?php echo $current_page == 'bill_calendar.php' ? 'page' : 'false'; ?>">
                    <i class="fas fa-calendar-alt"></i> Hóa đơn
                </a>
            </li>
            <li>
                <a href="notifications.php"
                    class="menu-item <?php echo $current_page == 'notifications.php' ? 'active' : ''; ?>"
                    aria-current="<?php echo $current_page == 'notifications.php' ? 'page' : 'false'; ?>">
                    <i class="fas fa-bell"></i> Thông báo
                </a>
            </li>
            <li>
                <a href="statistics.php"
                    class="menu-item <?php echo $current_page == 'statistics.php' ? 'active' : ''; ?>"
                    aria-current="<?php echo $current_page == 'statistics.php' ? 'page' : 'false'; ?>">
                    <i class="fas fa-chart-pie"></i> Báo cáo
                </a>
            </li>
            <li>
                <a href="profile.php" class="menu-item <?php echo $current_page == 'profile.php' ? 'active' : ''; ?>"
                    aria-current="<?php echo $current_page == 'profile.php' ? 'page' : 'false'; ?>">
                    <i class="fas fa-user"></i> Tài khoản
                </a>
            </li>
        </ul>
    </nav>
    <div class="p-4 border-t">
        <a href="logout.php" class="menu-item text-danger">
            <i class="fas fa-sign-out-alt"></i> Đăng xuất
        </a>
    </div>
</aside>
