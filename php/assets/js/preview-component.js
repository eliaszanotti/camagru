class PreviewComponent {
	constructor() {
		this.previewArea = document.getElementById('previewArea');
		this.discardBtn = document.getElementById('discardBtn');
		this.saveBtn = document.getElementById('saveBtn');

		this.init();
	}

	init() {
		this.setupEventListeners();
	}

	discardPreview() {
		this.previewArea.innerHTML = `<p class="text-center">Preview will appear here</p>`;
		this.discardBtn.disabled = true;
		this.saveBtn.disabled = true;

		window.currentImageData = null;
		window.currentImageSource = null;
	}

	updatePreview(imageData, source) {
		this.previewArea.innerHTML = `<img src="${imageData}" alt="Preview" class="w-full h-full object-cover rounded-box">`;
		this.saveBtn.disabled = false;
		this.discardBtn.disabled = false;

		window.currentImageData = imageData;
		window.currentImageSource = source;
	}

	setupEventListeners() {
		this.discardBtn.addEventListener('click', () => {
			this.discardPreview();
		});
	}

	makeGloballyAvailable() {
		window.updatePreview = (imageData, source) => {
			this.updatePreview(imageData, source);
		};

		window.discardPreview = () => {
			this.discardPreview();
		};
	}
}

document.addEventListener('DOMContentLoaded', () => {
	window.previewComponent = new PreviewComponent();
	window.previewComponent.makeGloballyAvailable();
});