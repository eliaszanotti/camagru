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
        <script>
            document.getElementById('browseArea').addEventListener('click', function() {
                document.getElementById('fileInput').click();
            });

            document.getElementById('fileInput').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Update preview area
                        updatePreview(e.target.result, 'file');

                        // Update browse area with thumbnail
                        const browseArea = document.getElementById('browseArea');
                        browseArea.innerHTML = `
                            <img src="${e.target.result}" alt="Selected image"
                                 class="w-full h-full object-cover rounded-lg">
                        `;
                    }
                    reader.readAsDataURL(file);
                }
            });
        </script>
<?php
    }
}
?>