<?php
class WebcamCaptureComponent
{
    public static function render(): void
    {
?>
        <div class="card border border-base-300 bg-base-100">
            <div class="card-body">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="card-title text-base">Use your camera</h3>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="aspect-square overflow-hidden rounded-box bg-base-300">
                        <video id="webcam" class="h-full w-full object-cover" autoplay playsinline></video>
                        <canvas id="canvas" class="hidden"></canvas>
                    </div>
                    <div class="form-control">
                        <button id="captureBtn" class="btn btn-outline btn-block">Take picture</button>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }

    public static function renderScripts(): void
    {
    ?>
        <script src="assets/js/webcam-component.js"></script>
<?php
    }
}
?>
