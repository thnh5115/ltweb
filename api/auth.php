<?php
/**
 * Authentication API
 * Handles login, register, forgot password, and reset password actions
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
 * @param bool   $success Success status
 * @param string $message Response message
 * @param array  $data    Additional data
 * @return void
 */
function jsonResponse($success, $message, $data = [])
{
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data'    => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Merge GET/POST with JSON body (JSON overrides)
$rawBody     = file_get_contents('php://input');
$jsonBody    = json_decode($rawBody, true);
$requestData = array_merge(
    $_GET ?? [],
    $_POST ?? [],
    is_array($jsonBody) ? $jsonBody : []
);

// Get action from request
$action = $requestData['action'] ?? null;

// Fallback: if action is missing but email/password present, default to login
if (!$action && isset($requestData['email'], $requestData['password'])) {
    $action = 'login';
}

if (!$action) {
    jsonResponse(false, 'Missing action parameter');
}

// Dispatch to appropriate handler
switch ($action) {
    case 'login':
        handleLogin($pdo, $requestData);
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
 * Handle user login (user + admin)
 *
 * @param PDO   $pdo     Database connection
 * @param array $request Request payload (GET/POST/JSON merged)
 * @return void
 */
function handleLogin($pdo, array $request)
{
    // Get and normalize input
    $email    = strtolower(trim($request['email'] ?? ''));
    $password = (string) ($request['password'] ?? '');

    // Validate input
    if ($email === '') {
        jsonResponse(false, 'Vui lòng nhập email');
    }

    if ($password === '') {
        jsonResponse(false, 'Vui lòng nhập mật khẩu');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(false, 'Email không hợp lệ');
    }

    try {
        // Query user from database
        $stmt = $pdo->prepare("
            SELECT id, fullname, email, password_hash, role
            FROM users
            WHERE LOWER(email) = :email
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
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name']  = $user['fullname'];
        $_SESSION['user_role']  = $user['role'];

        // Nếu là admin, đặt thêm session dành riêng cho admin
        if ($user['role'] === 'ADMIN') {
            $_SESSION['admin_id']    = $user['id'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['admin_name']  = $user['fullname'];
        }

        // Determine redirect URL based on role
        $redirect = '/public/user/dashboard.php'; // Default fallback
        if ($user['role'] === 'ADMIN') {
            $redirect = '/public/admin/admin_dashboard.php';
        } elseif ($user['role'] === 'USER') {
            $redirect = '/public/user/dashboard.php';
        }

        jsonResponse(true, 'Đăng nhập thành công', [
            'redirect' => $redirect
        ]);

    } catch (PDOException $e) {
        // Log error for debugging (in production, use proper logging)
        error_log("Login Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi hệ thống, vui lòng thử lại sau');
    }
}
