<?php
$pageTitle = 'Home';
require_once 'includes/header.php';
?>

<main class="p-16">
    <div class="container mx-auto">
        <section class="hero min-h-80 bg-gradient-to-r from-primary to-secondary text-primary-content rounded-box">
            <div class="hero-content text-center">
                <div class="space-y-4">
                    <h1 class="text-4xl font-bold">Welcome to Camagru!</h1>
                    <p class="text-lg">Create, share and enjoy photo edits with webcam filters</p>
                    <div class="space-x-2">
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <a href="register.php" class="btn btn-lg btn-neutral">Register</a>
                            <a href="login.php" class="btn  btn-lg btn-neutral">Login</a>
                        <?php else: ?>
                            <a href="create.php" class="btn btn-neutral btn-lg">Start Creating</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
        <?php require_once 'includes/recent-posts.php'; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>