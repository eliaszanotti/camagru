<?php

require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/CapturedImage.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

header('Content-Type: application/json');

class ShareHandler
{
    private Post $postModel;
    private CapturedImage $capturedImageModel;

    public function __construct()
    {
        $this->postModel = new Post();
        $this->capturedImageModel = new CapturedImage();

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
        $imageData = $_POST['image_data'] ?? null;
        $clientImageId = $_POST['image_id'] ?? null;
        $source = $_POST['source'] ?? 'webcam';
        $caption = $_POST['caption'] ?? '';
        $isPublished = isset($_POST['is_published']);

        // Validate required fields
        if (!$imageData || !$clientImageId) {
            $this->jsonResponse(false, 'Missing required image data');
            return;
        }

        // Process base64 image
        $imagePath = $this->saveImageToServer($imageData, $userId, $source);
        if (!$imagePath) {
            $this->jsonResponse(false, 'Failed to save image');
            return;
        }

        // Save image to captured_images table
        $capturedImageId = $this->capturedImageModel->create([
            'user_id' => $userId,
            'image_path' => $imagePath,
            'source' => $source
        ]);

        if (!$capturedImageId) {
            $this->jsonResponse(false, 'Failed to save image record');
            return;
        }

        // Create post
        $postData = [
            'user_id' => $userId,
            'image_path' => $imagePath,
            'caption' => $caption,
            'is_published' => $isPublished
        ];

        if ($this->postModel->create($postData)) {
            $this->jsonResponse(true, 'Photo shared successfully!');
        } else {
            $this->jsonResponse(false, 'Failed to create post');
        }
    }

    private function saveImageToServer(string $imageData, int $userId, string $source): ?string
    {
        try {
            // Validate base64 image
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
                $imageType = $matches[1];
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $imageData = base64_decode($imageData);

                if ($imageData === false) {
                    return null;
                }
            } else {
                return null;
            }

            // Create uploads directory if it doesn't exist
            $uploadsDir = __DIR__ . '/../uploads/captured/';
            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }

            // Generate unique filename
            $filename = 'captured_' . $userId . '_' . time() . '_' . uniqid() . '.' . $imageType;
            $filepath = $uploadsDir . $filename;
            $relativePath = '/uploads/captured/' . $filename;

            // Save image to file
            if (file_put_contents($filepath, $imageData) === false) {
                return null;
            }

            return $relativePath;
        } catch (Exception $e) {
            error_log("Error saving image: " . $e->getMessage());
            return null;
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