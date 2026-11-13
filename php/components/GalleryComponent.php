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
                        <div class="card bg-base-200 cursor-pointer"
                            role="link"
                            tabindex="0"
                            aria-label="<?php echo $post['is_published'] ? 'Edit photo' : 'Share photo'; ?>"
                            data-href="share.php?post=<?php echo (int) $post['id']; ?>">
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
                                    <button onclick="openDeletePostDialog(<?php echo (int) $post['id']; ?>)"
                                        class="btn btn-error btn-sm">
                                        Delete
                                    </button>
                                    <a href="share.php?post=<?php echo $post['id']; ?>"
                                        class="btn btn-primary btn-sm">
                                        <?php echo $post['is_published'] ? 'Edit' : 'Share'; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <dialog id="delete-photo-dialog" class="modal"
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
        <script>
            document.querySelectorAll('.card[data-href]').forEach((card) => {
                card.addEventListener('click', (event) => {
                    if (event.target.closest('button, a')) {
                        return;
                    }

                    window.location.href = card.dataset.href;
                });

                card.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter' || event.key === ' ') {
                        event.preventDefault();
                        window.location.href = card.dataset.href;
                    }
                });
            });

            let postIdToDelete = null;

            function openDeletePostDialog(postId) {
                postIdToDelete = postId;
                document.getElementById('delete-photo-dialog').showModal();
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
