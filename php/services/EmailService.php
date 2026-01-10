<?php

require_once __DIR__ . '/../config/database.php';

class EmailService {
    private string $fromEmail;
    private string $fromName;

    public function __construct() {
        $this->fromEmail = $_ENV['SMTP_FROM_EMAIL'] ?? 'noreply@camagru.com';
        $this->fromName = $_ENV['SMTP_FROM_NAME'] ?? 'Camagru';

        if ($this->fromEmail === 'noreply@camagru.com') {
            $this->loadEnvFromFile();
        }
    }

    private function loadEnvFromFile(): void {
        $envFile = __DIR__ . '/../.env';
        if (!file_exists($envFile)) {
            return;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && !str_starts_with($line, '#')) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                switch ($key) {
                    case 'SMTP_FROM_EMAIL':
                        $this->fromEmail = $value;
                        break;
                    case 'SMTP_FROM_NAME':
                        $this->fromName = $value;
                        break;
                }
            }
        }
    }

    public function sendVerificationEmail(string $email, string $username, string $token): bool {
        $subject = "Verify your Camagru account";
        $baseUrl = $this->getBaseUrl();
        $verificationUrl = "{$baseUrl}/php/verify.php?token=" . urlencode($token);
        $message = $this->getVerificationTemplate($username, $verificationUrl);
        return $this->sendEmail($email, $subject, $message);
    }

    public function sendPasswordResetEmail(string $email, string $username, string $token): bool {
        $subject = "Reset your Camagru password";
        $baseUrl = $this->getBaseUrl();
        $resetUrl = "{$baseUrl}/php/reset-password.php?token=" . urlencode($token);
        $message = $this->getPasswordResetTemplate($username, $resetUrl);
        return $this->sendEmail($email, $subject, $message);
    }

    public function sendCommentNotification(string $authorEmail, string $authorUsername, string $commenterUsername, string $postUrl): bool {
        $userModel = new User();
        $author = $userModel->findByEmail($authorEmail);

        if (!$author || !$author['email_notifications']) {
            return true;
        }

        $subject = "New comment on your Camagru post";
        $message = $this->getCommentNotificationTemplate($authorUsername, $commenterUsername, $postUrl);
        return $this->sendEmail($authorEmail, $subject, $message);
    }

    public function sendTestEmail(string $to, string $subject, string $message): bool {
        return $this->sendEmail($to, $subject, $message);
    }

    public function getFromEmail(): string {
        return $this->fromEmail;
    }

    private function sendEmail(string $to, string $subject, string $message): bool {
        $headers = [
            "From: {$this->fromName} <{$this->fromEmail}>",
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            "Reply-To: {$this->fromEmail}",
            'X-Mailer: PHP/' . phpversion()
        ];

        return mail($to, $subject, $message, implode("\r\n", $headers));
    }

    private function getBaseUrl(): string {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return "{$protocol}://{$host}";
    }

    private function getVerificationTemplate(string $username, string $verificationUrl): string {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Verify your Camagru account</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #3b82f6; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9fafb; }
                .button { display: inline-block; padding: 12px 24px; background: #3b82f6; color: white; text-decoration: none; border-radius: 6px; margin: 20px 0; }
                .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Welcome to Camagru!</h1>
                </div>
                <div class='content'>
                    <p>Hi {$username},</p>
                    <p>Thank you for registering on Camagru! To complete your registration and start creating amazing photos, please verify your email address by clicking the button below:</p>
                    <div style='text-align: center;'>
                        <a href='{$verificationUrl}' class='button'>Verify Email Address</a>
                    </div>
                    <p>Or copy and paste this link into your browser:</p>
                    <p style='word-break: break-all; color: #666;'>{$verificationUrl}</p>
                    <p><strong>This link will expire in 24 hours.</strong></p>
                    <p>If you didn't create this account, please ignore this email.</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Camagru. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
    }

    private function getPasswordResetTemplate(string $username, string $resetUrl): string {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Reset your Camagru password</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #ef4444; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9fafb; }
                .button { display: inline-block; padding: 12px 24px; background: #ef4444; color: white; text-decoration: none; border-radius: 6px; margin: 20px 0; }
                .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Password Reset Request</h1>
                </div>
                <div class='content'>
                    <p>Hi {$username},</p>
                    <p>We received a request to reset your password for your Camagru account. To reset your password, click the button below:</p>
                    <div style='text-align: center;'>
                        <a href='{$resetUrl}' class='button'>Reset Password</a>
                    </div>
                    <p>Or copy and paste this link into your browser:</p>
                    <p style='word-break: break-all; color: #666;'>{$resetUrl}</p>
                    <p><strong>This link will expire in 1 hour.</strong></p>
                    <p>If you didn't request this password reset, please ignore this email. Your password will remain unchanged.</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Camagru. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
    }

    private function getCommentNotificationTemplate(string $authorUsername, string $commenterUsername, string $postUrl): string {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>New comment on your Camagru post</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #10b981; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9fafb; }
                .button { display: inline-block; padding: 12px 24px; background: #10b981; color: white; text-decoration: none; border-radius: 6px; margin: 20px 0; }
                .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>New Comment!</h1>
                </div>
                <div class='content'>
                    <p>Hi {$authorUsername},</p>
                    <p><strong>{$commenterUsername}</strong> commented on your post!</p>
                    <p>Click the button below to view the comment and respond:</p>
                    <div style='text-align: center;'>
                        <a href='{$postUrl}' class='button'>View Comment</a>
                    </div>
                    <p>Keep creating amazing content on Camagru!</p>
                </div>
                <div class='footer'>
                    <p>&copy; " . date('Y') . " Camagru. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
    }
}
