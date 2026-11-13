<?php
require_once __DIR__ . '/StickerComponent.php';

class PreviewComponent
{
    public static function render(): void
    {
?>
        <div class="card border border-base-300 bg-base-100">
            <div class="card-body gap-5">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h3 class="card-title text-base">Your composition</h3>
                    <p id="editorHint" class="text-sm text-base-content/60">Choose a photo to begin.</p>
                </div>
                <div class="grid gap-5 md:grid-cols-2 md:items-start">
                    <div id="previewArea" class="flex aspect-square w-full items-center justify-center overflow-hidden rounded-box border border-base-300 bg-base-200">
                        <div class="max-w-xs px-6 text-center">
                            <span class="mb-3 block text-4xl" aria-hidden="true">▧</span>
                            <p class="font-medium">Your photo will appear here</p>
                            <p class="mt-1 text-sm text-base-content/60">Upload an image or take one with your camera.</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <h4 class="font-semibold">Add a sticker</h4>
                            <p class="mt-1 text-sm text-base-content/60">Choose one to add it to your photo.</p>
                        </div>
                        <?php StickerComponent::render(); ?>
                    </div>
                </div>
                <form id="savePostForm">
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
