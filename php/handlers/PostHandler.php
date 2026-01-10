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
        $sticker = $_POST['sticker'] ?? null;

        if (!$imageData) {
            header('Location: /create.php?error=missing_image');
            exit;
        }

        $imagePath = $this->saveImageData($imageData, $sticker);
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

    private function saveImageData(string $dataUrl, ?string $sticker = null): ?string
    {
        ini_set('memory_limit', '512M');

        if (preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $matches)) {
            $imageType = $matches[1];
            $base64Data = substr($dataUrl, strpos($dataUrl, ',') + 1);

            $imageData = base64_decode($base64Data);
            if ($imageData === false) {
                return null;
            }

            $uploadsDir = __DIR__ . '/../uploads/posts/';
            if (!file_exists($uploadsDir)) {
                @mkdir($uploadsDir, 0777, true);
            }

            $filename = 'post_' . uniqid() . '.png';
            $filepath = $uploadsDir . $filename;

            $baseImage = imagecreatefromstring($imageData);
            if ($baseImage === false) {
                return null;
            }

            $origWidth = imagesx($baseImage);
            $origHeight = imagesy($baseImage);
            $size = min($origWidth, $origHeight);

            $squareImage = imagecreatetruecolor($size, $size);

            $srcX = (int)(($origWidth - $size) / 2);
            $srcY = (int)(($origHeight - $size) / 2);

            imagecopyresampled($squareImage, $baseImage, 0, 0, $srcX, $srcY, $size, $size, $size, $size);
            imagedestroy($baseImage);

            if ($sticker) {
                $stickerPath = __DIR__ . '/../assets/stickers/' . basename($sticker);
                if (file_exists($stickerPath)) {
                    $stickerImage = imagecreatefrompng($stickerPath);
                    if ($stickerImage !== false) {
                        imagealphablending($squareImage, true);
                        imagesavealpha($squareImage, true);

                        $baseWidth = imagesx($squareImage);
                        $baseHeight = imagesy($squareImage);
                        $stickerWidth = imagesx($stickerImage);
                        $stickerHeight = imagesy($stickerImage);

                        $newStickerWidth = (int)($baseWidth * 0.33);
                        $newStickerHeight = (int)($stickerHeight * ($newStickerWidth / $stickerWidth));

                        $resizedSticker = imagescale($stickerImage, $newStickerWidth, $newStickerHeight);

                        $destX = $baseWidth - $newStickerWidth;
                        $destY = $baseHeight - $newStickerHeight;

                        imagecopy($squareImage, $resizedSticker, $destX, $destY, 0, 0, $newStickerWidth, $newStickerHeight);

                        imagedestroy($stickerImage);
                        imagedestroy($resizedSticker);
                    }
                }
            }

            if (imagepng($squareImage, $filepath)) {
                imagedestroy($squareImage);
                return 'uploads/posts/' . $filename;
            }

            imagedestroy($squareImage);
        }

        return null;
    }
}

// Handle requests
new PostHandler();