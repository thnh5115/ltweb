<?php
/**
 * Authentication API
 * Handles login, register, forgot password, and reset password actions
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json; charset=utf-8');

// Prevent caching of API responses
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

require_once __DIR__ . '/../functions.php';

// Merge GET/POST with JSON body (JSON overrides)
$rawBody = file_get_contents('php://input');
$jsonBody = json_decode($rawBody, true);
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
        handleRegister($pdo, $requestData);
        break;

    case 'forgot_password':
        handleForgotPassword($pdo, $requestData);
        break;

    case 'reset_password':
        handleResetPassword($pdo, $requestData);
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
    $email = strtolower(trim($request['email'] ?? ''));
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
            SELECT id, fullname, email, password_hash, role, status, avatar_url
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

        if (($user['status'] ?? 'INACTIVE') !== 'ACTIVE') {
            $message = 'Tài khoản của bạn đang ở trạng thái không cho phép đăng nhập';
            if ($user['status'] === 'BANNED') {
                $message = 'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.';
            } elseif ($user['status'] === 'INACTIVE') {
                $message = 'Tài khoản của bạn chưa được kích hoạt. Vui lòng kiểm tra email kích hoạt hoặc liên hệ hỗ trợ.';
            }
            jsonResponse(false, $message);
        }

        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);

        // Store user information in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['fullname'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_avatar'] = $user['avatar_url'] ?? null;

        // Nếu là admin, đặt thêm session dành riêng cho admin
        if (in_array($user['role'], ['ADMIN'], true)) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_email'] = $user['email'];
            $_SESSION['admin_name'] = $user['fullname'];
            $_SESSION['admin_role'] = $user['role'];

            logAdminAction($pdo, $user['id'], 'ADMIN_LOGIN', 'Đăng nhập admin thành công');
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

/**
 * Handle user registration
 */
function handleRegister($pdo, array $request)
{
    $fullname = trim($request['fullname'] ?? '');
    $email = strtolower(trim($request['email'] ?? ''));
    $password = $request['password'] ?? '';
    $confirm = $request['confirm_password'] ?? '';

    if ($fullname === '' || $email === '' || $password === '') {
        jsonResponse(false, 'Vui lòng điền đầy đủ thông tin');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(false, 'Email không hợp lệ');
    }

    if (strlen($password) < 8) {
        jsonResponse(false, 'Mật khẩu phải có ít nhất 8 ký tự');
    }

    if ($password !== $confirm) {
        jsonResponse(false, 'Mật khẩu xác nhận không khớp');
    }

    // Check email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        jsonResponse(false, 'Email đã được sử dụng');
    }

    try {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password_hash, role, status) VALUES (?, ?, ?, 'USER', 'ACTIVE')");
        $stmt->execute([$fullname, $email, $hash]);

        jsonResponse(true, 'Đăng ký tài khoản thành công. Vui lòng đăng nhập.');
    } catch (PDOException $e) {
        error_log("Register Error: " . $e->getMessage());
        jsonResponse(false, 'Lỗi hệ thống khi đăng ký');
    }
}

/**
 * Handle forgot password
 */
function handleForgotPassword($pdo, array $request)
{
    $email = strtolower(trim($request['email'] ?? ''));

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(false, 'Email không hợp lệ');
    }

    // Check if email exists
    $stmt = $pdo->prepare("SELECT id, fullname FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        jsonResponse(false, 'Email không tồn tại trong hệ thống');
    }

    try {
        $pdo->beginTransaction();

        // Clean existing tokens of this user and expired tokens in general
        $pdo->prepare("DELETE FROM password_resets WHERE user_id = :uid OR expires_at < NOW()")
            ->execute([':uid' => $user['id']]);

        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');

        $stmtInsert = $pdo->prepare("
            INSERT INTO password_resets (user_id, token_hash, expires_at, requested_ip, requested_agent)
            VALUES (:user_id, :token_hash, :expires_at, :ip, :agent)
        ");
        $stmtInsert->execute([
            ':user_id' => $user['id'],
            ':token_hash' => $tokenHash,
            ':expires_at' => $expiresAt,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            ':agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250)
        ]);

        $pdo->commit();

        error_log("Password reset token for {$email}: {$token}");

        jsonResponse(true, 'Chúng tôi đã gửi hướng dẫn đặt lại mật khẩu. (Demo: token đã được log)', [
            'token' => $token,
            'expires_at' => $expiresAt
        ]);
    } catch (Throwable $e) {
        $pdo->rollBack();
        error_log('Forgot password error: ' . $e->getMessage());
        jsonResponse(false, 'Không thể tạo yêu cầu đặt lại mật khẩu. Vui lòng thử lại.');
    }
}

/**
 * Handle password reset via token
 */
function handleResetPassword($pdo, array $request)
{
    $token = trim($request['token'] ?? '');
    $password = (string) ($request['password'] ?? '');
    $confirm = (string) ($request['confirm_password'] ?? $request['confirm'] ?? '');

    if ($token === '') {
        jsonResponse(false, 'Thiếu token đặt lại mật khẩu');
    }

    if (strlen($password) < 6) {
        jsonResponse(false, 'Mật khẩu phải có ít nhất 6 ký tự');
    }

    if ($confirm !== '' && $password !== $confirm) {
        jsonResponse(false, 'Mật khẩu xác nhận không khớp');
    }

    $tokenHash = hash('sha256', $token);

    $stmt = $pdo->prepare("
        SELECT pr.id, pr.user_id, pr.expires_at, pr.used_at, u.email
        FROM password_resets pr
        INNER JOIN users u ON u.id = pr.user_id
        WHERE pr.token_hash = :token_hash
        LIMIT 1
    ");
    $stmt->execute([':token_hash' => $tokenHash]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$record) {
        jsonResponse(false, 'Token không hợp lệ hoặc đã sử dụng');
    }

    if ($record['used_at']) {
        jsonResponse(false, 'Token đã được sử dụng');
    }

    if (new DateTime($record['expires_at']) < new DateTime()) {
        jsonResponse(false, 'Token đã hết hạn, vui lòng tạo yêu cầu mới');
    }

    try {
        $pdo->beginTransaction();

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password_hash = :hash WHERE id = :user_id")
            ->execute([
                ':hash' => $hash,
                ':user_id' => $record['user_id']
            ]);

        $pdo->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = :id")
            ->execute([':id' => $record['id']]);

        // Optional: delete other pending tokens for this user
        $pdo->prepare("DELETE FROM password_resets WHERE user_id = :uid AND used_at IS NULL AND id != :id")
            ->execute([':uid' => $record['user_id'], ':id' => $record['id']]);

        $pdo->commit();

        jsonResponse(true, 'Đặt lại mật khẩu thành công. Bạn có thể đăng nhập bằng mật khẩu mới.');
    } catch (Throwable $e) {
        $pdo->rollBack();
        error_log('Reset password error: ' . $e->getMessage());
        jsonResponse(false, 'Không thể đặt lại mật khẩu. Vui lòng thử lại.');
    }
}
