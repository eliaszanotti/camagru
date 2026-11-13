<?php

use Camagru\Tests\Support\PageTestSupport;

return [
    'redirects guests from the profile page to login' => static function (): void {
        $response = PageTestSupport::request('profile.php');

        if ($response['status'] !== 302 || $response['location'] !== 'login.php') {
            throw new RuntimeException('Expected a guest to be redirected to login.php.');
        }
    },

    'shows account settings and enabled comment notifications' => static function (): void {
        $user = PageTestSupport::createUser();

        try {
            $cookie = PageTestSupport::loginAs($user['username'], $user['password']);
            $response = PageTestSupport::request('profile.php', 'GET', [], $cookie);

            if ($response['status'] !== 200) {
                throw new RuntimeException('Expected HTTP 200, got HTTP ' . $response['status'] . '.');
            }

            PageTestSupport::assertContains('My Profile', $response['body']);
            PageTestSupport::assertContains('Change Username', $response['body']);
            PageTestSupport::assertContains('Change Email', $response['body']);
            PageTestSupport::assertContains('Change Password', $response['body']);
            PageTestSupport::assertContains('name="email_notifications"', $response['body']);
            if (!preg_match('/name="email_notifications"[^>]*checked/', $response['body'])) {
                throw new RuntimeException('Expected comment email notifications to be enabled by default.');
            }
        } finally {
            PageTestSupport::deleteUser($user['id']);
        }
    },

    'updates the signed-in user username' => static function (): void {
        $user = PageTestSupport::createUser();
        $updatedUsername = 'page_test_renamed_' . bin2hex(random_bytes(4));

        try {
            $cookie = PageTestSupport::loginAs($user['username'], $user['password']);
            $response = PageTestSupport::postWithCsrf(
                'profile.php',
                ['form_type' => 'profile_username', 'username' => $updatedUsername],
                $cookie
            );

            PageTestSupport::assertContains('Profile updated successfully!', $response['body']);
            $storedUsername = PageTestSupport::databaseSql(
                'SELECT username FROM users WHERE id = ' . $user['id'] . ';'
            );
            if (strtok($storedUsername, "\n") !== $updatedUsername) {
                throw new RuntimeException('Expected the updated username to be saved.');
            }
        } finally {
            PageTestSupport::deleteUser($user['id']);
        }
    },

    'rejects an invalid email address without changing the profile' => static function (): void {
        $user = PageTestSupport::createUser();

        try {
            $cookie = PageTestSupport::loginAs($user['username'], $user['password']);
            $response = PageTestSupport::postWithCsrf(
                'profile.php',
                ['form_type' => 'profile_email', 'email' => 'not-an-email'],
                $cookie
            );

            PageTestSupport::assertContains('Invalid email format', $response['body']);
        } finally {
            PageTestSupport::deleteUser($user['id']);
        }
    },

    'rejects a username that belongs to another account' => static function (): void {
        $user = PageTestSupport::createUser();
        $otherUser = PageTestSupport::createUser();

        try {
            $cookie = PageTestSupport::loginAs($user['username'], $user['password']);
            $response = PageTestSupport::postWithCsrf(
                'profile.php',
                ['form_type' => 'profile_username', 'username' => $otherUser['username']],
                $cookie
            );

            PageTestSupport::assertContains('Username is already taken', $response['body']);
        } finally {
            PageTestSupport::deleteUser($user['id']);
            PageTestSupport::deleteUser($otherUser['id']);
        }
    },

    'rejects an email address that belongs to another account without sending email' => static function (): void {
        $user = PageTestSupport::createUser();
        $otherUser = PageTestSupport::createUser();

        try {
            $cookie = PageTestSupport::loginAs($user['username'], $user['password']);
            $response = PageTestSupport::postWithCsrf(
                'profile.php',
                ['form_type' => 'profile_email', 'email' => $otherUser['email']],
                $cookie
            );

            PageTestSupport::assertContains('Email is already registered', $response['body']);
        } finally {
            PageTestSupport::deleteUser($user['id']);
            PageTestSupport::deleteUser($otherUser['id']);
        }
    },

    'changes the password after checking the current password' => static function (): void {
        $user = PageTestSupport::createUser();
        $newPassword = 'ChangedPagePass2!';

        try {
            $cookie = PageTestSupport::loginAs($user['username'], $user['password']);
            $wrongCurrentResponse = PageTestSupport::postWithCsrf(
                'profile.php',
                [
                    'form_type' => 'password_change',
                    'current_password' => 'WrongCurrentPass1!',
                    'new_password' => $newPassword,
                    'confirm_password' => $newPassword,
                ],
                $cookie
            );
            PageTestSupport::assertContains('Current password is incorrect', $wrongCurrentResponse['body']);

            $response = PageTestSupport::postWithCsrf(
                'profile.php',
                [
                    'form_type' => 'password_change',
                    'current_password' => $user['password'],
                    'new_password' => $newPassword,
                    'confirm_password' => $newPassword,
                ],
                $cookie
            );
            PageTestSupport::assertContains('Profile updated successfully!', $response['body']);
            PageTestSupport::loginAs($user['username'], $newPassword);
        } finally {
            PageTestSupport::deleteUser($user['id']);
        }
    },

    'rejects a new password when its confirmation does not match' => static function (): void {
        $user = PageTestSupport::createUser();

        try {
            $cookie = PageTestSupport::loginAs($user['username'], $user['password']);
            $response = PageTestSupport::postWithCsrf(
                'profile.php',
                [
                    'form_type' => 'password_change',
                    'current_password' => $user['password'],
                    'new_password' => 'NewProfilePass2!',
                    'confirm_password' => 'DifferentProfilePass3!',
                ],
                $cookie
            );

            PageTestSupport::assertContains('Passwords do not match', $response['body']);
        } finally {
            PageTestSupport::deleteUser($user['id']);
        }
    },

    'allows the user to turn off comment email notifications' => static function (): void {
        $user = PageTestSupport::createUser(true, true);

        try {
            $cookie = PageTestSupport::loginAs($user['username'], $user['password']);
            $response = PageTestSupport::postWithCsrf(
                'profile.php',
                ['form_type' => 'notifications'],
                $cookie
            );

            PageTestSupport::assertContains('Profile updated successfully!', $response['body']);
            $storedPreference = PageTestSupport::databaseSql(
                'SELECT email_notifications FROM users WHERE id = ' . $user['id'] . ';'
            );
            if (strtok($storedPreference, "\n") !== 'f') {
                throw new RuntimeException('Expected comment email notifications to be disabled.');
            }
        } finally {
            PageTestSupport::deleteUser($user['id']);
        }
    },

    'rejects profile updates without a CSRF token' => static function (): void {
        $user = PageTestSupport::createUser();

        try {
            $cookie = PageTestSupport::loginAs($user['username'], $user['password']);
            $response = PageTestSupport::request(
                'profile.php',
                'POST',
                ['form_type' => 'profile_username', 'username' => 'forged_name'],
                $cookie
            );

            if ($response['status'] !== 403) {
                throw new RuntimeException('Expected HTTP 403, got HTTP ' . $response['status'] . '.');
            }
        } finally {
            PageTestSupport::deleteUser($user['id']);
        }
    },

    'rejects profile updates with an invalid CSRF token' => static function (): void {
        $user = PageTestSupport::createUser();

        try {
            $cookie = PageTestSupport::loginAs($user['username'], $user['password']);
            $response = PageTestSupport::request(
                'profile.php',
                'POST',
                [
                    'form_type' => 'profile_username',
                    'username' => 'forged_name',
                    'csrf_token' => 'invalid-token',
                ],
                $cookie
            );

            if ($response['status'] !== 403) {
                throw new RuntimeException('Expected HTTP 403, got HTTP ' . $response['status'] . '.');
            }
        } finally {
            PageTestSupport::deleteUser($user['id']);
        }
    },
];
