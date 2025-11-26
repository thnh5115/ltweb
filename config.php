<?php
session_start();

// Define Base URL
define('BASE_URL', '/public/user/');

// Database Connection
$host = 'db'; // Docker service name
$db = 'expense_manager';
$user = 'user';
$pass = 'password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

// Uncomment to enable real DB connection
// try {
//      $pdo = new PDO($dsn, $user, $pass, $options);
// } catch (\PDOException $e) {
//      // Fallback to mock data if DB fails or not set up
//      // throw new \PDOException($e->getMessage(), (int)$e->getCode());
// }

// Set timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Error reporting (Turn off for production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>