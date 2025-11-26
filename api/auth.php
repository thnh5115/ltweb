<?php
/**
 * Authentication API
 * Handles login, register, forgot password, and reset password actions
 * 
 * @author Senior PHP Developer
 * @version 1.0
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

// Prevent caching of API responses
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

require_once __DIR__ . '/../db/config.php';

/**
 * Send JSON response and terminate script
 * 
 * @param bool $success Success status
 * @param string $message Response message
 * @param array $data Additional data
 * @return void
 */
function jsonResponse($success, $message, $data = [])
{
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Get action from request
$action = $_POST['action'] ?? $_GET['action'] ?? null;

// Fallback: if action missing but có email/password gửi lên thì mặc định login
if (!$action && isset($_POST['email'], $_POST['password'])) {
    $action = 'login';
}

if (!$action) {
    jsonResponse(false, 'Missing action parameter');
}

// Dispatch to appropriate handler
switch ($action) {
    case 'login':
        handleLogin($pdo);
        break;

    case 'register':
        // TODO: Implement user registration
        jsonResponse(false, 'Chức năng đăng ký chưa được triển khai');
        break;

    case 'forgot_password':
        // TODO: Implement forgot password
        jsonResponse(false, 'Chức năng quên mật khẩu chưa được triển khai');
        break;

    case 'reset_password':
        // TODO: Implement reset password
        jsonResponse(false, 'Chức năng đặt lại mật khẩu chưa được triển khai');
        break;

    default:
        jsonResponse(false, 'Action không được hỗ trợ: ' . htmlspecialchars($action));
}

/**
 * Handle user login
 * 
 * @param PDO $pdo Database connection
 * @return void
 */
function handleLogin($pdo)
{
    // Get and sanitize input
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($email)) {
        jsonResponse(false, 'Vui lòng nhập email');
    }

    if (empty($password)) {
        jsonResponse(false, 'Vui lòng nhập mật khẩu');
    }

    // Basic email format validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(false, 'Email không hợp lệ');
    }

    try {
        // Query user from database
        $stmt = $pdo->prepare("
            SELECT id, fullname, email, password_hash, role 
            FROM users 
            WHERE email = :email 
            LIMIT 1
        ");

        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify user exists and password is correct
        if (!$user || !password_verify($password, $user['password_hash'])) {
            // Use same message for both cases to prevent user enumeration
            jsonResponse(false, 'Email hoặc mật khẩu không đúng');
        }

        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        // Store user information in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['fullname'];
        $_SESSION['user_role'] = $user['role'];

        // Determine redirect URL based on role
        $redirect = '/public/user/dashboard.php'; // Default fallback

        if ($user['role'] === 'ADMIN') {
            $redirect = '/public/admin/admin_dashboard.php';
        } elseif ($user['role'] === 'USER') {
            $redirect = '/public/user/dashboard.php';
        }

        // Optional: Update last login timestamp
        // $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
        // $updateStmt->execute([':id' => $user['id']]);

        // Return success response
        jsonResponse(true, 'Đăng nhập thành công', [
            'redirect' => $redirect
        ]);

    } catch (PDOException $e) {
        // Log error for debugging (in production, use proper logging)
        error_log("Login Error: " . $e->getMessage());

        // Return generic error message to user
        jsonResponse(false, 'Lỗi hệ thống, vui lòng thử lại sau');
    }
}
