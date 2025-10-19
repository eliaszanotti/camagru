<?php
class SuperposableImagesComponent {
    private static $overlays = [
        ['id' => 'frame1', 'name' => 'Picture Frame', 'file' => 'frame1.png'],
        ['id' => 'hat1', 'name' => 'Party Hat', 'file' => 'hat1.png'],
        ['id' => 'glasses1', 'name' => 'Cool Glasses', 'file' => 'glasses1.png'],
        ['id' => 'mustache1', 'name' => 'Mustache', 'file' => 'mustache1.png'],
    ];

    public static function render(): void {
        ?>
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">Choose Effect</h2>
                <div class="grid grid-cols-2 gap-2">
                    <?php foreach (self::$overlays as $overlay): ?>
                        <div class="form-control">
                            <label class="label cursor-pointer">
                                <input type="radio" name="overlay" value="<?php echo $overlay['id']; ?>"
                                       class="radio radio-primary" onchange="updateCaptureButton()">
                                <div class="label-text">
                                    <div class="flex items-center gap-2">
                                        <div class="w-12 h-12 bg-base-200 rounded flex items-center justify-center">
                                            <span class="text-xl">🖼️</span>
                                        </div>
                                        <span><?php echo htmlspecialchars($overlay['name']); ?></span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
    }

    public static function getOverlayPath(string $overlayId): ?string {
        foreach (self::$overlays as $overlay) {
            if ($overlay['id'] === $overlayId) {
                return '/assets/images/overlays/' . $overlay['file'];
            }
        }
        return null;
    }
}
?>