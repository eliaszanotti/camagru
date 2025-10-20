<?php
class WebcamCaptureComponent
{
    public static function render(): void
    {
?>
        <div class="card bg-base-200">
            <div class="card-body">
                <h2 class="card-title">Webcam</h2>
                <div class="space-y-4">
                    <div class="aspect-square">
                        <video id="webcam" class="w-full h-full rounded-box bg-base-300" autoplay></video>
                        <canvas id="canvas" class="hidden"></canvas>
                    </div>
                    <div class="form-control">
                        <button id="captureBtn" class="btn btn-primary btn-block">Take Picture</button>
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