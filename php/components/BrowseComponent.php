<?php
class BrowseComponent
{
    public static function render(): void
    {
?>
        <div class="card border border-base-300 bg-base-100">
            <div class="card-body">
                <h3 class="card-title text-base">Upload an image</h3>
                <input type="file" id="fileInput" class="file-input file-input-bordered w-full" accept="image/jpeg,image/png,image/gif" aria-label="Choose an image" />
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
