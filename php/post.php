<?php
$pageTitle = 'Photo Details';
require_once 'includes/header.php';
require_once 'models/Like.php';
require_once 'handlers/CommentHandler.php';

$postId = $_GET['id'] ?? 0;

if (!$postId) {
    header('Location: index.php');
    exit;
}

$commentHandler = new CommentHandler($postId);
$formService = $commentHandler->getFormService();
$post = $commentHandler->getPost();
$comments = $commentHandler->getComments();

$likeModel = new Like();
$likesCount = $likeModel->getCount($postId);
$isLiked = isset($_SESSION['user_id']) ? $likeModel->isLiked($postId, $_SESSION['user_id']) : false;

if ($commentHandler->shouldClearForm()) {
    unset($_POST['content']);
}
?>

<!-- Main Content -->
<main class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Back button -->
        <div class="mb-6">
            <a href="index.php" class="btn btn-ghost">← Back to Home</a>
        </div>

        <!-- Photo Details -->
        <div class="card bg-base-100 shadow-xl mb-8">
            <figure class="max-h-96">
                <img src="<?php echo htmlspecialchars($post['image_path']); ?>"
                     alt="Photo by <?php echo htmlspecialchars($post['username']); ?>"
                     class="w-full h-full object-contain">
            </figure>
            <div class="card-body">
                <div class="flex items-center gap-2 text-sm text-base-content/70 mb-2">
                    <span>By <?php echo htmlspecialchars($post['username']); ?></span>
                    <span>•</span>
                    <span><?php echo date('M j, Y g:i A', strtotime($post['created_at'])); ?></span>
                </div>

                <?php if (!empty($post['caption'])): ?>
                    <p class="text-lg mb-4"><?php echo htmlspecialchars($post['caption']); ?></p>
                <?php endif; ?>

                <!-- Like and Stats -->
                <div class="flex items-center gap-6 mb-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <form method="POST" action="like.php" class="inline">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <input type="hidden" name="action" value="<?php echo $isLiked ? 'unlike' : 'like'; ?>">
                            <button type="submit" class="btn btn-ghost">
                                <?php echo $isLiked ? '❤️' : '🤍'; ?> <span id="likes-count"><?php echo $likesCount; ?></span> Likes
                            </button>
                        </form>
                    <?php else: ?>
                        <button class="btn btn-ghost" disabled>
                            🤍 <span id="likes-count"><?php echo $likesCount; ?></span> Likes
                        </button>
                    <?php endif; ?>

                    <div class="text-base-content/70">
                        💬 <span id="comments-count"><?php echo count($comments); ?></span> Comments
                    </div>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title mb-4">Comments</h2>

                <!-- Add Comment Form -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="mb-6">
                        <?php require_once __DIR__ . '/includes/alerts.php'; ?>

                        <?php if ($formService->getError('content')): ?>
                            <div class="alert alert-error mb-4">
                                <span><?php echo htmlspecialchars($formService->getError('content')); ?></span>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="space-y-4">
                            <div class="form-control">
                                <textarea name="content" class="textarea textarea-bordered h-24"
                                          placeholder="Add a comment..." maxlength="500"
                                          required><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                                <label class="label">
                                    <span class="label-text-alt">Max 500 characters</span>
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary">Post Comment</button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-6">
                        <p>Please <a href="login.php" class="link link-primary">login</a> to add comments.</p>
                    </div>
                <?php endif; ?>

                <!-- Comments List -->
                <?php if (empty($comments)): ?>
                    <div class="text-center py-8 bg-base-200 rounded-lg">
                        <p class="text-base-content/70">No comments yet. Be the first to comment!</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($comments as $comment): ?>
                            <div class="bg-base-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="avatar placeholder">
                                            <div class="bg-neutral text-neutral-content rounded-full w-8 h-8">
                                                <span class="text-sm"><?php echo strtoupper(substr($comment['username'], 0, 1)); ?></span>
                                            </div>
                                        </div>
                                        <span class="font-semibold"><?php echo htmlspecialchars($comment['username']); ?></span>
                                        <span class="text-sm text-base-content/70">
                                            <?php echo date('M j, Y g:i A', strtotime($comment['created_at'])); ?>
                                        </span>
                                    </div>

                                    <?php if (isset($_SESSION['user_id']) && $comment['user_id'] == $_SESSION['user_id']): ?>
                                        <form method="POST" action="delete-comment.php" class="inline">
                                            <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                            <button type="submit" class="btn btn-error btn-xs"
                                                    onclick="return confirm('Delete this comment?')">
                                                Delete
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <p class="text-base-content"><?php echo htmlspecialchars($comment['content']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>