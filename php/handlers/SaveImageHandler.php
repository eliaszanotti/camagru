<?php

require_once __DIR__ . '/../models/CapturedImage.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

header('Content-Type: application/json');

try {
    // Check if user is authenticated
    if (!AuthMiddleware::isLoggedIn()) {
        echo json_encode([
            'success' => false,
            'error' => 'Authentication required'
        ]);
        exit;
    }

    $userId = AuthMiddleware::getUserId();

    // Validate input
    if (!isset($_POST['image']) || !isset($_POST['image_id'])) {
        echo json_encode([
            'success' => false,
            'error' => 'Missing required data'
        ]);
        exit;
    }

    $imageData = $_POST['image'];
    $clientImageId = $_POST['image_id'];
    $source = $_POST['source'] ?? 'webcam';

    // Validate base64 image
    if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $matches)) {
        $imageType = $matches[1];
        $imageData = substr($imageData, strpos($imageData, ',') + 1);
        $imageData = base64_decode($imageData);

        if ($imageData === false) {
            echo json_encode([
                'success' => false,
                'error' => 'Invalid image data'
            ]);
            exit;
        }
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Invalid image format'
        ]);
        exit;
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
        echo json_encode([
            'success' => false,
            'error' => 'Failed to save image file'
        ]);
        exit;
    }

    // Save to database
    $capturedImageModel = new CapturedImage();
    $serverImageId = $capturedImageModel->create([
        'user_id' => $userId,
        'image_path' => $relativePath,
        'source' => $source
    ]);

    if ($serverImageId > 0) {
        echo json_encode([
            'success' => true,
            'serverImageId' => $serverImageId,
            'imagePath' => $relativePath
        ]);
    } else {
        // Remove file if database insert failed
        unlink($filepath);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to save image to database'
        ]);
    }

} catch (Exception $e) {
    error_log("Error in SaveImageHandler: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Server error occurred'
    ]);
}