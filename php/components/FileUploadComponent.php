<?php
class FileUploadComponent {
    public static function render(FormService $formService): void {
        ?>
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">Upload Image</h2>
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Choose Image</span>
                        </label>
                        <input type="file" name="image" id="fileInput" class="file-input file-input-bordered w-full"
                            accept="image/jpeg,image/png,image/gif" required>
                        <label class="label">
                            <span class="label-text-alt">JPEG, PNG or GIF (Max 5MB)</span>
                        </label>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Caption (optional)</span>
                        </label>
                        <textarea name="caption" class="textarea textarea-bordered h-24"
                            maxlength="500"><?php echo htmlspecialchars($_POST['caption'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-full">Upload Photo</button>
                </form>
            </div>
        </div>
        <?php
    }
}
?>