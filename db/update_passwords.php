<?php
// db/update_passwords.php
// Chạy file này để update lại password hash chuẩn cho user mẫu
require_once __DIR__ . '/config.php';

try {
    // Password mới: "password"
    $newPass = password_hash('password', PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("UPDATE users SET password_hash = :pass WHERE email = :email");

    $stmt->execute([':pass' => $newPass, ':email' => 'admin@test.com']);
    echo "Updated Admin password to 'password'.<br>";

    $stmt->execute([':pass' => $newPass, ':email' => 'user@test.com']);
    echo "Updated User password to 'password'.<br>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
