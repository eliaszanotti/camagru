<?php
class HistoryComponent
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
        <div class="card bg-base-200">
            <div class="card-body">
                <h2 class="card-title">My Photos</h2>
                <?php if (empty($userPosts)): ?>
                    <div class="text-center text-base-content/50 py-8">
                        <p>No photos saved yet</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-6 gap-4">
                        <?php foreach ($userPosts as $post): ?>
                            <div class="space-y-2">
                                <img src="<?php echo htmlspecialchars($post['image_path'] ?? ''); ?>"
                                    alt="Photo by <?php echo htmlspecialchars($post['username'] ?? 'Unknown'); ?>"
                                    class="w-full aspect-square object-cover rounded-box">
                                <div class="grid grid-cols-2 gap-2">
                                    <button onclick="deletePost(<?php echo $post['id']; ?>)"
                                        class="btn btn-error">
                                        Delete
                                    </button>
                                    <a href="share.php?post=<?php echo $post['id']; ?>"
                                        class="btn btn-primary">
                                        Share
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
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