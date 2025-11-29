<?php
// db/config.php - Kết nối DB cho container PHP + MySQL chung

$host = getenv('DB_HOST') ?: '127.0.0.1';
$name = getenv('DB_NAME') ?: 'expense_manager';
$user = getenv('DB_USER') ?: 'user';
$pass = getenv('DB_PASS') ?: 'password';
$port = getenv('DB_PORT') ?: 3306;

try {
    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

    $pdo = new PDO(
        $dsn,
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    // Đảm bảo charset
    $pdo->exec("SET NAMES utf8mb4");
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
