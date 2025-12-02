<?php
$pageTitle = 'My Gallery';
require_once 'middleware/AuthMiddleware.php';
AuthMiddleware::requireAuth();
require_once 'components/GalleryComponent.php';

require_once 'includes/header.php';
?>

<h1 class="text-3xl font-bold mb-8">My Gallery</h1>
<?php GalleryComponent::render(); ?>

<?php require_once 'includes/footer.php'; ?>