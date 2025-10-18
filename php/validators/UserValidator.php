<?php

class UserValidator {

    /**
     * Validate username
     */
    public static function validateUsername(string $username, ?int $excludeUserId = null): array {
        $errors = [];

        if (empty(trim($username))) {
            $errors['username'] = 'Username is required';
        } elseif (strlen($username) < 3) {
            $errors['username'] = 'Username must be at least 3 characters long';
        } elseif (strlen($username) > 50) {
            $errors['username'] = 'Username must be less than 50 characters';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors['username'] = 'Username can only contain letters, numbers, and underscores';
        }

        return $errors;
    }

    /**
     * Validate email
     */
    public static function validateEmail(string $email, ?int $excludeUserId = null): array {
        $errors = [];

        if (empty(trim($email))) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }

        return $errors;
    }

    /**
     * Validate password
     */
    public static function validatePassword(string $password, string $confirmPassword): array {
        $errors = [];

        if (empty($password)) {
            $errors['password'] = 'Password is required';
        } elseif (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters long';
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $errors['password'] = 'Password must contain at least one uppercase letter';
        } elseif (!preg_match('/[a-z]/', $password)) {
            $errors['password'] = 'Password must contain at least one lowercase letter';
        } elseif (!preg_match('/[0-9]/', $password)) {
            $errors['password'] = 'Password must contain at least one number';
        } elseif (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors['password'] = 'Password must contain at least one special character';
        }

        if ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match';
        }

        return $errors;
    }

    /**
     * Validate profile update data
     */
    public static function validateProfileUpdate(array $data, int $userId, User $userModel): array {
        $errors = [];

        // Validate username if provided
        if (isset($data['username'])) {
            $usernameErrors = self::validateUsername($data['username'], $userId);
            if (!empty($usernameErrors)) {
                $errors = array_merge($errors, $usernameErrors);
            } else {
                // Check if username is taken by another user
                $existingUser = $userModel->findByUsername($data['username']);
                if ($existingUser && $existingUser['id'] != $userId) {
                    $errors['username'] = 'Username is already taken';
                }
            }
        }

        // Validate email if provided
        if (isset($data['email'])) {
            $emailErrors = self::validateEmail($data['email'], $userId);
            if (!empty($emailErrors)) {
                $errors = array_merge($errors, $emailErrors);
            } else {
                // Check if email is taken by another user
                $existingUser = $userModel->findByEmail($data['email']);
                if ($existingUser && $existingUser['id'] != $userId) {
                    $errors['email'] = 'Email is already registered';
                }
            }
        }

        // Validate password if provided
        if (isset($data['password']) && !empty($data['password'])) {
            $passwordErrors = self::validatePassword($data['password'], $data['confirm_password'] ?? '');
            if (!empty($passwordErrors)) {
                $errors = array_merge($errors, $passwordErrors);
            }
        }

        // Validate current password for password change
        if (isset($data['current_password']) && !empty($data['current_password'])) {
            if (!isset($data['new_password']) || empty($data['new_password'])) {
                $errors['new_password'] = 'New password is required when changing password';
            }
        }

        return $errors;
    }

    /**
     * Validate registration data
     */
    public static function validateRegistrationData(array $data): array {
        $errors = [];

        // Validate username
        $usernameErrors = self::validateUsername($data['username'] ?? '');
        $errors = array_merge($errors, $usernameErrors);

        // Validate email
        $emailErrors = self::validateEmail($data['email'] ?? '');
        $errors = array_merge($errors, $emailErrors);

        // Validate password
        $passwordErrors = self::validatePassword($data['password'] ?? '', $data['confirm_password'] ?? '');
        $errors = array_merge($errors, $passwordErrors);

        return $errors;
    }

    /**
     * Validate login data
     */
    public static function validateLoginData(array $data): array {
        $errors = [];

        if (empty($data['login_identifier'])) {
            $errors['login_identifier'] = 'Email or username is required';
        }

        if (empty($data['password'])) {
            $errors['password'] = 'Password is required';
        }

        return $errors;
    }

    /**
     * Validate password reset request
     */
    public static function validatePasswordResetRequest(array $data): array {
        $errors = [];

        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }

        return $errors;
    }

    /**
     * Validate password reset
     */
    public static function validatePasswordReset(array $data): array {
        $errors = [];

        if (empty($data['token'])) {
            $errors['general'] = 'Invalid reset token';
        }

        $passwordErrors = self::validatePassword($data['password'] ?? '', $data['confirm_password'] ?? '');
        $errors = array_merge($errors, $passwordErrors);

        return $errors;
    }
}