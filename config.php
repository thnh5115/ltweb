<?php
session_start();

// Define Base URL
define('BASE_URL', '/public/user/');

// Database Connection
require_once __DIR__ . '/db/config.php';

// Set timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Error reporting (Turn off for production)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
?>