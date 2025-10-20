class BrowseComponent {
	constructor() {
		this.fileInput = document.getElementById("fileInput");
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
			};
			reader.readAsDataURL(file);
		}
	}

	updatePreview(imageData) {
		if (window.updatePreview) {
			window.updatePreview(imageData, "file");
		}
	}

	setupEventListeners() {
		this.fileInput.addEventListener("change", (e) => {
			this.handleFileSelect(e);
		});
	}
}

document.addEventListener('DOMContentLoaded', () => {
	window.browseComponent = new BrowseComponent();
});