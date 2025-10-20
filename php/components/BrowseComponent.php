<?php
class BrowseComponent
{
    public static function render(): void
    {
?>
        <div class="card bg-base-200">
            <div class="card-body">
                <h2 class="card-title">Browse Image</h2>
                <input type="file" id="fileInput" class="file-input w-full" accept="image/jpeg,image/png,image/gif" />
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