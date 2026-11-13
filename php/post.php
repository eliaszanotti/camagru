<?php
$pageTitle = 'Photo Details';
require_once 'includes/header.php';
require_once 'models/Like.php';
require_once 'handlers/CommentHandler.php';
require_once 'components/LikeButton.php';

$postId = (int)($_GET['id'] ?? 0);

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
$flashToast = $_SESSION['flash_toast'] ?? null;
unset($_SESSION['flash_toast']);

if ($commentHandler->shouldClearForm()) {
    unset($_POST['content']);
}
?>

<?php if ($formService->hasErrors()): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.showToast(<?= json_encode(implode(' ', $formService->getErrors()), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'error');
        });
    </script>
<?php elseif ($formService->hasSuccess()): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.showToast(<?= json_encode($formService->getSuccess(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>);
        });
    </script>
<?php endif; ?>

<?php if ($flashToast && isset($flashToast['message'], $flashToast['type'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.showToast(
                <?= json_encode($flashToast['message'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                <?= json_encode($flashToast['type'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>
            );
        });
    </script>
<?php endif; ?>

<div class="grid md:grid-cols-[1fr_2fr] gap-8">
    <div class="space-y-4">
        <a href="index.php" class="btn btn-ghost">Back to Home</a>
        <div class="card bg-base-200 overflow-hidden">
            <img src="<?php echo htmlspecialchars($post['image_path']); ?>"
                alt="Photo by <?php echo htmlspecialchars($post['username']); ?>"
                class="w-full aspect-square object-center object-cover">
            <div class="card-body">
                <div class="flex items-center gap-2 text-sm text-base-content/70 mb-2">
                    <span>By <?php echo htmlspecialchars($post['username']); ?></span>
                    <span>•</span>
                    <span><?php echo date('M j, Y g:i A', strtotime($post['created_at'])); ?></span>
                </div>

                <?php if (!empty($post['caption'])): ?>
                    <p class="text-lg mb-4"><?php echo htmlspecialchars($post['caption']); ?></p>
                <?php endif; ?>

                <div class="flex items-center gap-6 mb-4">
                    <?php echo LikeButton::create($post['id'], $_SESSION['user_id'] ?? 0); ?>
                    <div class="text-base-content/70">
                        <span id="comments-count"><?php echo count($comments); ?></span> Comments
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="card bg-base-200">
            <div class="card-body space-y-4">
                <h2 class="card-title">Comments</h2>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="space-y-4">
                        <form method="POST" class="space-y-4" id="comment-form">
                            <div class="form-control">
                                <textarea name="content" class="textarea textarea-bordered w-full"
                                    placeholder="Add a comment..." maxlength="500"
                                    required id="comment-content"><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="btn btn-primary">Post Comment</button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="alert alert-primary">
                        <p>Please <a href="login.php" class="link">login</a> to add comments.</p>
                    </div>
                <?php endif; ?>

                <?php if (empty($comments)): ?>
                    <div class="text-center py-8 bg-base-100 rounded-box">
                        <p class="text-base-content/50">No comments yet. Be the first to comment!</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($comments as $comment): ?>
                            <div class="bg-base-100 rounded-box p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold"><?php echo htmlspecialchars($comment['username']); ?></span>
                                        <span class="text-sm text-base-content/70">
                                            <?php echo date('M j, Y g:i A', strtotime($comment['created_at'])); ?>
                                        </span>
                                    </div>

                                    <?php if (isset($_SESSION['user_id']) && $comment['user_id'] == $_SESSION['user_id']): ?>
                                        <form method="POST" action="delete-comment.php" class="inline">
                                            <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                            <input type="hidden" name="post_id" value="<?php echo $postId; ?>">
                                            <button type="button" class="btn btn-error btn-outline"
                                                onclick="document.getElementById('delete-comment-<?php echo $comment['id']; ?>').showModal()">
                                                Delete
                                            </button>
                                            <dialog id="delete-comment-<?php echo $comment['id']; ?>" class="modal"
                                                onclick="if (event.target === this) this.close()"
                                                onkeydown="if (event.key === 'Enter') { event.preventDefault(); this.querySelector('[data-confirm-delete]').click(); }">
                                                <div class="modal-box">
                                                    <h3 class="text-lg font-bold">Delete this comment?</h3>
                                                    <p class="py-4 text-base-content/70">This action cannot be undone.</p>
                                                    <div class="modal-action">
                                                        <button type="button" class="btn btn-ghost"
                                                            onclick="this.closest('dialog').close()">Cancel</button>
                                                        <button type="submit" class="btn btn-error" data-confirm-delete>Delete comment</button>
                                                    </div>
                                                </div>
                                            </dialog>
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
</div>

<script>
    document.getElementById('comment-content')?.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey && !event.isComposing) {
            event.preventDefault();
            event.currentTarget.form?.requestSubmit();
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>
