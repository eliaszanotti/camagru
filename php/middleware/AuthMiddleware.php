<?php

require_once __DIR__ . '/../controllers/AuthController.php';

class AuthMiddleware {
    private const CSRF_TOKEN_LENGTH = 32;
    private const SESSION_TIMEOUT = 3600; // 1 hour

    /**
     * Initialize secure session
     */
    public static function initSession(): void {
        // Set secure session parameters only if session hasn't started yet
        if (session_status() === PHP_SESSION_NONE) {
            // Set secure session parameters
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_samesite', 'Strict');

            // Start session
            session_start();
        }

        // Check session timeout
        self::checkSessionTimeout();

        // Initialize CSRF token
        self::initCSRFToken();
    }

    /**
     * Generate CSRF token
     */
    private static function initCSRFToken(): void {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(self::CSRF_TOKEN_LENGTH));
        }
    }

    /**
     * Get current CSRF token
     */
    public static function getCSRFToken(): string {
        return $_SESSION['csrf_token'] ?? '';
    }

    /**
     * Validate CSRF token
     */
    public static function validateCSRFToken(string $token): bool {
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    /**
     * Require CSRF token in POST requests
     */
    public static function requireCSRF(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

            if (!self::validateCSRFToken($token)) {
                http_response_code(403);
                die('CSRF token validation failed. Please refresh the page and try again.');
            }
        }
    }

    /**
     * Check session timeout
     */
    private static function checkSessionTimeout(): void {
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > self::SESSION_TIMEOUT)) {
            // Destroy session and redirect
            $_SESSION = [];

            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }

            session_destroy();

            // Redirect to login with timeout message
            if (!basename($_SERVER['PHP_SELF']) === 'login.php') {
                if (!headers_sent()) {
                    header('Location: login.php?timeout=1');
                } else {
                    echo '<script>window.location.href = "login.php?timeout=1";</script>';
                }
                exit;
            }
        }

        // Update last activity
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            $_SESSION['last_activity'] = time();
        }
    }

    /**
     * Require authentication
     */
    public static function requireAuth(): void {
        if (!AuthController::isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            if (!headers_sent()) {
                header('Location: login.php');
            } else {
                echo '<script>window.location.href = "login.php";</script>';
            }
            exit;
        }
    }

    /**
     * Require guest (not logged in)
     */
    public static function requireGuest(): void {
        if (AuthController::isLoggedIn()) {
            if (!headers_sent()) {
                header('Location: index.php');
            } else {
                echo '<script>window.location.href = "index.php";</script>';
            }
            exit;
        }
    }

    /**
     * Require email verification
     */
    public static function requireVerified(): void {
        self::requireAuth();

        if (!AuthController::isLoggedIn() || !($_SESSION['is_verified'] ?? false)) {
            if (!headers_sent()) {
                header('Location: verify-email.php');
            } else {
                echo '<script>window.location.href = "verify-email.php";</script>';
            }
            exit;
        }
    }

    /**
     * Set security headers
     */
    public static function setSecurityHeaders(): void {
        // Only set headers if no output has been sent yet
        if (!headers_sent()) {
            // Prevent clickjacking
            header('X-Frame-Options: DENY');

            // Prevent MIME type sniffing
            header('X-Content-Type-Options: nosniff');

            // Enable XSS protection (for older browsers)
            header('X-XSS-Protection: 1; mode=block');

            // Content Security Policy
            header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self'; connect-src 'self';");

            // Strict Transport Security (HTTPS only)
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
            }

            // Referrer Policy
            header('Referrer-Policy: strict-origin-when-cross-origin');
        }
    }

    /**
     * Sanitize input
     */
    public static function sanitizeInput(string $input): string {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate email
     */
    public static function validateEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate password strength
     */
    public static function validatePasswordStrength(string $password): array {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }

        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }

        return $errors;
    }

    /**
     * Generate random token
     */
    public static function generateToken(int $length = 32): string {
        return bin2hex(random_bytes($length));
    }

    /**
     * Log security events
     */
    public static function logSecurityEvent(string $event, array $context = []): void {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'event' => $event,
            'context' => $context
        ];

        error_log('SECURITY: ' . json_encode($logEntry));
    }

    /**
     * Rate limiting for login attempts
     */
    public static function checkLoginRateLimit(): bool {
        $maxAttempts = 5;
        $windowMinutes = 15;
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        // Simple file-based rate limiting
        $rateLimitFile = sys_get_temp_dir() . '/camagru_rate_limit_' . md5($ip);

        if (file_exists($rateLimitFile)) {
            $data = unserialize(file_get_contents($rateLimitFile));

            // Reset if window has passed
            if (time() - $data['first_attempt'] > $windowMinutes * 60) {
                $data = ['attempts' => 0, 'first_attempt' => time()];
            }

            if ($data['attempts'] >= $maxAttempts) {
                self::logSecurityEvent('login_rate_limit_exceeded', ['ip' => $ip]);
                return false;
            }

            $data['attempts']++;
        } else {
            $data = ['attempts' => 1, 'first_attempt' => time()];
        }

        file_put_contents($rateLimitFile, serialize($data));
        return true;
    }

    /**
     * Clear login rate limit
     */
    public static function clearLoginRateLimit(): void {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $rateLimitFile = sys_get_temp_dir() . '/camagru_rate_limit_' . md5($ip);

        if (file_exists($rateLimitFile)) {
            unlink($rateLimitFile);
        }
    }
}

// Initialize middleware for all requests
AuthMiddleware::initSession();
AuthMiddleware::setSecurityHeaders();