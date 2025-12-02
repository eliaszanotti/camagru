<?php

require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class PostHandler
{
    private Post $postModel;

    public function __construct()
    {
        $this->postModel = new Post();

        // Only handle POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleCreatePost();
        }
    }

    private function handleCreatePost(): void
    {
        // Check authentication
        if (!AuthMiddleware::isLoggedIn()) {
            header('Location: /login.php');
            exit;
        }

        $userId = AuthMiddleware::getUserId();
        $imageData = $_POST['image_data'] ?? '';
        $caption = $_POST['caption'] ?? '';
        $isPublished = isset($_POST['is_published']) ? (int)$_POST['is_published'] : 0;

        // Validate required fields
        if (!$imageData) {
            header('Location: /create.php?error=missing_image');
            exit;
        }

        // Convert data URL to image file
        $imagePath = $this->saveImageData($imageData);
        if (!$imagePath) {
            header('Location: /create.php?error=save_failed');
            exit;
        }

        // Create post
        $postData = [
            'user_id' => $userId,
            'image_path' => $imagePath,
            'caption' => $caption,
            'is_published' => $isPublished
        ];

        if ($this->postModel->create($postData)) {
            header('Location: /create.php?success=1');
        } else {
            header('Location: /create.php?error=create_failed');
        }
        exit;
    }

    private function saveImageData(string $dataUrl): ?string
    {
        // Extract image data from data URL
        if (preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $matches)) {
            $imageType = $matches[1];
            $base64Data = substr($dataUrl, strpos($dataUrl, ',') + 1);

            // Decode base64
            $imageData = base64_decode($base64Data);
            if ($imageData === false) {
                return null;
            }

            // Create uploads directory if it doesn't exist
            $uploadsDir = __DIR__ . '/../uploads/posts/';
            if (!file_exists($uploadsDir)) {
                @mkdir($uploadsDir, 0777, true);
            }

            // Generate unique filename
            $filename = 'post_' . uniqid() . '.' . $imageType;
            $filepath = $uploadsDir . $filename;

            // Save image
            if (@file_put_contents($filepath, $imageData)) {
                return 'uploads/posts/' . $filename;
            }
        }

        return null;
    }
}

// Handle requests
new PostHandler();