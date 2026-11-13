<?php
class UnpublishedHistoryComponent
{
    public static function render(): void
    {
        $userPosts = [];

        if (isset($_SESSION['user_id'])) {
            require_once __DIR__ . '/../models/Post.php';
            $postModel = new Post();
            $userPosts = $postModel->getUnpublishedByUserId($_SESSION['user_id']);
        }

?>
        <div class="card bg-base-200">
            <div class="card-body">
                <h2 class="card-title">Draft Photos</h2>
                <?php if (empty($userPosts)): ?>
                    <div class="text-center text-base-content/50 py-8">
                        <p>No draft photos saved yet</p>
                    </div>
                <?php else: ?>
                    <div class="grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                        <?php foreach ($userPosts as $post): ?>
                            <div class="space-y-2">
                                <img src="<?php echo htmlspecialchars($post['image_path'] ?? ''); ?>"
                                    alt="Photo by <?php echo htmlspecialchars($post['username'] ?? 'Unknown'); ?>"
                                    class="w-full aspect-square object-cover rounded-box">
                                <div class="grid grid-cols-2 gap-2">
                                    <button onclick="openDeletePostDialog(<?php echo (int) $post['id']; ?>)"
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
                <dialog id="delete-draft-photo-dialog" class="modal"
                    onclick="if (event.target === this) this.close()"
                    onkeydown="if (event.key === 'Enter') { event.preventDefault(); this.querySelector('[data-confirm-delete]').click(); }">
                    <div class="modal-box">
                        <h3 class="text-lg font-bold">Delete this photo?</h3>
                        <p class="py-4 text-base-content/70">This action cannot be undone.</p>
                        <div class="modal-action">
                            <button type="button" class="btn btn-ghost" onclick="this.closest('dialog').close()">Cancel</button>
                            <button type="button" class="btn btn-error" data-confirm-delete onclick="confirmDeletePost()">Delete photo</button>
                        </div>
                    </div>
                </dialog>
            </div>
        </div>
        <script>
            let postIdToDelete = null;

            function openDeletePostDialog(postId) {
                postIdToDelete = postId;
                document.getElementById('delete-draft-photo-dialog').showModal();
            }

            function confirmDeletePost() {
                if (postIdToDelete === null) {
                    return;
                }

                fetch('delete-post.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'post_id=' + postIdToDelete
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
        </script>
<?php
    }
}
?>
