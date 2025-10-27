class HistoryComponent {
	constructor() {
		this.storageKey = "camagru_history";
		this.container = document.getElementById("historyContainer");
		this.imageHistory = this.loadFromStorage();

		this.init();
	}

	init() {
		this.render();
		this.setupSaveButton();
		this.setupEventListeners();
	}

	loadFromStorage() {
		try {
			return JSON.parse(localStorage.getItem(this.storageKey) || "[]");
		} catch (error) {
			console.error("Error loading history from storage:", error);
			return [];
		}
	}

	saveToStorage() {
		try {
			localStorage.setItem(
				this.storageKey,
				JSON.stringify(this.imageHistory)
			);
		} catch (error) {
			console.error("Error saving history to storage:", error);
		}
	}

	getEmptyStateHtml() {
		return `<div class="text-center text-base-content/50">
			<div></div>
			<p>No images saved yet</p>
		</div>`;
	}

	getHistoryItemHtml(image, index) {
		return `<div class="flex flex-col gap-2">
			<img src="${image.data}" alt="History image ${index + 1}"
				 class="w-full aspect-square object-cover rounded-box">
			<div class="grid grid-cols-2 gap-2">
				<button data-action="delete" data-image-id="${image.id}"
						class="btn btn-error">
					Delete
				</button>
				<button data-action="share" data-image-id="${image.id}"
						class="btn btn-primary">
					Share
				</button>
			</div>
		</div>`;
	}

	getHistoryGridHtml() {
		let itemsHtml = "";

		this.imageHistory
			.slice()
			.reverse()
			.forEach((image, index) => {
				itemsHtml += this.getHistoryItemHtml(image, index + 1);
			});

		return `<div class="grid grid-cols-6 gap-2">
			${itemsHtml}
		</div>`;
	}

	render() {
		if (this.imageHistory.length === 0) {
			this.container.innerHTML = this.getEmptyStateHtml();
			return;
		}

		this.container.innerHTML = this.getHistoryGridHtml();
	}

	addImage(imageData, source) {
		const imageEntry = {
			id: Date.now().toString(),
			data: imageData,
			source: source,
			timestamp: new Date().toISOString(),
		};

		this.imageHistory.push(imageEntry);
		this.saveToStorage();
		this.render();
	}

	deleteImage(imageId) {
		this.imageHistory = this.imageHistory.filter(
			(img) => img.id !== imageId
		);
		this.saveToStorage();
		this.render();
	}

	shareImage(imageId) {
		const image = this.imageHistory.find((img) => img.id === imageId);
		if (image) {
			sessionStorage.setItem(
				"shareImage",
				JSON.stringify({
					id: image.id,
					data: image.data,
					source: image.source,
					timestamp: image.timestamp,
				})
			);
			window.location.href = "share.php";
		}
	}

	setupSaveButton() {
		const saveBtn = document.getElementById("saveBtn");
		if (saveBtn) {
			saveBtn.addEventListener("click", () => {
				if (window.currentImageData && window.currentImageSource) {
					this.addImage(
						window.currentImageData,
						window.currentImageSource
					);
				}
			});
		}
	}

	setupEventListeners() {
		this.container.addEventListener("click", (e) => {
			const button = e.target.closest("button[data-action]");
			if (!button) return;

			const action = button.dataset.action;
			const imageId = button.dataset.imageId;

			switch (action) {
				case "share":
					this.shareImage(imageId);
					break;
				case "delete":
					this.deleteImage(imageId);
					break;
			}
		});
	}
}

document.addEventListener("DOMContentLoaded", () => {
	window.historyComponent = new HistoryComponent();
});

window.saveToHistory = (imageData, source) => {
	if (window.historyComponent) {
		window.historyComponent.addImage(imageData, source);
	}
};
