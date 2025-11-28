<?php
require_once __DIR__ . '/config.php';

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
}

function formatMoney($amount)
{
    return number_format($amount, 0, ',', '.') . ' đ';
}

function getCurrentUser()
{
    return isset($_SESSION['user']) ? $_SESSION['user'] : null;
}

function jsonResponse($success, $message, $data = [], $extra = [])
{
    header('Content-Type: application/json; charset=utf-8');
    $payload = [
        'success' => $success,
        'message' => $message,
        'data' => $data
    ];

    if (is_array($extra) && !empty($extra)) {
        $payload = array_merge($payload, $extra);
    }

    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function logAdminAction($pdo, $adminId, $action, $description, $targetType = null, $targetId = null, array $metadata = [])
{
    try {
        $stmt = $pdo->prepare("
            INSERT INTO admin_logs (admin_id, action, description, target_type, target_id, meta, ip_address, user_agent)
            VALUES (:admin_id, :action, :description, :target_type, :target_id, :meta, :ip, :agent)
        ");
        $stmt->execute([
            ':admin_id' => $adminId,
            ':action' => $action,
            ':description' => $description,
            ':target_type' => $targetType,
            ':target_id' => $targetId,
            ':meta' => $metadata ? json_encode($metadata, JSON_UNESCAPED_UNICODE) : null,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            ':agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250)
        ]);
    } catch (Throwable $e) {
        error_log('admin log error: ' . $e->getMessage());
    }
}

function createNotification(PDO $pdo, int $userId, string $type, string $title, string $message, ?string $linkUrl = null): void
{
    $allowedTypes = ['info', 'success', 'warning', 'error', 'reminder'];
    if (!in_array($type, $allowedTypes, true)) {
        $type = 'info';
    }

    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, link_url) VALUES (:user_id, :type, :title, :message, :link)");
    $stmt->execute([
        ':user_id' => $userId,
        ':type' => $type,
        ':title' => $title,
        ':message' => $message,
        ':link' => $linkUrl ?: null
    ]);
}

function notificationExists(PDO $pdo, int $userId, string $title, ?string $linkUrl = null): bool
{
    $sql = "SELECT id FROM notifications WHERE user_id = :user_id AND title = :title";
    $params = [
        ':user_id' => $userId,
        ':title' => $title
    ];

    if ($linkUrl !== null) {
        $sql .= " AND link_url = :link";
        $params[':link'] = $linkUrl;
    } else {
        $sql .= " AND link_url IS NULL";
    }

    $sql .= " LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    return (bool) $stmt->fetchColumn();
}

function createNotificationIfMissing(PDO $pdo, int $userId, string $type, string $title, string $message, ?string $linkUrl = null): void
{
    if (!notificationExists($pdo, $userId, $title, $linkUrl)) {
        createNotification($pdo, $userId, $type, $title, $message, $linkUrl);
    }
}
?>
