<?php
require_once 'models/Post.php';
require_once 'models/Like.php';

$postModel = new Post();
$likeModel = new Like();

// Get current page from URL parameter, default to 1
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$postsPerPage = 8;

// Get posts for current page
$posts = $postModel->getAll($currentPage, $postsPerPage);
$totalPosts = $postModel->getTotalCount();
$totalPages = ceil($totalPosts / $postsPerPage);
?>

<section class="mt-16">
    <h2 class="text-3xl font-bold mb-8">Recent Posts</h2>

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
                <?php
                $likesCount = $likeModel->getCount($post['id']);
                $imageSrc = $post['image_path'] ?: "https://picsum.photos/seed/{$post['id']}/400/300.jpg";
                $caption = $post['caption'] ?: 'Untitled Post';
                $date = date('M j, Y', strtotime($post['created_at']));
                ?>
                <div class="card bg-base-200">
                    <div class="card-body">
                        <div class="card-title"><?php echo htmlspecialchars($caption); ?></div>
                        <figure class="mb-4">
                            <img src="<?php echo htmlspecialchars($imageSrc); ?>"
                                 alt="<?php echo htmlspecialchars($caption); ?>"
                                 class="w-full h-48 object-cover rounded-box">
                        </figure>
                        <div class="flex items-center gap-2 text-sm text-base-content/50 mb-4">
                            <span>By <?php echo htmlspecialchars($post['username']); ?></span>
                            <span>•</span>
                            <span><?php echo $date; ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <span class="text-sm"><?php echo $likesCount; ?> likes</span>
                            </div>
                            <a href="post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-primary">View</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages >= 1): ?>
            <div class="flex justify-center">
                <div class="join">
                    <!-- Previous button -->
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?php echo $currentPage - 1; ?>" class="join-item btn">«</a>
                    <?php else: ?>
                        <button class="join-item btn" disabled>«</button>
                    <?php endif; ?>

                    <!-- Page numbers -->
                    <?php
                    $maxVisiblePages = 5;
                    $startPage = max(1, $currentPage - floor($maxVisiblePages / 2));
                    $endPage = min($totalPages, $startPage + $maxVisiblePages - 1);

                    if ($endPage - $startPage < $maxVisiblePages - 1) {
                        $startPage = max(1, $endPage - $maxVisiblePages + 1);
                    }

                    if ($startPage > 1) {
                        echo '<a href="?page=1" class="join-item btn">1</a>';
                        if ($startPage > 2) {
                            echo '<button class="join-item btn" disabled>...</button>';
                        }
                    }

                    for ($i = $startPage; $i <= $endPage; $i++) {
                        $active = $i == $currentPage ? 'btn-active' : '';
                        echo "<a href=\"?page=$i\" class=\"join-item btn $active\">$i</a>";
                    }

                    if ($endPage < $totalPages) {
                        if ($endPage < $totalPages - 1) {
                            echo '<button class="join-item btn" disabled>...</button>';
                        }
                        echo "<a href=\"?page=$totalPages\" class=\"join-item btn\">$totalPages</a>";
                    }
                    ?>

                    <!-- Next button -->
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?php echo $currentPage + 1; ?>" class="join-item btn">»</a>
                    <?php else: ?>
                        <button class="join-item btn" disabled>»</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>