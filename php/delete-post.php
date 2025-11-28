<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'models/Post.php';

$postModel = new Post();
$postId = $_POST['post_id'] ?? 0;

$success = false;
if ($postId) {
    // Get post info to delete the image file
    $post = $postModel->findById($postId);

    if ($post && $post['user_id'] == $_SESSION['user_id']) {
        // Delete from database first
        if ($postModel->delete($postId, $_SESSION['user_id'])) {
            // Delete the image file
            if (file_exists($post['image_path'])) {
                unlink($post['image_path']);
            }
            $success = true;
        }
    }
}

// Return JSON response for AJAX requests
header('Content-Type: application/json');
echo json_encode(['success' => $success]);
exit;
?>