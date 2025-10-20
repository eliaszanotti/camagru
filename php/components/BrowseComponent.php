<?php
class BrowseComponent
{
    public static function render(): void
    {
?>
        <div class="card bg-base-200">
            <div class="card-body">
                <h2 class="card-title">Browse Image</h2>
                <div id="browseArea" class="aspect-square bg-base-300 rounded-box flex items-center justify-center cursor-pointer hover:bg-base-400 transition-colors">
                    <div class="text-center text-base-content/50">
                        <div class="text-4xl mb-2">📁</div>
                        <p>Click to browse</p>
                    </div>
                </div>
                <input type="file" id="fileInput" class="hidden" accept="image/jpeg,image/png,image/gif" />
            </div>
        </div>
    <?php
    }

    public static function renderScripts(): void
    {
    ?>
        <script src="assets/js/browse-component.js"></script>
<?php
    }
}
?>