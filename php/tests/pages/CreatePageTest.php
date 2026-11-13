<?php

use Camagru\Tests\Support\PageTestSupport;

return [
    'redirects guests away from the photo editor' => static function (): void {
        $response = PageTestSupport::request('create.php');

        if ($response['status'] !== 302 || $response['location'] !== 'login.php') {
            throw new RuntimeException('Expected a guest to be redirected to login.php.');
        }
    },

    'shows the camera upload and sticker editor to a signed-in user' => static function (): void {
        $user = PageTestSupport::createUser();

        try {
            $cookie = PageTestSupport::loginAs($user['username'], $user['password']);
            $response = PageTestSupport::request('create.php', 'GET', [], $cookie);

            if ($response['status'] !== 200) {
                throw new RuntimeException('Expected HTTP 200, got HTTP ' . $response['status'] . '.');
            }

            PageTestSupport::assertContains('id="fileInput"', $response['body']);
            PageTestSupport::assertContains('id="webcam"', $response['body']);
            PageTestSupport::assertContains('id="captureBtn"', $response['body']);
            PageTestSupport::assertContains('id="previewArea"', $response['body']);
            PageTestSupport::assertContains('class="sticker-btn', $response['body']);
            PageTestSupport::assertContains('id="saveBtn"', $response['body']);
            PageTestSupport::assertContains('Save to drafts', $response['body']);
        } finally {
            PageTestSupport::deleteUser($user['id']);
        }
    },

    'shows the signed-in user draft history' => static function (): void {
        $user = PageTestSupport::createUser();
        $draftCaption = 'create_draft_' . bin2hex(random_bytes(4));
        PageTestSupport::createPost($user['id'], $draftCaption, false);

        try {
            $cookie = PageTestSupport::loginAs($user['username'], $user['password']);
            $response = PageTestSupport::request('create.php', 'GET', [], $cookie);

            PageTestSupport::assertContains('Draft photos', $response['body']);
            PageTestSupport::assertContains('src="assets/stickers/neutral-face.png"', $response['body']);
            PageTestSupport::assertContains('Share', $response['body']);
        } finally {
            PageTestSupport::deleteUser($user['id']);
        }
    },
];
