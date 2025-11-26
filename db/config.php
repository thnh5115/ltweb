<?php
// db/config.php - resilient connection (local or docker)

$dbConfigs = [
    [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'name' => getenv('DB_NAME') ?: 'expense_manager',
        'user' => getenv('DB_USER') ?: 'user',
        'pass' => getenv('DB_PASS') ?: 'password',
    ],
    [
        'host' => 'db',
        'name' => 'expense_manager',
        'user' => 'user',
        'pass' => 'password',
    ],
    [
        'host' => 'localhost',
        'name' => 'expense_manager',
        'user' => 'user',
        'pass' => 'password',
    ],
];

$pdo = null;
$lastError = '';

foreach ($dbConfigs as $cfg) {
    try {
        $pdo = new PDO(
            "mysql:host={$cfg['host']};dbname={$cfg['name']};charset=utf8mb4",
            $cfg['user'],
            $cfg['pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        break;
    } catch (PDOException $e) {
        $lastError = $e->getMessage();
        continue;
    }
}

if (!$pdo) {
    die('Database connection failed: ' . $lastError);
}
