<?php
// db/config.php - Kết nối DB linh hoạt (local, docker, Render)

// 1. Ưu tiên đọc từ biến môi trường (Render / server thật)
$dbConfigs = [
    [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'name' => getenv('DB_NAME') ?: 'expense_manager',
        'user' => getenv('DB_USER') ?: 'user',
        'pass' => getenv('DB_PASS') ?: 'password',
        'port' => getenv('DB_PORT') ?: 3306,
    ],
    // 2. Cấu hình cho docker-compose (service name: db)
    [
        'host' => 'db',
        'name' => 'expense_manager',
        'user' => 'user',
        'pass' => 'password',
        'port' => 3306,
    ],
    // 3. Fallback cho XAMPP / local
    [
        'host' => 'localhost',
        'name' => 'expense_manager',
        'user' => 'user',
        'pass' => 'password',
        'port' => 3306,
    ],
];

$pdo = null;
$lastError = '';

foreach ($dbConfigs as $cfg) {
    try {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $cfg['host'],
            $cfg['port'],
            $cfg['name']
        );

        $pdo = new PDO(
            $dsn,
            $cfg['user'],
            $cfg['pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );

        // Set charset sau khi kết nối
        $pdo->exec("SET NAMES utf8mb4");
        break;
    } catch (PDOException $e) {
        $lastError = $e->getMessage();
        continue;
    }
}

if (!$pdo) {
    // PRODUCTION: nên log chứ không echo lỗi, 
    // nhưng để bạn debug khi mới deploy:
    die('Database connection failed: ' . $lastError);
}
