<?php

use Camagru\Tests\Support\PageTestSupport;

return [
    'redirects when no photo id is provided' => static function (): void {
        $response = PageTestSupport::request('post.php');

        if ($response['status'] !== 302 || $response['location'] !== 'index.php') {
            throw new RuntimeException('Expected a missing photo id to redirect to index.php.');
        }
    },

    'shows published photo details and comments publicly without a comment form' => static function (): void {
        $owner = PageTestSupport::createUser(true, false);
        $caption = 'public_detail_' . bin2hex(random_bytes(4));
        $commentText = 'visible_public_comment_' . bin2hex(random_bytes(4));
        $postId = PageTestSupport::createPost($owner['id'], $caption, true);
        PageTestSupport::createComment($owner['id'], $postId, $commentText);

        try {
            $response = PageTestSupport::request('post.php?id=' . $postId);

            if ($response['status'] !== 200) {
                throw new RuntimeException('Expected HTTP 200, got HTTP ' . $response['status'] . '.');
            }

            PageTestSupport::assertContains($caption, $response['body']);
            PageTestSupport::assertContains($commentText, $response['body']);
            PageTestSupport::assertContains('Please', $response['body']);
            PageTestSupport::assertContains('login.php', $response['body']);
            PageTestSupport::assertNotContains('id="comment-form"', $response['body']);
        } finally {
            PageTestSupport::deleteUser($owner['id']);
        }
    },

    'does not expose a draft photo through its detail page' => static function (): void {
        $owner = PageTestSupport::createUser();
        $postId = PageTestSupport::createPost($owner['id'], 'private_draft_detail', false);

        try {
            $response = PageTestSupport::request('post.php?id=' . $postId);
            if ($response['status'] !== 302 || $response['location'] !== 'index.php') {
                throw new RuntimeException('Expected a draft photo to redirect to index.php.');
            }
        } finally {
            PageTestSupport::deleteUser($owner['id']);
        }
    },

    'escapes comment content on the photo detail page' => static function (): void {
        $owner = PageTestSupport::createUser(true, false);
        $postId = PageTestSupport::createPost($owner['id'], 'xss_detail_test', true);
        PageTestSupport::createComment($owner['id'], $postId, '<script>alert(1)</script>');

        try {
            $response = PageTestSupport::request('post.php?id=' . $postId);
            PageTestSupport::assertContains('&lt;script&gt;alert(1)&lt;/script&gt;', $response['body']);
            PageTestSupport::assertNotContains('<script>alert(1)</script>', $response['body']);
        } finally {
            PageTestSupport::deleteUser($owner['id']);
        }
    },

    'allows a signed-in user to comment and shows the new comment' => static function (): void {
        $owner = PageTestSupport::createUser(true, false);
        $commenter = PageTestSupport::createUser();
        $postId = PageTestSupport::createPost($owner['id'], 'comment_submission_test', true);
        $newComment = 'new_comment_' . bin2hex(random_bytes(4));

        try {
            $cookie = PageTestSupport::loginAs($commenter['username'], $commenter['password']);
            $response = PageTestSupport::postWithCsrf(
                'post.php?id=' . $postId,
                ['content' => $newComment],
                $cookie
            );

            if ($response['status'] !== 200) {
                throw new RuntimeException('Expected HTTP 200, got HTTP ' . $response['status'] . '.');
            }

            PageTestSupport::assertContains($newComment, $response['body']);
            PageTestSupport::assertContains('csrf_token', $response['body']);
        } finally {
            PageTestSupport::deleteUser($owner['id']);
            PageTestSupport::deleteUser($commenter['id']);
        }
    },

    'rejects an empty or overlong comment' => static function (): void {
        $owner = PageTestSupport::createUser(true, false);
        $commenter = PageTestSupport::createUser();
        $postId = PageTestSupport::createPost($owner['id'], 'comment_validation_test', true);

        try {
            $cookie = PageTestSupport::loginAs($commenter['username'], $commenter['password']);
            $emptyResponse = PageTestSupport::postWithCsrf(
                'post.php?id=' . $postId,
                ['content' => ''],
                $cookie
            );
            PageTestSupport::assertContains('Comment cannot be empty', $emptyResponse['body']);

            $overlongResponse = PageTestSupport::postWithCsrf(
                'post.php?id=' . $postId,
                ['content' => str_repeat('x', 501)],
                $cookie
            );
            PageTestSupport::assertContains('Comment must be less than 500 characters', $overlongResponse['body']);
        } finally {
            PageTestSupport::deleteUser($owner['id']);
            PageTestSupport::deleteUser($commenter['id']);
        }
    },

    'rejects comment submissions without a CSRF token' => static function (): void {
        $owner = PageTestSupport::createUser(true, false);
        $commenter = PageTestSupport::createUser();
        $postId = PageTestSupport::createPost($owner['id'], 'comment_csrf_test', true);

        try {
            $cookie = PageTestSupport::loginAs($commenter['username'], $commenter['password']);
            $response = PageTestSupport::request(
                'post.php?id=' . $postId,
                'POST',
                ['content' => 'forged comment'],
                $cookie
            );

            if ($response['status'] !== 403) {
                throw new RuntimeException('Expected HTTP 403, got HTTP ' . $response['status'] . '.');
            }
        } finally {
            PageTestSupport::deleteUser($owner['id']);
            PageTestSupport::deleteUser($commenter['id']);
        }
    },
];
