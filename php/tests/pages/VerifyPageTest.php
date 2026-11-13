<?php

use Camagru\Tests\Support\PageTestSupport;

return [
    'shows a failure message when the verification token is missing' => static function (): void {
        $response = PageTestSupport::request('verify.php');

        if ($response['status'] !== 200) {
            throw new RuntimeException('Expected HTTP 200, got HTTP ' . $response['status'] . '.');
        }

        PageTestSupport::assertContains('Verification Failed', $response['body']);
        PageTestSupport::assertContains('login.php', $response['body']);
    },

    'verifies an account with a valid token exactly once' => static function (): void {
        $token = bin2hex(random_bytes(32));
        $user = PageTestSupport::createUser(false, true, $token);

        try {
            $response = PageTestSupport::request('verify.php?token=' . urlencode($token));
            if ($response['status'] !== 200) {
                throw new RuntimeException('Expected HTTP 200, got HTTP ' . $response['status'] . '.');
            }
            PageTestSupport::assertContains('Email Verified!', $response['body']);

            $reuseResponse = PageTestSupport::request('verify.php?token=' . urlencode($token));
            PageTestSupport::assertContains('Verification Failed', $reuseResponse['body']);
        } finally {
            PageTestSupport::deleteUser($user['id']);
        }
    },
];
