<?php
require_once '../../config.php';
session_start();
unset($_SESSION['admin_id']);
unset($_SESSION['admin_name']);
header('Location: /public/user/login.php');
exit;
?>