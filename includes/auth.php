<?php
// includes/auth.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if admin is logged in
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_id']);
}

/**
 * Require admin login
 */
function require_admin_login() {
    if (!is_admin_logged_in()) {
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
        header("Location: " . $base . "/admin/login");
        exit;
    }
}

/**
 * Log in admin
 */
function login_admin($pdo, $email, $password) {
    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id'] = $user['id'];
        session_regenerate_id(true);
        return true;
    }
    return false;
}

/**
 * Log out admin
 */
function logout_admin() {
    unset($_SESSION['admin_id']);
    session_destroy();
}
