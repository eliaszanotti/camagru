<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'models/Comment.php';

$commentModel = new Comment();
$commentId = $_POST['comment_id'] ?? 0;

if ($commentId) {
    $commentModel->delete($commentId, $_SESSION['user_id']);
}

// Redirect back to the post
$postId = $_GET['post_id'] ?? $_SERVER['HTTP_REFERER'];
if ($postId) {
    header("Location: post.php?id=$postId");
} else {
    header('Location: gallery.php');
}
exit;
?>