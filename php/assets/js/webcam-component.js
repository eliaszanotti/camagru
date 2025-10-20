class WebcamComponent {
	constructor() {
		this.video = document.getElementById('webcam');
		this.canvas = document.getElementById('canvas');
		this.captureBtn = document.getElementById('captureBtn');
		this.stream = null;

		this.init();
	}

	init() {
		this.setupEventListeners();
		this.startWebcam();
	}

	async startWebcam() {
		try {
			this.stream = await navigator.mediaDevices.getUserMedia({
				video: {
					width: { ideal: 640 },
					height: { ideal: 640 }
				}
			});
			this.video.srcObject = this.stream;
		} catch (err) {
			console.error('Error accessing webcam:', err);
			this.handleWebcamError();
		}
	}

	handleWebcamError() {
		const webcamContainer = this.video.parentElement;
		webcamContainer.innerHTML = `
			<div class="w-full h-full bg-base-200 rounded-box flex items-center justify-center">
				<div class="text-center text-base-content/50">
					<div class="text-4xl mb-2">📷</div>
					<p class="text-sm">Webcam not available</p>
				</div>
			</div>
		`;
		this.captureBtn.disabled = true;
	}

	captureImage() {
		const context = this.canvas.getContext('2d');
		this.canvas.width = this.video.videoWidth;
		this.canvas.height = this.video.videoHeight;
		context.drawImage(this.video, 0, 0);

		const imageData = this.canvas.toDataURL('image/jpeg', 0.9);
		this.updatePreview(imageData);
	}

	updatePreview(imageData) {
		if (window.updatePreview) {
			window.updatePreview(imageData, 'webcam');
		}
	}

	setupEventListeners() {
		this.captureBtn.addEventListener('click', () => {
			this.captureImage();
		});

		window.addEventListener('beforeunload', () => {
			this.cleanup();
		});
	}

	cleanup() {
		if (this.stream) {
			this.stream.getTracks().forEach(track => track.stop());
		}
	}
}

document.addEventListener('DOMContentLoaded', () => {
	window.webcamComponent = new WebcamComponent();
});