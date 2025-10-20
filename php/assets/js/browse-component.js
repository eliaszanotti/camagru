class BrowseComponent {
	constructor() {
		this.fileInput = document.getElementById('fileInput');
		this.browseArea = document.getElementById('browseArea');

		this.init();
	}

	init() {
		this.setupEventListeners();
	}

	handleFileSelect(e) {
		const file = e.target.files[0];
		if (file) {
			const reader = new FileReader();
			reader.onload = (e) => {
				this.updatePreview(e.target.result);
				this.updateBrowseArea(e.target.result);
			};
			reader.readAsDataURL(file);
		}
	}

	updateBrowseArea(imageData) {
		this.browseArea.innerHTML = `
			<img src="${imageData}" alt="Selected image"
				 class="w-full h-full object-cover rounded-box">
		`;
	}

	updatePreview(imageData) {
		if (window.updatePreview) {
			window.updatePreview(imageData, 'file');
		}
	}

	setupEventListeners() {
		this.browseArea.addEventListener('click', () => {
			this.fileInput.click();
		});

		this.fileInput.addEventListener('change', (e) => {
			this.handleFileSelect(e);
		});
	}
}

document.addEventListener('DOMContentLoaded', () => {
	window.browseComponent = new BrowseComponent();
});