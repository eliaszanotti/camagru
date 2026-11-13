<?php
require_once __DIR__ . '/middleware/AuthMiddleware.php';
AuthMiddleware::requireAuth();
AuthMiddleware::requireCSRF();
require_once 'models/Comment.php';

$commentModel = new Comment();
$commentId = (int)($_POST['comment_id'] ?? 0);
$deleted = false;

if ($commentId) {
    $deleted = $commentModel->delete($commentId, $_SESSION['user_id']);
}

// Redirect back to the post
$postId = (int)($_POST['post_id'] ?? 0);
if ($postId) {
    $_SESSION['flash_toast'] = $deleted
        ? ['message' => 'Comment deleted successfully.', 'type' => 'success']
        : ['message' => 'Failed to delete comment.', 'type' => 'error'];
    header("Location: post.php?id=$postId");
} else {
    header('Location: index.php');
}
exit;
?>
