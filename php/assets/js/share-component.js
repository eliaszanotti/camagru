class ShareComponent {
	constructor() {
		this.container = document.getElementById("shareContent");
		this.imageData = null;
		this.alertContainer = null;

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
		const urlParams = new URLSearchParams(window.location.search);
		const imageId = urlParams.get("image");

		if (!imageId) {
			return;
		}

		const imageHistory = JSON.parse(
			localStorage.getItem("camagru_history") || "[]"
		);
		const image = imageHistory.find((img) => img.id === imageId);

		if (image) {
			this.imageData = {
				id: image.id,
				data: image.data,
				source: image.source,
				timestamp: image.timestamp,
			};
		}
	}

	renderNoImage() {
		this.container.innerHTML = this.getEmptyStateHtml();
	}

	getEmptyStateHtml() {
		return `
			<div class='text-center py-16'>
				<h2 class='text-2xl font-semibold mb-4'>No Image to Share</h2>
				<p class='text-base-content/50 mb-8'>Please select an image from your history to share.</p>
				<a href='create.php' class='btn btn-primary'>
					Back to Create
				</a>
			</div>
		`;
	}

	getShareFormHtml() {
		return `
			<div class='grid grid-cols-1 lg:grid-cols-2 gap-8'>
				<div class='space-y-4'>
					<h2 class='text-xl font-semibold'>Preview</h2>
					<div class='bg-base-200 rounded-box p-4'>
						<img src='${this.imageData.data}' alt='Photo to share' class='w-full aspect-square object-cover rounded-box'>
					</div>
				</div>

				<div class='space-y-4'>
					<h2 class='text-xl font-semibold'>Post Details</h2>
					<form id='shareForm' class='space-y-6'>
						<fieldset class='fieldset w-full'>
							<legend class='fieldset-legend'>Caption</legend>
							<textarea name='caption' class='textarea w-full'
								placeholder='Add a caption to your photo...'
								rows='4'></textarea>
						</fieldset>

						<fieldset class='fieldset'>
							<label class='cursor-pointer label'>
								<input type='checkbox' name='is_published' class='checkbox checkbox-primary' checked>
								<span class='label-text'>Publish to public feed</span>
							</label>
						</fieldset>

						<input type='hidden' name='image_data' value='${this.imageData.data}'>
						<input type='hidden' name='image_id' value='${this.imageData.id}'>
						<input type='hidden' name='source' value='${this.imageData.source}'>

						<div id='shareAlerts'></div>

						<div class='flex gap-4'>
							<button type='submit' class='btn btn-primary flex-1' id='shareBtn'>
								Share Photo
							</button>
							<a href='create.php' class='btn btn-ghost'>
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
		const form = document.getElementById("shareForm");

		if (form) {
			form.addEventListener("submit", (e) => {
				e.preventDefault();
				this.handleShare();
			});
		}
	}

	async handleShare() {
		const shareBtn = document.getElementById("shareBtn");
		const originalText = shareBtn.innerHTML;

		shareBtn.disabled = true;
		shareBtn.innerHTML =
			'<span class="loading loading-spinner"></span> Sharing...';

		try {
			const formData = new FormData(document.getElementById("shareForm"));

			const response = await fetch("handlers/ShareHandler.php", {
				method: "POST",
				body: formData,
			});

			const result = await response.json();

			if (result.success) {
				window.location.href = "index.php";
			} else {
				this.showError(result.error || "Failed to share photo");
			}
		} catch (error) {
			this.showError("Network error occurred. Please try again.");
		} finally {
			shareBtn.disabled = false;
			shareBtn.innerHTML = originalText;
		}
	}

	showError(message) {
		const alertContainer = document.getElementById("shareAlerts");
		if (alertContainer) {
			alertContainer.innerHTML = this.getErrorAlertHtml(message);
		}
	}

	getErrorAlertHtml(message) {
		return `
			<div class='alert alert-error mb-4'>
				<svg xmlns='http://www.w3.org/2000/svg' class='stroke-current shrink-0 h-6 w-6' fill='none' viewBox='0 0 24 24'>
					<path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' />
				</svg>
				<span>${message}</span>
			</div>
		`;
	}
}

document.addEventListener("DOMContentLoaded", () => {
	new ShareComponent();
});
