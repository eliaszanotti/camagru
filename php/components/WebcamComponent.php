<?php
class WebcamComponent {
    public static function render(): void {
        ?>
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">Webcam Capture</h2>
                <div class="relative">
                    <video id="webcam" class="w-full rounded-lg bg-black" autoplay></video>
                    <canvas id="canvas" class="hidden"></canvas>
                </div>
                <div class="form-control mt-4">
                    <button id="captureBtn" class="btn btn-primary w-full" disabled>📷 Capture Photo</button>
                    <label class="label">
                        <span class="label-text-alt text-warning">Please select an effect first</span>
                    </label>
                </div>
            </div>
        </div>
        <?php
    }

    public static function renderScripts(): void {
        ?>
        <script>
            let stream = null;

            async function startWebcam() {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            width: { ideal: 640 },
                            height: { ideal: 480 }
                        }
                    });
                    const video = document.getElementById('webcam');
                    video.srcObject = stream;
                } catch (err) {
                    console.error('Error accessing webcam:', err);
                    const webcamDiv = document.getElementById('webcam').parentElement;
                    webcamDiv.innerHTML = `
                        <div class="aspect-square bg-base-200 rounded-lg flex items-center justify-center">
                            <div class="text-center text-base-content/50">
                                <div class="text-6xl mb-2">📷</div>
                                <p>Webcam not available</p>
                                <p class="text-sm mt-2">Please use file upload instead</p>
                            </div>
                        </div>
                    `;
                }
            }

            document.getElementById('captureBtn').addEventListener('click', async function() {
                const video = document.getElementById('webcam');
                const canvas = document.getElementById('canvas');
                const context = canvas.getContext('2d');

                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                context.drawImage(video, 0, 0);

                canvas.toBlob(async function(blob) {
                    const formData = new FormData();
                    formData.append('image', blob, 'webcam-capture.jpg');
                    formData.append('caption', document.querySelector('textarea[name="caption"]').value || '');
                    formData.append('overlay', document.querySelector('input[name="overlay"]:checked')?.value || '');
                    const csrfToken = document.querySelector('input[name="csrf_token"]');
                    if (csrfToken) {
                        formData.append('csrf_token', csrfToken.value);
                    }

                    try {
                        const response = await fetch('create.php', {
                            method: 'POST',
                            body: formData
                        });

                        if (response.redirected) {
                            window.location.href = response.url;
                        } else {
                            location.reload();
                        }
                    } catch (err) {
                        console.error('Error uploading capture:', err);
                        alert('Error uploading photo. Please try again.');
                    }
                }, 'image/jpeg', 0.9);
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