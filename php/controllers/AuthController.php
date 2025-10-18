<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../services/EmailService.php';
require_once __DIR__ . '/../validators/UserValidator.php';

class AuthController {
    private User $userModel;
    private EmailService $emailService;

    public function __construct() {
        $this->userModel = new User();
        $this->emailService = new EmailService();
    }

    /**
     * Register a new user
     */
    public function register(array $data): array {
        $errors = [];

        // Validate input
        $errors = array_merge($errors, UserValidator::validateRegistrationData($data));

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Check if user already exists
        if ($this->userModel->emailExists($data['email'])) {
            $errors['email'] = 'Email is already registered';
        }

        if ($this->userModel->usernameExists($data['username'])) {
            $errors['username'] = 'Username is already taken';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Hash password
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

        // Generate verification token
        $verificationToken = bin2hex(random_bytes(32));

        // Create user
        $userData = [
            'username' => trim($data['username']),
            'email' => trim($data['email']),
            'password_hash' => $passwordHash,
            'verification_token' => $verificationToken
        ];

        if (!$this->userModel->create($userData)) {
            return ['success' => false, 'errors' => ['general' => 'Registration failed. Please try again.']];
        }

        // Send verification email
        if (!$this->emailService->sendVerificationEmail($data['email'], $data['username'], $verificationToken)) {
            error_log("Failed to send verification email to: " . $data['email']);
            // Don't fail registration if email fails, but log it
        }

        return ['success' => true, 'message' => 'Registration successful! Please check your email to verify your account.'];
    }

    /**
     * Login user
     */
    public function login(array $data): array {
        // Validate input
        $errors = UserValidator::validateLoginData($data);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Find user by email or username
        $user = null;
        if (filter_var($data['login_identifier'], FILTER_VALIDATE_EMAIL)) {
            $user = $this->userModel->findByEmail($data['login_identifier']);
        } else {
            $user = $this->userModel->findByUsername($data['login_identifier']);
        }

        // Check if user exists and password is correct
        if (!$user || !password_verify($data['password'], $user['password_hash'])) {
            return ['success' => false, 'errors' => ['general' => 'Invalid username/email or password']];
        }

  
        // Create secure session
        $this->createSecureSession($user);

        return ['success' => true, 'message' => 'Login successful!'];
    }

    /**
     * Logout user
     */
    public function logout(): void {
        // Destroy session
        $_SESSION = [];

        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destroy session
        session_destroy();
    }

    /**
     * Request password reset
     */
    public function requestPasswordReset(array $data): array {
        // Validate input
        $errors = UserValidator::validatePasswordResetRequest($data);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $user = $this->userModel->findByEmail($data['email']);

        // Always return success to prevent email enumeration
        if (!$user) {
            return ['success' => true, 'message' => 'If the email exists, a reset link has been sent.'];
        }

        // Generate reset token
        $resetToken = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Save reset token
        if (!$this->userModel->createPasswordResetToken($data['email'], $resetToken, $expires)) {
            return ['success' => false, 'errors' => ['general' => 'Failed to create reset token. Please try again.']];
        }

        // Send reset email
        if (!$this->emailService->sendPasswordResetEmail($data['email'], $user['username'], $resetToken)) {
            error_log("Failed to send password reset email to: " . $data['email']);
            return ['success' => false, 'errors' => ['general' => 'Failed to send reset email. Please try again.']];
        }

        return ['success' => true, 'message' => 'If the email exists, a reset link has been sent.'];
    }

    /**
     * Reset password
     */
    public function resetPassword(array $data): array {
        // Validate input
        $errors = UserValidator::validatePasswordReset($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Find user by reset token
        $user = $this->userModel->findByPasswordResetToken($data['token']);
        if (!$user) {
            return ['success' => false, 'errors' => ['general' => 'Invalid or expired reset token']];
        }

        // Hash new password
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

        // Update password
        if (!$this->userModel->updatePassword($user['id'], $passwordHash)) {
            return ['success' => false, 'errors' => ['general' => 'Failed to update password. Please try again.']];
        }

        return ['success' => true, 'message' => 'Password reset successful! You can now login with your new password.'];
    }

    /**
     * Verify email
     */
    public function verifyEmail(string $token): array {
        if (empty($token)) {
            return ['success' => false, 'message' => 'Invalid verification token'];
        }

        if (!$this->userModel->verifyEmail($token)) {
            return ['success' => false, 'message' => 'Invalid or expired verification token'];
        }

        return ['success' => true, 'message' => 'Email verified successfully! You can now login.'];
    }

    /**
     * Update user profile
     */
    public function updateProfile(int $userId, array $data): array {
        // Validate input
        $errors = UserValidator::validateProfileUpdate($data, $userId, $this->userModel);

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Hash password if valid
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        // Update profile
        if (!$this->userModel->updateProfile($userId, $data)) {
            return ['success' => false, 'errors' => ['general' => 'Failed to update profile. Please try again.']];
        }

        return ['success' => true, 'message' => 'Profile updated successfully!'];
    }

    
    /**
     * Create secure session
     */
    private function createSecureSession(array $user): void {
        // Set session variables first, before any output
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['is_verified'] = $user['is_verified'];
        $_SESSION['logged_in'] = true;
        $_SESSION['last_activity'] = time();

        // Try to regenerate session ID only if headers haven't been sent
        if (!headers_sent()) {
            session_regenerate_id(true);
        }
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn(): bool {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Get current user
     */
    public static function getCurrentUser(): ?array {
        if (!self::isLoggedIn()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email'],
            'is_verified' => $_SESSION['is_verified']
        ];
    }

    /**
     * Require authentication
     */
    public static function requireAuth(): void {
        if (!self::isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }

    /**
     * Check session timeout
     */
    public static function checkSessionTimeout(): void {
        $timeout = 3600; // 1 hour

        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            // Destroy session manually instead of calling logout() statically
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();

            header('Location: login.php?timeout=1');
            exit;
        }

        $_SESSION['last_activity'] = time();
    }
}