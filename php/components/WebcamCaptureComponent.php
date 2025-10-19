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
                        <video id="webcam" class="w-full h-full rounded-box bg-black" autoplay></video>
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
        <script>
            let stream = null;

            async function startWebcam() {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            width: {
                                ideal: 640
                            },
                            height: {
                                ideal: 640
                            }
                        }
                    });
                    const video = document.getElementById('webcam');
                    video.srcObject = stream;
                } catch (err) {
                    console.error('Error accessing webcam:', err);
                    const webcamContainer = document.getElementById('webcam').parentElement;
                    webcamContainer.innerHTML = `
                        <div class="w-full h-full bg-base-200 rounded-lg flex items-center justify-center">
                            <div class="text-center text-base-content/50">
                                <div class="text-4xl mb-2">📷</div>
                                <p class="text-sm">Webcam not available</p>
                            </div>
                        </div>
                    `;
                    document.getElementById('captureBtn').disabled = true;
                }
            }

            document.getElementById('captureBtn').addEventListener('click', function() {
                const video = document.getElementById('webcam');
                const canvas = document.getElementById('canvas');
                const context = canvas.getContext('2d');

                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0);

                const imageData = canvas.toDataURL('image/jpeg', 0.9);
                updatePreview(imageData, 'webcam');
            });

            // Start webcam when page loads
            startWebcam();

            // Cleanup on page unload
            window.addEventListener('beforeunload', function() {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
            });
        </script>
<?php
    }
}
?>