<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'models/Like.php';

$likeModel = new Like();
$postId = $_POST['post_id'] ?? 0;
$action = $_POST['action'] ?? '';

if ($postId && in_array($action, ['like', 'unlike'])) {
    if ($action === 'like') {
        $likeData = [
            'post_id' => $postId,
            'user_id' => $_SESSION['user_id']
        ];
        $likeModel->create($likeData);
    } else {
        $likeModel->delete($postId, $_SESSION['user_id']);
    }
}

// Redirect back to referring page
$referer = $_SERVER['HTTP_REFERER'] ?? 'gallery.php';
header("Location: $referer");
exit;
?>