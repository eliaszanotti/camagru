class StickerComponent {
	constructor() {
		this.selectedSticker = null;
		this.init();
	}

	init() {
		this.setupEventListeners();
	}

	selectSticker(stickerName, buttonElement) {
		if (!window.currentImageData) {
			return;
		}

		if (this.selectedSticker === stickerName) {
			this.clearSelection();
			return;
		}

		this.selectedSticker = stickerName;

		document.querySelectorAll('.sticker-btn').forEach(btn => {
			btn.classList.remove('ring-2', 'ring-primary');
		});

		buttonElement.classList.add('ring-2', 'ring-primary');

		window.selectedSticker = stickerName;

		this.updatePreviewWithSticker();

		if (window.previewComponent) {
			window.previewComponent.onStickerSelected();
		}
	}

	updatePreviewWithSticker() {
		if (!window.currentImageData) {
			return;
		}

		const previewArea = document.getElementById('previewArea');

		if (!this.selectedSticker) {
			previewArea.innerHTML = `<img src="${window.currentImageData}" alt="Preview" class="w-full h-full object-cover rounded-box">`;
			return;
		}

		previewArea.innerHTML = `
			<div class="relative w-full h-full">
				<img src="${window.currentImageData}" alt="Preview" class="w-full h-full object-cover rounded-box">
				<img src="assets/stickers/${this.selectedSticker}" alt="Sticker" class="absolute bottom-0 right-0 w-1/3 h-1/3 object-contain pointer-events-none p-2">
			</div>
		`;
	}

	clearSelection() {
		this.selectedSticker = null;
		window.selectedSticker = null;

		document.querySelectorAll('.sticker-btn').forEach(btn => {
			btn.classList.remove('ring-2', 'ring-primary');
		});

		this.updatePreviewWithSticker();

		if (window.previewComponent) {
			window.previewComponent.onStickerDeselected();
		}
	}

	setupEventListeners() {
		document.querySelectorAll('.sticker-btn').forEach(btn => {
			btn.addEventListener('click', () => {
				const stickerName = btn.dataset.sticker;
				this.selectSticker(stickerName, btn);
			});
		});
	}
}

document.addEventListener('DOMContentLoaded', () => {
	window.stickerComponent = new StickerComponent();
});
