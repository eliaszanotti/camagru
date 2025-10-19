<?php
$pageTitle = 'Create Photo';
require_once 'components/BrowseComponent.php';
require_once 'components/WebcamCaptureComponent.php';
require_once 'components/PreviewComponent.php';
require_once 'components/HistoryComponent.php';

require_once 'includes/header.php';
?>

<main class="p-8">
    <div class="container mx-auto space-y-8">
        <h1 class="text-3xl font-bold">Create Photo</h1>
        <div class="grid grid-cols-[1fr_1fr_auto_1fr] gap-8">
            <div><?php BrowseComponent::render(); ?></div>
            <div><?php WebcamCaptureComponent::render(); ?></div>
            <div class="divider divider-horizontal"></div>
            <?php PreviewComponent::render(); ?>
        </div>
        <?php HistoryComponent::render(); ?>
    </div>
</main>

<script>
    // Load all component scripts first
    <?php BrowseComponent::renderScripts(); ?>
    <?php WebcamCaptureComponent::renderScripts(); ?>
    <?php PreviewComponent::renderScripts(); ?>
    <?php HistoryComponent::renderScripts(); ?>

    // Initialize button event listeners after components are loaded
    document.addEventListener('DOMContentLoaded', function() {
        const discardBtn = document.getElementById('discardBtn');
        if (discardBtn) {
            discardBtn.addEventListener('click', function() {
                console.log('Discard button clicked');
                discardPreview();
            });
        } else {
            console.error('Discard button not found');
        }

        const saveBtn = document.getElementById('saveBtn');
        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                if (window.currentImageData && window.currentImageSource) {
                    saveToHistory(window.currentImageData, window.currentImageSource);

                    // Show success feedback
                    this.textContent = '✅ Saved!';
                    this.classList.remove('btn-success');
                    this.classList.add('btn-success', 'btn-disabled');

                    setTimeout(() => {
                        this.textContent = '💾 Save to History';
                        this.classList.remove('btn-disabled');
                        // Reset preview after saving
                        discardPreview();
                    }, 1500);
                }
            });
        } else {
            console.error('Save button not found');
        }
    });
</script>

<?php require_once 'includes/footer.php'; ?>