<?php
class GalleryComponent
{
    public static function render(): void
    {
        $userPosts = [];

        if (isset($_SESSION['user_id'])) {
            require_once __DIR__ . '/../models/Post.php';
            $postModel = new Post();
            $userPosts = $postModel->getByUserId($_SESSION['user_id']);
        }

?>
        <div>
            <?php if (empty($userPosts)): ?>
                <div class="text-center text-base-content/50 py-8">
                    <p>No photos saved yet</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                    <?php foreach ($userPosts as $post): ?>
                        <div class="card bg-base-200">
                            <figure class="aspect-square">
                                <img src="<?php echo htmlspecialchars($post['image_path'] ?? ''); ?>"
                                    alt="Photo by <?php echo htmlspecialchars($post['username'] ?? 'Unknown'); ?>"
                                    class="w-full h-full object-cover object-center">
                            </figure>
                            <div class="card-body p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-sm font-medium truncate">
                                        <?php echo !empty($post['caption']) ? htmlspecialchars($post['caption']) : 'No caption'; ?>
                                    </h3>
                                    <?php if ($post['is_published']): ?>
                                        <span class="badge badge-success badge-sm">Published</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning badge-sm">Draft</span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-actions justify-end">
                                    <button onclick="deletePost(<?php echo $post['id']; ?>)"
                                        class="btn btn-error btn-sm">
                                        Delete
                                    </button>
                                    <a href="share.php?post=<?php echo $post['id']; ?>"
                                        class="btn btn-primary btn-sm">
                                        Share
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <script>
            function deletePost(postId) {
                if (confirm('Delete this photo?')) {
                    fetch('delete-post.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'post_id=' + postId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Failed to delete photo');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to delete photo');
                        });
                }
            }
        </script>
<?php
    }
}
?>