<?php
require_once __DIR__ . '/../../config.php';

function isAdminLoggedIn()
{
    if (isset($_SESSION['admin_id'])) {
        return true;
    }

    // Nếu đã đăng nhập user với role ADMIN thì đồng bộ lại session admin
    if (isset($_SESSION['user_id'], $_SESSION['user_role']) && $_SESSION['user_role'] === 'ADMIN') {
        $_SESSION['admin_id']    = $_SESSION['user_id'];
        $_SESSION['admin_email'] = $_SESSION['user_email'] ?? null;
        $_SESSION['admin_name']  = $_SESSION['user_name'] ?? null;
        return true;
    }

    return false;
}

function requireAdminLogin()
{
    if (!isAdminLoggedIn()) {
        header('Location: /public/admin/login.php');
        exit;
    }
}
?>
