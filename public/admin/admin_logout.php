<?php
require_once '../../config.php';

// Xóa toàn bộ session liên quan đến admin/user
$_SESSION['admin_id'] = null;
unset($_SESSION['admin_id'], $_SESSION['admin_email'], $_SESSION['admin_name']);
unset($_SESSION['user_id'], $_SESSION['user_email'], $_SESSION['user_name'], $_SESSION['user_role']);

header('Location: /public/admin/login.php');
exit;
