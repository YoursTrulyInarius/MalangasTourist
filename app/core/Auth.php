<?php
// app/core/Auth.php

class Auth {
    public static function requireLogin($loginRoute = '/login') {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . BASE_URL . $loginRoute);
            exit();
        }
    }

    public static function requireRole($role) {
        self::requireLogin();
        if (($_SESSION['user_role'] ?? null) !== $role) {
            http_response_code(403);
            die("403 Forbidden. You do not have the required role.");
        }
    }

    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public static function user() {
        if (self::isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'] ?? '',
                'role' => $_SESSION['user_role'] ?? '',
                'applicant_type' => $_SESSION['user_applicant_type'] ?? null
            ];
        }
        return null;
    }
    
    public static function login($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_applicant_type'] = $user['applicant_type'] ?? null;
    }

    public static function logout() {
        session_unset();
        session_destroy();
    }
}
