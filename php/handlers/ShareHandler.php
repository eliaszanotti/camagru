<?php

require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

header('Content-Type: application/json');

class ShareHandler
{
    private Post $postModel;

    public function __construct()
    {
        $this->postModel = new Post();

        // Only handle POST requests for AJAX
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleShare();
        }
    }

    private function handleShare(): void
    {
        // Check authentication
        if (!AuthMiddleware::isLoggedIn()) {
            $this->jsonResponse(false, 'Authentication required');
            return;
        }

        $userId = AuthMiddleware::getUserId();
        $postId = (int)($_POST['post_id'] ?? 0);
        $caption = $_POST['caption'] ?? '';
        $isPublished = isset($_POST['is_published']);

        // Validate required fields
        if (!$postId) {
            $this->jsonResponse(false, 'Missing post ID');
            return;
        }

        // Check if post exists and belongs to user
        $post = $this->postModel->findById($postId);
        if (!$post || $post['user_id'] != $userId) {
            $this->jsonResponse(false, 'Post not found or access denied');
            return;
        }

        // Update post
        $updateData = [
            'caption' => $caption,
            'is_published' => $isPublished
        ];

        if ($this->postModel->update($postId, $updateData)) {
            $this->jsonResponse(true, 'Photo updated successfully!');
        } else {
            $this->jsonResponse(false, 'Failed to update photo');
        }
    }

    private function jsonResponse(bool $success, string $message): void
    {
        echo json_encode([
            'success' => $success,
            'message' => $message
        ]);
        exit;
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && basename($_SERVER['PHP_SELF']) === 'ShareHandler.php') {
    new ShareHandler();
}