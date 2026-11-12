<?php
// app/controllers/AuthController.php

class AuthController {
    private $userModel;

    public function __construct() {
        require_once '../app/models/User.php';
        $this->userModel = new User();
    }

    public function loginForm() {
        if (Auth::isLoggedIn()) {
            $this->redirectBasedOnRole(Auth::user()['role']);
        }
        $data = [];
        if (isset($_SESSION['login_error'])) {
            $data['error'] = $_SESSION['login_error'];
            unset($_SESSION['login_error']);
        }
        require_once '../app/views/auth/login.php';
    }

    public function login() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['status'] !== 'active') {
                $_SESSION['login_error'] = "Your account is disabled.";
                header("Location: " . BASE_URL . "/login");
                exit();
            }

            Auth::login($user);
            $this->redirectBasedOnRole($user['role']);
        } else {
            $_SESSION['login_error'] = "Invalid email or password.";
            header("Location: " . BASE_URL . "/login");
            exit();
        }
    }

    public function registerForm() {
        if (Auth::isLoggedIn()) {
            $this->redirectBasedOnRole(Auth::user()['role']);
        }
        $data = [];
        if (isset($_SESSION['register_error'])) {
            $data['error'] = $_SESSION['register_error'];
            unset($_SESSION['register_error']);
        }
        require_once '../app/views/auth/register.php';
    }

    public function register() {
        $data = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'password' => $_POST['password'] ?? '',
            'role' => $_POST['role'] ?? 'business',
            'applicant_type' => in_array($_POST['applicant_type'] ?? '', ['private', 'public'], true) ? $_POST['applicant_type'] : 'private'
        ];

        // Basic validation
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            $_SESSION['register_error'] = "Please fill in all required fields.";
            header("Location: " . BASE_URL . "/register");
            exit();
        }

        $userId = $this->userModel->create($data);

        if ($userId) {
            // Auto login after registration
            $user = $this->userModel->findById($userId);
            Auth::login($user);
            $this->redirectBasedOnRole($user['role']);
        } else {
            $_SESSION['register_error'] = "Email is already registered.";
            header("Location: " . BASE_URL . "/register");
            exit();
        }
    }

    public function logout() {
        Auth::logout();
        header("Location: " . BASE_URL . "/");
        exit();
    }

    // Tourist Login/Register Methods
    public function touristLoginForm() {
        if (Auth::isLoggedIn()) {
            $this->redirectBasedOnRole(Auth::user()['role']);
        }
        $data = [];
        $data['next'] = $_GET['next'] ?? '';
        if (!empty($data['next'])) {
            $_SESSION['tourist_next'] = $data['next'];
        } elseif (!empty($_SESSION['tourist_next'])) {
            $data['next'] = $_SESSION['tourist_next'];
        }

        if (isset($_SESSION['login_error'])) {
            $data['error'] = $_SESSION['login_error'];
            unset($_SESSION['login_error']);
        }
        require_once '../app/views/auth/tourist-login.php';
    }

    public function touristLogin() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $next = $_POST['next'] ?? '';
        if (empty($next) && !empty($_SESSION['tourist_next'])) {
            $next = $_SESSION['tourist_next'];
        }

        $user = $this->userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['status'] !== 'active') {
                $_SESSION['tourist_login_error'] = "Your account is disabled.";
                $redirectUrl = !empty($next) && strpos($next, '/') === 0 ? BASE_URL . $next : BASE_URL . '/tourist/login';
                header("Location: " . $redirectUrl);
                exit();
            }

            Auth::login($user);
            if ($user['role'] === 'tourist' && !empty($next) && strpos($next, '/') === 0) {
                unset($_SESSION['tourist_next']);
                header("Location: " . BASE_URL . $next);
                exit();
            }
            unset($_SESSION['tourist_next']);
            $this->redirectBasedOnRole($user['role']);
        } else {
            $_SESSION['tourist_login_error'] = "Invalid email or password.";
            $redirectUrl = !empty($next) && strpos($next, '/') === 0 ? BASE_URL . $next : BASE_URL . '/tourist/login';
            header("Location: " . $redirectUrl);
            exit();
        }
    }

    public function touristRegisterForm() {
        if (Auth::isLoggedIn()) {
            $this->redirectBasedOnRole(Auth::user()['role']);
        }
        $data = [];
        $data['next'] = $_GET['next'] ?? '';
        if (isset($_SESSION['register_error'])) {
            $data['error'] = $_SESSION['register_error'];
            unset($_SESSION['register_error']);
        }
        require_once '../app/views/auth/tourist-register.php';
    }

    public function touristRegister() {
        $next = $_POST['next'] ?? '';
        $data = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => preg_replace('/[^0-9]/', '', $_POST['phone'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'role' => 'tourist' // Force tourist role
        ];

        // Basic validation
        $redirectUrl = BASE_URL . '/tourist/register';
        if (!empty($next) && strpos($next, '/') === 0) {
            $redirectUrl .= '?next=' . urlencode($next);
        }

        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            $_SESSION['register_error'] = "Please fill in all required fields.";
            header("Location: " . $redirectUrl);
            exit();
        }

        if (!empty($data['phone']) && (strlen($data['phone']) > 11 || !ctype_digit($data['phone']))) {
            $_SESSION['register_error'] = "Phone number must contain only digits and be 11 digits or fewer.";
            header("Location: " . $redirectUrl);
            exit();
        }

        $userId = $this->userModel->create($data);

        if ($userId) {
            // Auto login after registration
            $user = $this->userModel->findById($userId);
            Auth::login($user);
            if (!empty($next) && strpos($next, '/') === 0) {
                header("Location: " . BASE_URL . $next);
                exit();
            }
            $this->redirectBasedOnRole($user['role']);
        } else {
            $_SESSION['register_error'] = "Email is already registered.";
            header("Location: " . BASE_URL . "/tourist/register");
            exit();
        }
    }

    public function forgotPasswordForm() {
        if (Auth::isLoggedIn()) {
            $this->redirectBasedOnRole(Auth::user()['role']);
        }
        $data = [];
        if (isset($_SESSION['forgot_error'])) {
            $data['error'] = $_SESSION['forgot_error'];
            unset($_SESSION['forgot_error']);
        }
        require_once '../app/views/auth/forgot-password.php';
    }

    public function forgotPasswordRequest() {
        $email = trim($_POST['email'] ?? '');
        if (empty($email)) {
            $_SESSION['forgot_error'] = 'Please enter your email address.';
            header('Location: ' . BASE_URL . '/forgot-password');
            exit();
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $user = $this->userModel->createPasswordResetToken($email, $code, $expiresAt);
        if (!$user) {
            $_SESSION['forgot_error'] = 'Email not found. Please use a registered email address.';
            header('Location: ' . BASE_URL . '/forgot-password');
            exit();
        }

        $_SESSION['password_reset_email'] = $user['email'];

        // Redirect immediately — don't make the user wait for SMTP
        $_SESSION['swal_success'] = 'A 6-digit reset code has been sent to your email. Enter it below to reset your password.';

        // Flush the redirect response to the browser first
        session_write_close();
        header('Location: ' . BASE_URL . '/reset-password');

        // Send the response body and close the connection so the browser navigates away
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } else {
            ignore_user_abort(true);
            ob_start();
            header('Content-Encoding: none');
            header('Content-Length: 0');
            header('Connection: close');
            ob_end_flush();
            flush();
        }

        // Send email in the background AFTER the browser has already moved on
        require_once '../app/services/EmailService.php';
        $emailService = new EmailService();
        $resetLink = BASE_URL . '/reset-password';
        $emailService->sendPasswordReset($user['email'], $user['name'], $resetLink, $code);

        exit();
    }

    public function resendResetCode() {
        $email = $_SESSION['password_reset_email'] ?? '';
        if (empty($email)) {
            $_SESSION['forgot_error'] = 'Please request a reset code first.';
            header('Location: ' . BASE_URL . '/forgot-password');
            exit();
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $user = $this->userModel->createPasswordResetToken($email, $code, $expiresAt);
        if (!$user) {
            $_SESSION['forgot_error'] = 'We could not resend the reset code. Please request a new one.';
            header('Location: ' . BASE_URL . '/forgot-password');
            exit();
        }

        $_SESSION['password_reset_email'] = $user['email'];
        $_SESSION['swal_success'] = 'A new 6-digit reset code has been sent to your email.';

        session_write_close();
        header('Location: ' . BASE_URL . '/reset-password');

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } else {
            ignore_user_abort(true);
            ob_start();
            header('Content-Encoding: none');
            header('Content-Length: 0');
            header('Connection: close');
            ob_end_flush();
            flush();
        }

        require_once '../app/services/EmailService.php';
        $emailService = new EmailService();
        $resetLink = BASE_URL . '/reset-password';
        $emailService->sendPasswordReset($user['email'], $user['name'], $resetLink, $code);
        exit();
    }

    public function resetPasswordForm() {
        if (Auth::isLoggedIn()) {
            $this->redirectBasedOnRole(Auth::user()['role']);
        }

        $data = [];
        if (isset($_SESSION['reset_error'])) {
            $data['error'] = $_SESSION['reset_error'];
            unset($_SESSION['reset_error']);
        }
        if (isset($_SESSION['swal_success'])) {
            $data['success'] = $_SESSION['swal_success'];
            unset($_SESSION['swal_success']);
        }
        require_once '../app/views/auth/reset-password.php';
    }

    public function verifyResetCode() {
        $code = trim((string)($_POST['code'] ?? ''));

        if (empty($code)) {
            $digits = [];
            for ($i = 1; $i <= 6; $i++) {
                $digits[] = trim((string)($_POST['code_digit_' . $i] ?? ''));
            }
            $code = implode('', $digits);
        }

        $code = preg_replace('/\D+/', '', $code);

        if (!preg_match('/^\d{6}$/', $code)) {
            $_SESSION['reset_error'] = 'Please enter the 6-digit code sent to your email.';
            header('Location: ' . BASE_URL . '/reset-password');
            exit();
        }

        $user = $this->userModel->findByResetToken($code);
        if (!$user) {
            error_log('Reset code rejected for code=' . $code . ' user_email=' . ($_SESSION['password_reset_email'] ?? 'unknown'));
            $_SESSION['reset_error'] = 'Invalid or expired reset code.';
            header('Location: ' . BASE_URL . '/reset-password');
            exit();
        }

        $_SESSION['password_reset_user_id'] = $user['id'];
        $_SESSION['password_reset_email'] = $user['email'];
        $_SESSION['swal_success'] = 'Code verified. Enter your new password.';
        header('Location: ' . BASE_URL . '/reset-password/new');
        exit();
    }

    public function newPasswordForm() {
        if (Auth::isLoggedIn()) {
            $this->redirectBasedOnRole(Auth::user()['role']);
        }

        if (empty($_SESSION['password_reset_user_id'])) {
            header('Location: ' . BASE_URL . '/reset-password');
            exit();
        }

        $data = [];
        if (isset($_SESSION['reset_error'])) {
            $data['error'] = $_SESSION['reset_error'];
            unset($_SESSION['reset_error']);
        }
        if (isset($_SESSION['swal_success'])) {
            $data['success'] = $_SESSION['swal_success'];
            unset($_SESSION['swal_success']);
        }

        require_once '../app/views/auth/reset-password-new.php';
    }

    public function resetPassword() {
        if (empty($_SESSION['password_reset_user_id'])) {
            header('Location: ' . BASE_URL . '/reset-password');
            exit();
        }

        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($password) || empty($confirmPassword)) {
            $_SESSION['reset_error'] = 'Please fill in all required fields.';
            header('Location: ' . BASE_URL . '/reset-password/new');
            exit();
        }

        if ($password !== $confirmPassword) {
            $_SESSION['reset_error'] = 'Passwords do not match.';
            header('Location: ' . BASE_URL . '/reset-password/new');
            exit();
        }

        $userId = $_SESSION['password_reset_user_id'];
        $this->userModel->updatePassword($userId, $password);
        $this->userModel->deletePasswordResetToken($userId);
        unset($_SESSION['password_reset_user_id']);

        $_SESSION['swal_success'] = 'Your password has been reset. Please sign in with your new password.';
        header('Location: ' . BASE_URL . '/login');
        exit();
    }

    private function redirectBasedOnRole($role) {
        if ($role === 'lgu') {
            header("Location: " . BASE_URL . "/lgu");
        } else if ($role === 'business') {
            header("Location: " . BASE_URL . "/business");
        } else {
            header("Location: " . BASE_URL . "/");
        }
        exit();
    }
}
