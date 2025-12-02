<?php
$pageTitle = 'My Gallery';
require_once 'components/GalleryComponent.php';

require_once 'includes/header.php';
?>

<main class="p-8">
    <div class="container mx-auto">
        <h1 class="text-3xl font-bold mb-8">My Gallery</h1>
        <?php GalleryComponent::render(); ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>