<?php
require_once __DIR__ . '/../../config.php';

function isAdminLoggedIn()
{
    if (isset($_SESSION['admin_id'])) {
        return true;
    }

    $allowedRoles = ['ADMIN'];
    if (isset($_SESSION['user_id'], $_SESSION['user_role']) && in_array($_SESSION['user_role'], $allowedRoles, true)) {
        $_SESSION['admin_id']    = $_SESSION['user_id'];
        $_SESSION['admin_email'] = $_SESSION['user_email'] ?? null;
        $_SESSION['admin_name']  = $_SESSION['user_name'] ?? null;
        $_SESSION['admin_role']  = $_SESSION['user_role'];
        return true;
    }

    return false;
}

function requireAdminLogin()
{
    if (!isAdminLoggedIn()) {
        header('Location: /public/admin/login.php');
        exit;
    }

    if (!isset($_SESSION['admin_role']) && isset($_SESSION['admin_id'])) {
        global $pdo;
        if ($pdo instanceof PDO) {
            $stmt = $pdo->prepare('SELECT role FROM users WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $_SESSION['admin_id']]);
            $role = $stmt->fetchColumn();
            if ($role) {
                $_SESSION['admin_role'] = $role;
            }
        }
    }
}
?>
