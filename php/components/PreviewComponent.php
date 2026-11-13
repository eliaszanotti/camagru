<?php
require_once __DIR__ . '/StickerComponent.php';

class PreviewComponent
{
    public static function render(): void
    {
?>
        <div class="card border border-base-300 bg-base-100">
            <div class="card-body gap-5">
                <div class="preview-editor-layout flex flex-col gap-5">
                    <div id="previewArea" class="flex aspect-square w-full self-start items-center justify-center overflow-hidden rounded-box border border-base-300 bg-base-200">
                        <div class="max-w-xs px-6 text-center">
                            <span class="mb-3 block text-4xl" aria-hidden="true">▧</span>
                            <p class="font-medium">Your photo will appear here</p>
                            <p class="mt-1 text-sm text-base-content/60">Upload an image or take one with your camera.</p>
                        </div>
                    </div>
                    <div class="relative min-h-0">
                        <div class="absolute inset-0 h-full min-h-0 overflow-y-auto">
                            <?php StickerComponent::render(); ?>
                        </div>
                    </div>
                </div>
                <form id="savePostForm">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(AuthMiddleware::getCSRFToken(), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="flex flex-col-reverse gap-3 border-t border-base-300 pt-4 sm:flex-row sm:items-center sm:justify-between">
                        <button type="button" id="discardBtn" class="btn btn-ghost" disabled>Start over</button>
                        <button type="submit" id="saveBtn" class="btn btn-primary" disabled>Save to drafts</button>
                    </div>
                </form>
            </div>
        </div>
    <?php
    }

    public static function renderScripts(): void
    {
    ?>
        <script src="assets/js/preview-component.js"></script>
<?php
        StickerComponent::renderScripts();
    }
}
?>
