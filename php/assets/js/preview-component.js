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
	}

	updatePreview(imageData) {
		this.previewArea.innerHTML = `<img src="${imageData}" alt="Preview" class="w-full h-full object-cover rounded-box">`;
		this.saveBtn.disabled = false;
		this.discardBtn.disabled = false;

		window.currentImageData = imageData;
	}

	saveToHistory() {
		if (!window.currentImageData) {
			alert('No image to save');
			return;
		}

		// Create a hidden form
		const form = document.createElement('form');
		form.method = 'POST';
		form.action = 'handlers/PostHandler.php';
		form.style.display = 'none';

		// Add image data as hidden field
		const imageInput = document.createElement('input');
		imageInput.type = 'hidden';
		imageInput.name = 'image_data';
		imageInput.value = window.currentImageData;
		form.appendChild(imageInput);

		// Add caption and publish status
		const captionInput = document.createElement('input');
		captionInput.type = 'hidden';
		captionInput.name = 'caption';
		captionInput.value = '';
		form.appendChild(captionInput);

		const publishedInput = document.createElement('input');
		publishedInput.type = 'hidden';
		publishedInput.name = 'is_published';
		publishedInput.value = '0';
		form.appendChild(publishedInput);

		// Submit form
		document.body.appendChild(form);
		form.submit();
	}

	setupEventListeners() {
		this.discardBtn.addEventListener('click', () => {
			this.discardPreview();
		});

		// Handle form submission
		const savePostForm = document.getElementById('savePostForm');
		if (savePostForm) {
			savePostForm.addEventListener('submit', (e) => {
				e.preventDefault();
				this.saveToHistory();
			});
		}
	}

	makeGloballyAvailable() {
		window.updatePreview = (imageData) => {
			this.updatePreview(imageData);
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