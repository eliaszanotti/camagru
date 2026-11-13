<?php

use Camagru\Tests\Support\PageTestSupport;

return [
    'redirects guests away from the personal gallery' => static function (): void {
        $response = PageTestSupport::request('gallery.php');

        if ($response['status'] !== 302 || $response['location'] !== 'login.php') {
            throw new RuntimeException('Expected a guest to be redirected to login.php.');
        }
    },

    'shows only the signed-in users photos in the personal gallery' => static function (): void {
        $owner = PageTestSupport::createUser();
        $otherUser = PageTestSupport::createUser();
        $ownCaption = 'own_gallery_' . bin2hex(random_bytes(4));
        $otherCaption = 'other_gallery_' . bin2hex(random_bytes(4));
        PageTestSupport::createPost($owner['id'], $ownCaption, false);
        PageTestSupport::createPost($otherUser['id'], $otherCaption, true);

        try {
            $cookie = PageTestSupport::loginAs($owner['username'], $owner['password']);
            $response = PageTestSupport::request('gallery.php', 'GET', [], $cookie);

            if ($response['status'] !== 200) {
                throw new RuntimeException('Expected HTTP 200, got HTTP ' . $response['status'] . '.');
            }

            PageTestSupport::assertContains('My Gallery', $response['body']);
            PageTestSupport::assertContains($ownCaption, $response['body']);
            PageTestSupport::assertNotContains($otherCaption, $response['body']);
            PageTestSupport::assertContains('Draft', $response['body']);
        } finally {
            PageTestSupport::deleteUser($owner['id']);
            PageTestSupport::deleteUser($otherUser['id']);
        }
    },
];
