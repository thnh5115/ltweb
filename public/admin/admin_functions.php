<?php
require_once __DIR__ . '/../../config.php';

function isAdminLoggedIn()
{
    return isset($_SESSION['admin_id']);
}

function requireAdminLogin()
{
    if (!isAdminLoggedIn()) {
        header('Location: /public/user/login.php');
        exit;
    }
}
?>