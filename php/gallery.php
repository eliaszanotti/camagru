<?php
$pageTitle = 'Gallery';
require_once 'includes/header.php';

require_once 'models/Post.php';
require_once 'models/Like.php';
require_once 'models/Comment.php';

$postModel = new Post();
$likeModel = new Like();
$commentModel = new Comment();

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$postsPerPage = 9;

$posts = $postModel->getAll($page, $postsPerPage);
$totalPosts = $postModel->getTotalCount();
$totalPages = ceil($totalPosts / $postsPerPage);
?>

<!-- Main Content -->
<main class="container mx-auto px-4 py-8">
    <div class="text-center mb-8">
        <h1 class="text-4xl font-bold mb-4">Public Gallery</h1>
        <p class="text-lg text-base-content/70">Browse all photos created by our community</p>
    </div>

    <?php if (empty($posts)): ?>
        <div class="text-center py-16">
            <h2 class="text-2xl font-semibold mb-4">No photos yet</h2>
            <p class="text-base-content/70 mb-6">Be the first to create and share a photo!</p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="create.php" class="btn btn-primary">Create Photo</a>
            <?php else: ?>
                <a href="register.php" class="btn btn-primary">Sign Up to Create</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Posts Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
            <?php foreach ($posts as $post): ?>
                <div class="card bg-base-100 shadow-xl">
                    <figure class="h-48">
                        <img src="<?php echo htmlspecialchars($post['image_path']); ?>"
                             alt="Photo by <?php echo htmlspecialchars($post['username']); ?>"
                             class="w-full h-full object-cover">
                    </figure>
                    <div class="card-body">
                        <div class="flex items-center gap-2 text-sm text-base-content/70 mb-2">
                            <span>By <?php echo htmlspecialchars($post['username']); ?></span>
                            <span>•</span>
                            <span><?php echo date('M j, Y', strtotime($post['created_at'])); ?></span>
                        </div>

                        <?php if (!empty($post['caption'])): ?>
                            <p class="text-sm mb-2"><?php echo htmlspecialchars($post['caption']); ?></p>
                        <?php endif; ?>

                        <div class="flex justify-between items-center">
                            <div class="flex gap-4">
                                <?php
                                $isLiked = isset($_SESSION['user_id']) ? $likeModel->isLiked($post['id'], $_SESSION['user_id']) : false;
                                $likesCount = $likeModel->getCount($post['id']);
                                $commentsCount = count($commentModel->getByPostId($post['id']));
                                ?>

                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <form method="POST" action="like.php" class="inline">
                                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                        <input type="hidden" name="action" value="<?php echo $isLiked ? 'unlike' : 'like'; ?>">
                                        <button type="submit" class="btn btn-sm btn-ghost">
                                            <?php echo $isLiked ? '❤️' : '🤍'; ?> <?php echo $likesCount; ?>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-ghost" disabled>
                                        🤍 <?php echo $likesCount; ?>
                                    </button>
                                <?php endif; ?>

                                <button class="btn btn-sm btn-ghost" disabled>
                                    💬 <?php echo $commentsCount; ?>
                                </button>
                            </div>

                            <a href="post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-primary">View</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="flex justify-center">
                <div class="join">
                    <!-- Previous -->
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="join-item btn">«</a>
                    <?php else: ?>
                        <button class="join-item btn" disabled>«</button>
                    <?php endif; ?>

                    <!-- Page numbers -->
                    <?php
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);

                    if ($startPage > 1) {
                        echo '<a href="?page=1" class="join-item btn">1</a>';
                        if ($startPage > 2) {
                            echo '<button class="join-item btn" disabled>...</button>';
                        }
                    }

                    for ($i = $startPage; $i <= $endPage; $i++) {
                        $active = $i == $page ? 'btn-active' : '';
                        echo "<a href=\"?page=$i\" class=\"join-item btn $active\">$i</a>";
                    }

                    if ($endPage < $totalPages) {
                        if ($endPage < $totalPages - 1) {
                            echo '<button class="join-item btn" disabled>...</button>';
                        }
                        echo "<a href=\"?page=$totalPages\" class=\"join-item btn\">$totalPages</a>";
                    }
                    ?>

                    <!-- Next -->
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="join-item btn">»</a>
                    <?php else: ?>
                        <button class="join-item btn" disabled>»</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?php require_once 'includes/footer.php'; ?>