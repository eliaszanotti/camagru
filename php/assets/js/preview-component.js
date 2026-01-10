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

		if (window.stickerComponent) {
			window.stickerComponent.clearSelection();
		}
	}

	updatePreview(imageData) {
		this.previewArea.innerHTML = `<img src="${imageData}" alt="Preview" class="w-full h-full object-cover rounded-box">`;
		this.discardBtn.disabled = false;

		window.currentImageData = imageData;
		this.checkSaveButtonState();
	}

	checkSaveButtonState() {
		this.saveBtn.disabled = !window.currentImageData || !window.selectedSticker;
	}

	onStickerSelected() {
		this.checkSaveButtonState();
	}

	onStickerDeselected() {
		this.checkSaveButtonState();
	}

	saveToHistory() {
		if (!window.currentImageData) {
			alert('No image to save');
			return;
		}

		const form = document.createElement('form');
		form.method = 'POST';
		form.action = 'handlers/PostHandler.php';
		form.style.display = 'none';

		const imageInput = document.createElement('input');
		imageInput.type = 'hidden';
		imageInput.name = 'image_data';
		imageInput.value = window.currentImageData;
		form.appendChild(imageInput);

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

		if (window.selectedSticker) {
			const stickerInput = document.createElement('input');
			stickerInput.type = 'hidden';
			stickerInput.name = 'sticker';
			stickerInput.value = window.selectedSticker;
			form.appendChild(stickerInput);
		}

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