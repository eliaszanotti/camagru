<?php

use Camagru\Tests\Support\PageTestSupport;

return [
    'rejects a missing or invalid reset token' => static function (): void {
        $response = PageTestSupport::request('reset-password.php?token=invalid-token');

        if ($response['status'] !== 200) {
            throw new RuntimeException('Expected HTTP 200, got HTTP ' . $response['status'] . '.');
        }

        PageTestSupport::assertContains('Invalid or expired reset token', $response['body']);
        PageTestSupport::assertNotContains('name="confirm_password"', $response['body']);
    },

    'renders reset fields for a valid token' => static function (): void {
        $token = bin2hex(random_bytes(32));
        $user = PageTestSupport::createUser(true, true, null, $token);

        try {
            $response = PageTestSupport::request('reset-password.php?token=' . urlencode($token));

            if ($response['status'] !== 200) {
                throw new RuntimeException('Expected HTTP 200, got HTTP ' . $response['status'] . '.');
            }

            PageTestSupport::assertContains('name="password"', $response['body']);
            PageTestSupport::assertContains('name="confirm_password"', $response['body']);
            PageTestSupport::assertContains('name="csrf_token"', $response['body']);
        } finally {
            PageTestSupport::deleteUser($user['id']);
        }
    },

    'rejects a weak replacement password' => static function (): void {
        $token = bin2hex(random_bytes(32));
        $user = PageTestSupport::createUser(true, true, null, $token);

        try {
            $response = PageTestSupport::postWithCsrf(
                'reset-password.php?token=' . urlencode($token),
                ['password' => 'password', 'confirm_password' => 'password']
            );

            PageTestSupport::assertContains('Password must contain at least one uppercase letter', $response['body']);
        } finally {
            PageTestSupport::deleteUser($user['id']);
        }
    },

    'resets a password with a valid token and clears the token' => static function (): void {
        $token = bin2hex(random_bytes(32));
        $user = PageTestSupport::createUser(true, true, null, $token);
        $newPassword = 'NewPagePass2!';

        try {
            $response = PageTestSupport::postWithCsrf(
                'reset-password.php?token=' . urlencode($token),
                ['password' => $newPassword, 'confirm_password' => $newPassword]
            );

            if ($response['status'] !== 302 || $response['location'] !== 'login.php') {
                throw new RuntimeException('Expected a successful password reset to redirect to login.php.');
            }

            $userRecord = PageTestSupport::databaseSql(
                'SELECT password_reset_token IS NULL FROM users WHERE id = ' . $user['id'] . ';'
            );
            if (strtok($userRecord, "\n") !== 't') {
                throw new RuntimeException('Expected the used reset token to be cleared.');
            }

            PageTestSupport::loginAs($user['username'], $newPassword);
        } finally {
            PageTestSupport::deleteUser($user['id']);
        }
    },
];
