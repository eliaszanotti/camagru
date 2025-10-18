<?php
$pageTitle = 'Create Photo';
require_once 'includes/header.php';
require_once 'handlers/CreatePostHandler.php';

$createPostHandler = new CreatePostHandler();
$formService = $createPostHandler->getFormService();
$userPosts = $createPostHandler->getUserPosts();

if ($createPostHandler->shouldClearForm()) {
    unset($_POST);
}
?>

<!-- Main Content -->
<main class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8">Create Photo</h1>

        <?php require_once __DIR__ . '/includes/alerts.php'; ?>

        <?php if ($formService->hasSuccess()): ?>
            <div class="mt-2">
                <a href="gallery.php" class="btn btn-primary btn-sm">View in Gallery</a>
            </div>
        <?php endif; ?>

        <?php if ($formService->getError('image')): ?>
            <div class="alert alert-error mb-6">
                <span><?php echo htmlspecialchars($formService->getError('image')); ?></span>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Upload Form -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">Upload Image</h2>
                    <form method="POST" enctype="multipart/form-data" class="space-y-4">
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Choose Image</span>
                            </label>
                            <input type="file" name="image" class="file-input file-input-bordered w-full"
                                   accept="image/jpeg,image/png,image/gif" required>
                            <label class="label">
                                <span class="label-text-alt">JPEG, PNG or GIF (Max 5MB)</span>
                            </label>
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Caption (optional)</span>
                            </label>
                            <textarea name="caption" class="textarea textarea-bordered h-24"
                                      maxlength="500"><?php echo htmlspecialchars($_POST['caption'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-full">Upload Photo</button>
                    </form>
                </div>
            </div>

            <!-- Preview -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">Preview</h2>
                    <div id="preview" class="aspect-square bg-base-200 rounded-lg flex items-center justify-center">
                        <div class="text-center text-base-content/50">
                            <div class="text-6xl mb-2">📷</div>
                            <p>Image preview will appear here</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Your Recent Photos -->
        <div class="mt-8">
            <h2 class="text-2xl font-bold mb-4">Your Recent Photos</h2>
            <?php if (empty($userPosts)): ?>
                <div class="text-center py-8 bg-base-200 rounded-lg">
                    <p class="text-base-content/70">You haven't created any photos yet</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <?php foreach (array_slice($userPosts, 0, 8) as $post): ?>
                        <div class="card bg-base-100 shadow-lg">
                            <figure class="h-32">
                                <img src="<?php echo htmlspecialchars($post['image_path']); ?>"
                                     alt="Your photo" class="w-full h-full object-cover">
                            </figure>
                            <div class="card-body p-3">
                                <p class="text-xs text-base-content/70">
                                    <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                                </p>
                                <form method="POST" action="delete-post.php" class="inline">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" class="btn btn-error btn-xs w-full"
                                            onclick="return confirm('Delete this photo?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
// Image preview
document.querySelector('input[type="file"]').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('preview');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="w-full h-full object-contain rounded-lg">`;
        }
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = `
            <div class="text-center text-base-content/50">
                <div class="text-6xl mb-2">📷</div>
                <p>Image preview will appear here</p>
            </div>
        `;
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>