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
		this.previewArea.innerHTML = `<div class="max-w-xs px-6 text-center"><span class="mb-3 block text-4xl" aria-hidden="true">▧</span><p class="font-medium">Your photo will appear here</p><p class="mt-1 text-sm text-base-content/60">Upload an image or take one with your camera.</p></div>`;
		this.discardBtn.disabled = true;
		this.saveBtn.disabled = true;
		this.setStickerAvailability(false);

		window.currentImageData = null;

		if (window.stickerComponent) {
			window.stickerComponent.clearSelection();
		}
	}

	updatePreview(imageData) {
		if (window.stickerComponent) {
			window.stickerComponent.clearSelection();
		}
		this.previewArea.innerHTML = `<img src="${imageData}" alt="Preview" class="w-full h-full object-cover rounded-box">`;
		this.discardBtn.disabled = false;
		this.setStickerAvailability(true);

		window.currentImageData = imageData;
		this.checkSaveButtonState();
	}

	setStickerAvailability(isAvailable) {
		document.querySelectorAll('.sticker-btn').forEach((button) => {
			button.disabled = !isAvailable;
		});
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

		const csrfInput = document.createElement('input');
		csrfInput.type = 'hidden';
		csrfInput.name = 'csrf_token';
		csrfInput.value = document.querySelector('#savePostForm input[name="csrf_token"]')?.value || '';
		form.appendChild(csrfInput);

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
