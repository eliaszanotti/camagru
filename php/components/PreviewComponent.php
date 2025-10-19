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
                    <div class="form-control grid grid-cols-2 gap-4">
                        <button id="discardBtn" class="btn btn-ghost btn-block" disabled>Discard</button>
                        <button id="saveBtn" class="btn btn-primary btn-block" disabled>Save to History</button>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }

    public static function renderScripts(): void
    {
    ?>
        <script>
            function discardPreview() {
                const previewArea = document.getElementById('previewArea');
                const discardBtn = document.getElementById('discardBtn');
                const saveBtn = document.getElementById('saveBtn');

                console.log('Discard preview called', { previewArea, discardBtn, saveBtn });

                // Reset preview area to original state
                previewArea.innerHTML = `
                    <p class="text-center">Preview will appear here</p>
                `;

                // Disable buttons
                discardBtn.disabled = true;
                saveBtn.disabled = true;

                // Clear current image data
                window.currentImageData = null;
                window.currentImageSource = null;
            }

            function updatePreview(imageData, source) {
                const previewArea = document.getElementById('previewArea');
                const saveBtn = document.getElementById('saveBtn');
                const discardBtn = document.getElementById('discardBtn');

                previewArea.innerHTML = `
                    <img src="${imageData}" alt="Preview" class="w-full h-full object-cover rounded-lg">
                `;

                // Enable buttons
                saveBtn.disabled = false;
                discardBtn.disabled = false;

                // Store current image data for saving
                window.currentImageData = imageData;
                window.currentImageSource = source;
            }

            // Make functions globally available
            window.discardPreview = discardPreview;
        </script>
<?php
    }
}
?>