<?php

use Camagru\Tests\Support\PageTestSupport;

return [
    'shows published photos publicly and hides drafts' => static function (): void {
        $user = PageTestSupport::createUser();
        $publishedCaption = 'public_home_' . bin2hex(random_bytes(4));
        $draftCaption = 'private_draft_' . bin2hex(random_bytes(4));
        $postId = PageTestSupport::createPost($user['id'], $publishedCaption, true, 500);
        PageTestSupport::createPost($user['id'], $draftCaption, false, 501);

        try {
            $response = PageTestSupport::request('index.php');

            if ($response['status'] !== 200) {
                throw new RuntimeException('Expected HTTP 200, got HTTP ' . $response['status'] . '.');
            }

            PageTestSupport::assertContains('Recent Posts', $response['body']);
            PageTestSupport::assertContains($publishedCaption, $response['body']);
            PageTestSupport::assertNotContains($draftCaption, $response['body']);
            PageTestSupport::assertContains('post.php?id=' . $postId, $response['body']);
        } finally {
            PageTestSupport::deleteUser($user['id']);
        }
    },

    'paginates the public gallery with at least five photos per full page and newest first' => static function (): void {
        $user = PageTestSupport::createUser();
        $captions = [];

        try {
            for ($index = 0; $index < 10; $index++) {
                $captions[$index] = 'page_gallery_' . $index . '_' . bin2hex(random_bytes(3));
                PageTestSupport::createPost($user['id'], $captions[$index], true, 1000 + $index);
            }

            $firstPage = PageTestSupport::request('index.php?page=1');
            if ($firstPage['status'] !== 200) {
                throw new RuntimeException('Expected page 1 to return HTTP 200.');
            }

            $photoCount = substr_count($firstPage['body'], 'post.php?id=');
            if ($photoCount < 5) {
                throw new RuntimeException('Expected at least five photos on a full gallery page.');
            }

            PageTestSupport::assertContains($captions[9], $firstPage['body']);
            PageTestSupport::assertContains($captions[2], $firstPage['body']);
            PageTestSupport::assertNotContains($captions[1], $firstPage['body']);
            if (strpos($firstPage['body'], $captions[9]) > strpos($firstPage['body'], $captions[8])) {
                throw new RuntimeException('Expected the newest photo to appear before the older photo.');
            }

            $secondPage = PageTestSupport::request('index.php?page=2');
            PageTestSupport::assertContains($captions[1], $secondPage['body']);
            PageTestSupport::assertContains($captions[0], $secondPage['body']);
            PageTestSupport::assertNotContains($captions[9], $secondPage['body']);
        } finally {
            PageTestSupport::deleteUser($user['id']);
        }
    },
];
