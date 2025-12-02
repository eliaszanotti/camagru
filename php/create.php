<?php
$pageTitle = 'Create Photo';
require_once 'components/BrowseComponent.php';
require_once 'components/WebcamCaptureComponent.php';
require_once 'components/PreviewComponent.php';
require_once 'components/UnpublishedHistoryComponent.php';

require_once 'includes/header.php';
?>

<div class="space-y-8">
    <h1 class="text-3xl font-bold">Create Photo</h1>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <div><?php BrowseComponent::render(); ?></div>
        <div><?php WebcamCaptureComponent::render(); ?></div>
        <div class="sm:col-span-2 lg:col-span-1"><?php PreviewComponent::render(); ?></div>
    </div>
    <?php UnpublishedHistoryComponent::render(); ?>
</div>

<?php BrowseComponent::renderScripts(); ?>
<?php WebcamCaptureComponent::renderScripts(); ?>
<?php PreviewComponent::renderScripts(); ?>

<?php require_once 'includes/footer.php'; ?>