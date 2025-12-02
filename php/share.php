<?php
$pageTitle = 'Share Photo';
require_once 'middleware/AuthMiddleware.php';
AuthMiddleware::requireAuth();

$postId = (int)($_GET['post'] ?? 0);

if (!$postId) {
    header('Location: create.php');
    exit;
}

require_once 'models/Post.php';
$postModel = new Post();
$post = $postModel->findById($postId);

if (!$post || $post['user_id'] != $_SESSION['user_id']) {
    header('Location: create.php');
    exit;
}

require_once 'includes/header.php';
?>

<div class="space-y-8">
    <h1 class="text-3xl font-bold">Share Your Photo</h1>
    <div class="grid sm:grid-cols-[1fr_2fr] gap-8">
        <div class="space-y-4">
            <h2 class="text-xl font-semibold">Preview</h2>
            <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="Photo to share" class="w-full aspect-square object-cover rounded-box">
        </div>
        <div class="space-y-4">
            <h2 class="text-xl font-semibold">Post Details</h2>
            <form method="POST" action="handlers/ShareHandler.php" class="space-y-6">
                <fieldset class="fieldset w-full">
                    <legend class="fieldset-legend">Caption</legend>
                    <textarea name="caption" class="textarea w-full"
                        placeholder="Add a caption to your photo..."
                        rows="4"><?php echo htmlspecialchars($post['caption'] ?? ''); ?></textarea>
                </fieldset>

                <fieldset class="fieldset">
                    <label class="cursor-pointer label">
                        <input type="checkbox" name="is_published" class="checkbox checkbox-primary" <?php echo $post['is_published'] ? 'checked' : ''; ?>>
                        <span class="label-text">Publish</span>
                    </label>
                </fieldset>

                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">

                <div id="shareAlerts">
                    <?php require_once __DIR__ . '/includes/alerts.php'; ?>
                </div>

                <div class="flex justify-end gap-4">
                    <button type="submit" class="btn btn-primary" id="shareBtn">
                        Update Post
                    </button>
                    <a href="create.php" class="btn btn-ghost">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>