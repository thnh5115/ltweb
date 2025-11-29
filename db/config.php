<?php
// db/config.php - resilient connection (local or docker)

$host = getenv(''DB_HOST'') ?: ''db'';
$name = getenv(''DB_NAME'') ?: ''expense_manager'';
$user = getenv(''DB_USER'') ?: ''user'';
$pass = getenv(''DB_PASS'') ?: ''password'';
$port = getenv(''DB_PORT'') ?: 3306;

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ATTR_ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    // Execute charset setting after connection
    $pdo->exec("SET NAMES utf8mb4");
} catch (PDOException $e) {
    die(''Database connection failed: '' . $e->getMessage());
}
