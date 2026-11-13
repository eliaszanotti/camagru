<?php

return [
    'accepts valid registration data' => static function (): void {
        $errors = UserValidator::validateRegistrationData([
            'username' => 'camagru_user',
            'email' => 'camagru@example.com',
            'password' => 'StrongPass1!',
            'confirm_password' => 'StrongPass1!',
        ]);

        if ($errors !== []) {
            throw new RuntimeException('Expected no validation errors.');
        }
    },

    'rejects an invalid email address' => static function (): void {
        $errors = UserValidator::validateRegistrationData([
            'username' => 'camagru_user',
            'email' => 'not-an-email',
            'password' => 'StrongPass1!',
            'confirm_password' => 'StrongPass1!',
        ]);

        if (!array_key_exists('email', $errors)) {
            throw new RuntimeException('Expected an email validation error.');
        }
    },

    'rejects a username that is too short' => static function (): void {
        $errors = UserValidator::validateRegistrationData([
            'username' => 'ab',
            'email' => 'camagru@example.com',
            'password' => 'StrongPass1!',
            'confirm_password' => 'StrongPass1!',
        ]);

        if (!array_key_exists('username', $errors)) {
            throw new RuntimeException('Expected a username validation error.');
        }
    },

    'rejects a username with unsupported characters' => static function (): void {
        $errors = UserValidator::validateRegistrationData([
            'username' => 'bad name',
            'email' => 'camagru@example.com',
            'password' => 'StrongPass1!',
            'confirm_password' => 'StrongPass1!',
        ]);

        if (!array_key_exists('username', $errors)) {
            throw new RuntimeException('Expected a username validation error.');
        }
    },

    'rejects a password that lacks required complexity' => static function (): void {
        $errors = UserValidator::validateRegistrationData([
            'username' => 'camagru_user',
            'email' => 'camagru@example.com',
            'password' => 'password',
            'confirm_password' => 'password',
        ]);

        if (!array_key_exists('password', $errors)) {
            throw new RuntimeException('Expected a password validation error.');
        }
    },

    'rejects a mismatched password confirmation' => static function (): void {
        $errors = UserValidator::validateRegistrationData([
            'username' => 'camagru_user',
            'email' => 'camagru@example.com',
            'password' => 'StrongPass1!',
            'confirm_password' => 'DifferentPass2!',
        ]);

        if (!array_key_exists('confirm_password', $errors)) {
            throw new RuntimeException('Expected a password confirmation error.');
        }
    },
];
