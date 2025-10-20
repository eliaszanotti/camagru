class BrowseComponent {
	constructor() {
		console.log("All file inputs on page:", document.querySelectorAll('input[type="file"]'));
		this.fileInput = document.getElementById("fileInput");
		console.log("BrowseComponent: fileInput found:", !!this.fileInput);
		this.init();
	}

	init() {
		this.setupEventListeners();
	}

	handleFileSelect(e) {
		console.log("handleFileSelect called");
		const file = e.target.files[0];
		console.log("File selected:", file);
		if (file) {
			const reader = new FileReader();
			reader.onload = (e) => {
				console.log("FileReader loaded, image data length:", e.target.result.length);
				this.updatePreview(e.target.result);
			};
			reader.readAsDataURL(file);
		}
	}


	updatePreview(imageData) {
		console.log("updatePreview called, window.updatePreview exists:", !!window.updatePreview);
		if (window.updatePreview) {
			console.log("Calling window.updatePreview with image data");
			window.updatePreview(imageData, "file");
		} else {
			console.error("window.updatePreview is not defined!");
		}
	}

	setupEventListeners() {
		console.log("Setting up event listeners");
		this.fileInput.addEventListener("change", (e) => {
			console.log("File input change event triggered");
			this.handleFileSelect(e);
		});
	}
}

document.addEventListener('DOMContentLoaded', () => {
	window.browseComponent = new BrowseComponent();
});