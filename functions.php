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
?>
