<?php

use Camagru\Tests\Support\PageTestSupport;

return [
    'redirects guests away from the share page' => static function (): void {
        $response = PageTestSupport::request('share.php?post=1');

        if ($response['status'] !== 302 || $response['location'] !== 'login.php') {
            throw new RuntimeException('Expected a guest to be redirected to login.php.');
        }
    },

    'shows the share form for the photo owner' => static function (): void {
        $owner = PageTestSupport::createUser();
        $caption = 'share_page_' . bin2hex(random_bytes(4));
        $postId = PageTestSupport::createPost($owner['id'], $caption, false);

        try {
            $cookie = PageTestSupport::loginAs($owner['username'], $owner['password']);
            $response = PageTestSupport::request('share.php?post=' . $postId, 'GET', [], $cookie);

            if ($response['status'] !== 200) {
                throw new RuntimeException('Expected HTTP 200, got HTTP ' . $response['status'] . '.');
            }

            PageTestSupport::assertContains('Share Your Photo', $response['body']);
            PageTestSupport::assertContains('name="caption"', $response['body']);
            PageTestSupport::assertContains('name="is_published"', $response['body']);
            PageTestSupport::assertContains('name="post_id" value="' . $postId . '"', $response['body']);
        } finally {
            PageTestSupport::deleteUser($owner['id']);
        }
    },

    'does not show another users photo in the share page' => static function (): void {
        $owner = PageTestSupport::createUser();
        $otherUser = PageTestSupport::createUser();
        $postId = PageTestSupport::createPost($owner['id'], 'not_your_share_photo', false);

        try {
            $cookie = PageTestSupport::loginAs($otherUser['username'], $otherUser['password']);
            $response = PageTestSupport::request('share.php?post=' . $postId, 'GET', [], $cookie);

            if ($response['status'] !== 302 || $response['location'] !== 'create.php') {
                throw new RuntimeException('Expected a non-owner to be redirected to create.php.');
            }
        } finally {
            PageTestSupport::deleteUser($owner['id']);
            PageTestSupport::deleteUser($otherUser['id']);
        }
    },
];
