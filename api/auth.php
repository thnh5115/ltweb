<?php
require_once '../../config.php';
require_once '../../functions.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($action === 'login') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Mock Admin Login
    if (($email === 'admin@example.com' || $email === 'admin') && $password === 'admin123') {
        $_SESSION['admin_id'] = 1;
        $_SESSION['admin_name'] = 'Super Admin';
        $_SESSION['role'] = 'admin';

        jsonResponse(true, 'Đăng nhập Admin thành công!', ['redirect' => '/public/admin/admin_dashboard.php']);
    }
    // Mock User Login
    elseif ($email === 'user@example.com' && $password === '123456') {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = 'Nguyễn Văn A';
        $_SESSION['user_email'] = $email;
        $_SESSION['role'] = 'user';

        jsonResponse(true, 'Đăng nhập thành công!', ['redirect' => '/public/user/dashboard.php']);
    } else {
        jsonResponse(false, 'Email hoặc mật khẩu không đúng.');
    }
} elseif ($action === 'register') {
    $fullname = $_POST['fullname'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        jsonResponse(false, 'Mật khẩu xác nhận không khớp.');
    }

    if ($email === 'exist@example.com') {
        jsonResponse(false, 'Email đã tồn tại.');
    }

    // Mock Registration Success
    jsonResponse(true, 'Đăng ký thành công! Vui lòng đăng nhập.');
} else {
    jsonResponse(false, 'Invalid action.');
}
?>