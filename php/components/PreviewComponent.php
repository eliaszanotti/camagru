<?php
class PreviewComponent
{
    public static function render(): void
    {
?>
        <div class="card bg-base-200">
            <div class="card-body">
                <h2 class="card-title">Preview</h2>
                <div class="space-y-4">
                    <div class="aspect-square">
                        <div id="previewArea" class="w-full h-full bg-base-300 rounded-box flex items-center justify-center">
                            <p class="text-center">Preview will appear here</p>
                        </div>
                    </div>
                    <form id="savePostForm">
                    <div class="form-control grid grid-cols-2 gap-4">
                        <button type="button" id="discardBtn" class="btn btn-ghost btn-block" disabled>Discard</button>
                        <button type="submit" id="saveBtn" class="btn btn-primary btn-block" disabled>Save to History</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    <?php
    }

    public static function renderScripts(): void
    {
    ?>
        <script src="assets/js/preview-component.js"></script>
<?php
    }
}
?>