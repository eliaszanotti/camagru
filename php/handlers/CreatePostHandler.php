<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../services/FormService.php';

class CreatePostHandler {
    private Post $postModel;
    private FormService $formService;
    private array $userPosts;

    public function __construct() {
        AuthMiddleware::requireAuth();

        $this->postModel = new Post();
        $this->formService = FormService::create();
        $this->userPosts = $this->postModel->getByUserId($_SESSION['user_id']);

        $this->handleRequest();
    }

    public function getFormService(): FormService {
        return $this->formService;
    }

    public function getUserPosts(): array {
        return $this->userPosts;
    }

    public function shouldClearForm(): bool {
        return $this->formService->hasSuccess();
    }

    private function handleRequest(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $caption = trim($_POST['caption'] ?? '');

        // Validate image upload
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $this->formService->addError('image', 'Please select an image to upload');
            return;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = $_FILES['image']['type'];

        if (!in_array($fileType, $allowedTypes)) {
            $this->formService->addError('image', 'Only JPEG, PNG and GIF images are allowed');
            return;
        }

        if ($_FILES['image']['size'] > 5 * 1024 * 1024) { // 5MB limit
            $this->formService->addError('image', 'Image size must be less than 5MB');
            return;
        }

        // Process image upload
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . '_' . time() . '.jpg';
        $uploadPath = $uploadDir . $fileName;

        $sourcePath = $_FILES['image']['tmp_name'];

        // Create image resource based on file type
        switch ($fileType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $sourceImage = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $sourceImage = imagecreatefromgif($sourcePath);
                break;
            default:
                $sourceImage = false;
        }

        if (!$sourceImage) {
            $this->formService->addError('image', 'Failed to process image');
            return;
        }

        // Resize image to max 800x800
        $maxSize = 800;
        $width = imagesx($sourceImage);
        $height = imagesy($sourceImage);

        if ($width > $maxSize || $height > $maxSize) {
            $ratio = min($maxSize / $width, $maxSize / $height);
            $newWidth = $width * $ratio;
            $newHeight = $height * $ratio;

            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($sourceImage);
            $sourceImage = $resizedImage;
        }

        // Save as JPEG
        if (imagejpeg($sourceImage, $uploadPath, 90)) {
            $postData = [
                'user_id' => $_SESSION['user_id'],
                'image_path' => $uploadPath,
                'caption' => $caption,
                'is_published' => true
            ];

            if ($this->postModel->create($postData)) {
                $this->formService->setSuccess('Photo created successfully!');
                $this->userPosts = $this->postModel->getByUserId($_SESSION['user_id']);
            } else {
                $this->formService->addError('general', 'Failed to save photo to database');
                if (file_exists($uploadPath)) {
                    unlink($uploadPath);
                }
            }
        } else {
            $this->formService->addError('image', 'Failed to save image file');
        }

        imagedestroy($sourceImage);
    }
}