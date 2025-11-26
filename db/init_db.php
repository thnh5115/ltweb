<?php
require_once __DIR__ . '/../db/config.php';

echo "Dang ket noi DB...<br>";

try {
    // 1. Đọc file SQL
    $sql = file_get_contents(__DIR__ . '/system.sql');

    // 2. Thực thi
    $pdo->exec($sql);
    echo "Da tao bang users thanh cong.<br>";

    // 3. Cập nhật password hash chuẩn xác (vì hash trong file sql chỉ là placeholder hoặc copy cũ)
    // Admin: admin123
    $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
    // User: 123456
    $userPass = password_hash('123456', PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("UPDATE users SET password_hash = :pass WHERE email = :email");

    // Update Admin
    $stmt->execute([':pass' => $adminPass, ':email' => 'admin@test.com']);
    echo "Da update password cho admin@test.com (admin123)<br>";

    // Update User
    $stmt->execute([':pass' => $userPass, ':email' => 'user@test.com']);
    echo "Da update password cho user@test.com (123456)<br>";

    echo "DONE! DB Setup Complete.";

} catch (PDOException $e) {
    die("Lỗi: " . $e->getMessage());
}
