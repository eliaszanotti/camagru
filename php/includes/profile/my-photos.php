<?php if (empty($userPosts)): ?>
    <div class="text-center py-16 bg-base-200 rounded-box">
        <h3 class="text-xl font-semibold mb-2">No photos yet</h3>
        <p class="text-base-content/70 mb-4">Start creating and sharing your photos!</p>
        <a href="create.php" class="btn btn-primary">Create Your First Photo</a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <?php foreach ($userPosts as $post): ?>
            <div class="card bg-base-100 shadow-lg">
                <figure class="h-32">
                    <img src="<?php echo htmlspecialchars($post['image_path']); ?>"
                        alt="Your photo" class="w-full h-full object-cover">
                </figure>
                <div class="card-body p-3">
                    <p class="text-xs text-base-content/70">
                        <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                    </p>
                    <div class="flex gap-1">
                        <a href="post.php?id=<?php echo $post['id']; ?>" class="btn btn-primary btn-xs flex-1">View</a>
                        <form method="POST" action="delete-post.php" class="inline">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <button type="submit" class="btn btn-error btn-xs"
                                onclick="return confirm('Delete this photo?')">
                                🗑️
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>