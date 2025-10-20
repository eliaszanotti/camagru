class ShareComponent {
	constructor() {
		this.container = document.getElementById('shareContent');
		this.imageData = null;

		this.init();
	}

	init() {
		this.loadImageData();
		if (this.imageData) {
			this.render();
		} else {
			this.renderNoImage();
		}
	}

	loadImageData() {
		try {
			const storedImage = sessionStorage.getItem('shareImage');
			if (storedImage) {
				this.imageData = JSON.parse(storedImage);
				sessionStorage.removeItem('shareImage'); // Clean up
			}
		} catch (error) {
			console.error('Error loading image data:', error);
		}
	}

	renderNoImage() {
		this.container.innerHTML = `
			<div class="text-center py-16">
				<h2 class="text-2xl font-semibold mb-4">No Image to Share</h2>
				<p class="text-base-content/50 mb-8">Please select an image from your history to share.</p>
				<a href="create.php" class="btn btn-primary">
					Back to Create
				</a>
			</div>
		`;
	}

	getShareFormHtml() {
		return `
			<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
				<!-- Image Preview -->
				<div class="space-y-4">
					<h2 class="text-xl font-semibold">Preview</h2>
					<div class="bg-base-200 rounded-box p-4">
						<img src="${this.imageData.data}" alt="Photo to share" class="w-full aspect-square object-cover rounded-box">
					</div>
				</div>

				<!-- Share Form -->
				<div class="space-y-4">
					<h2 class="text-xl font-semibold">Post Details</h2>
					<form id="shareForm" class="space-y-6">
						<fieldset class="fieldset w-full">
							<legend class="fieldset-legend">Caption</legend>
							<textarea name="caption" class="textarea w-full"
								placeholder="Add a caption to your photo..."
								rows="4"></textarea>
						</fieldset>

						<fieldset class="fieldset">
							<label class="cursor-pointer label">
								<input type="checkbox" name="is_published" class="checkbox checkbox-primary" checked>
								<span class="label-text">Publish to gallery</span>
							</label>
						</fieldset>

						<input type="hidden" name="image_data" value="${this.imageData.data}">
						<input type="hidden" name="image_id" value="${this.imageData.id}">
						<input type="hidden" name="source" value="${this.imageData.source}">

						<div class="flex gap-4">
							<button type="submit" class="btn btn-primary flex-1" id="shareBtn">
								Share Photo
							</button>
							<a href="create.php" class="btn btn-ghost">
								Cancel
							</a>
						</div>
					</form>
				</div>
			</div>
		`;
	}

	render() {
		this.container.innerHTML = this.getShareFormHtml();
		this.setupEventListeners();
	}

	setupEventListeners() {
		const form = document.getElementById('shareForm');
		const shareBtn = document.getElementById('shareBtn');

		if (form) {
			form.addEventListener('submit', (e) => {
				e.preventDefault();
				this.handleShare();
			});
		}
	}

	async handleShare() {
		const shareBtn = document.getElementById('shareBtn');
		const originalText = shareBtn.innerHTML;

		// Show loading state
		shareBtn.disabled = true;
		shareBtn.innerHTML = '<span class="loading loading-spinner"></span> Sharing...';

		try {
			const formData = new FormData(document.getElementById('shareForm'));

			const response = await fetch('handlers/ShareHandler.php', {
				method: 'POST',
				body: formData
			});

			const result = await response.json();

			if (result.success) {
				// Show success message and redirect
				window.location.href = 'gallery.php';
			} else {
				// Show error message
				this.showError(result.error || 'Failed to share photo');
			}
		} catch (error) {
			console.error('Error sharing photo:', error);
			this.showError('Network error occurred. Please try again.');
		} finally {
			// Restore button state
			shareBtn.disabled = false;
			shareBtn.innerHTML = originalText;
		}
	}

	showError(message) {
		// Create or update error alert
		let alertContainer = document.getElementById('shareAlerts');
		if (!alertContainer) {
			alertContainer = document.createElement('div');
			alertContainer.id = 'shareAlerts';
			alertContainer.className = 'alert alert-error mb-4';
			this.container.prepend(alertContainer);
		}

		alertContainer.className = 'alert alert-error mb-4';
		alertContainer.innerHTML = `
			<svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
			</svg>
			<span>${message}</span>
		`;

		// Auto-hide after 5 seconds
		setTimeout(() => {
			if (alertContainer && alertContainer.parentNode) {
				alertContainer.parentNode.removeChild(alertContainer);
			}
		}, 5000);
	}
}

document.addEventListener('DOMContentLoaded', () => {
	new ShareComponent();
});