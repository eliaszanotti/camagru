<?php
$pageTitle = 'Create Photo';
require_once 'components/BrowseComponent.php';
require_once 'components/WebcamCaptureComponent.php';
require_once 'components/PreviewComponent.php';
require_once 'components/UnpublishedHistoryComponent.php';

require_once 'includes/header.php';

$statusMessages = [
    'success' => ['success', 'Your photo was saved to drafts.'],
    'missing_image' => ['error', 'Choose a photo before saving.'],
    'save_failed' => ['error', 'The photo could not be processed. Please try another image.'],
    'create_failed' => ['error', 'The photo could not be saved. Please try again.'],
];
$status = (($_GET['success'] ?? null) === '1') ? 'success' : ($_GET['error'] ?? null);
?>

<?php if (is_string($status) && isset($statusMessages[$status])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.showToast(<?= json_encode($statusMessages[$status][1], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= json_encode($statusMessages[$status][0]) ?>);
        });
    </script>
<?php endif; ?>

<div class="space-y-8">
    <div>
        <h1 class="text-3xl font-bold tracking-tight">Create a photo</h1>
        <p class="mt-2 max-w-2xl text-base-content/70">Choose an image, add a sticker, then save it to your drafts.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <section aria-labelledby="source-title" class="space-y-4 lg:col-span-1 lg:flex lg:h-full lg:flex-col">
            <h2 id="source-title" class="text-lg font-semibold">1. Choose a photo</h2>
            <?php BrowseComponent::render(); ?>
            <?php WebcamCaptureComponent::render(); ?>
        </section>

        <section aria-labelledby="edit-title" class="lg:col-span-2">
            <h2 id="edit-title" class="mb-4 text-lg font-semibold">2. Preview and edit</h2>
            <?php PreviewComponent::render(); ?>
        </section>
    </div>

    <section id="draft-photos" aria-label="Draft photos">
        <?php UnpublishedHistoryComponent::render(); ?>
    </section>
</div>

<?php BrowseComponent::renderScripts(); ?>
<?php WebcamCaptureComponent::renderScripts(); ?>
<?php PreviewComponent::renderScripts(); ?>

<?php require_once 'includes/footer.php'; ?>
