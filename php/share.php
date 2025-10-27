<?php
$pageTitle = 'Share Photo';
require_once 'middleware/AuthMiddleware.php';
AuthMiddleware::requireAuth();

require_once 'includes/header.php';
?>

<main class="p-16">
    <div class="container mx-auto space-y-8">
        <h1 class="text-3xl font-bold">Share Your Photo</h1>

        <div id="shareContent">
            <div class="text-center py-8">
                <p class="text-base-content/50">Loading image...</p>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/alerts.php'; ?>

<script src="assets/js/share-component.js"></script>
<?php require_once 'includes/footer.php'; ?>