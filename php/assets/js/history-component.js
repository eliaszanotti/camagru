class HistoryComponent {
    constructor() {
        this.storageKey = 'camagru_history';
        this.container = document.getElementById('historyContainer');
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
            return JSON.parse(localStorage.getItem(this.storageKey) || '[]');
        } catch (error) {
            console.error('Error loading history from storage:', error);
            return [];
        }
    }

    saveToStorage() {
        try {
            localStorage.setItem(this.storageKey, JSON.stringify(this.imageHistory));
        } catch (error) {
            console.error('Error saving history to storage:', error);
        }
    }

    render() {
        if (this.imageHistory.length === 0) {
            this.container.innerHTML = `
                <div class="text-center py-8 text-base-content/50">
                    <div class="text-4xl mb-2">📚</div>
                    <p>No images saved yet</p>
                </div>
            `;
            return;
        }

        let itemsHtml = '';

        this.imageHistory.slice().reverse().forEach((image, index) => {
            itemsHtml += `
                <div class="flex flex-col gap-2">
                    <img src="${image.data}" alt="History image ${index + 1}"
                         class="w-full aspect-square object-cover rounded-lg">
                    <div class="flex gap-2">
                        <button data-action="share" data-image-id="${image.id}"
                                class="btn btn-primary flex-1">
                            Share
                        </button>
                        <button data-action="delete" data-image-id="${image.id}"
                                class="btn btn-error flex-1">
                            Delete
                        </button>
                    </div>
                </div>
            `;
        });

        this.container.innerHTML = `
            <div class="grid grid-cols-6 gap-2">
                ${itemsHtml}
            </div>
        `;
    }

    addImage(imageData, source) {
        const imageEntry = {
            id: Date.now().toString(),
            data: imageData,
            source: source,
            timestamp: new Date().toISOString()
        };

        this.imageHistory.push(imageEntry);
        this.saveToStorage();
        this.render();
    }

    deleteImage(imageId) {
        this.imageHistory = this.imageHistory.filter(img => img.id !== imageId);
        this.saveToStorage();
        this.render();
    }

    shareImage(imageId) {
        const image = this.imageHistory.find(img => img.id === imageId);
        if (image) {
            const link = document.createElement('a');
            link.download = `camagru-${imageId}.png`;
            link.href = image.data;
            link.click();
        }
    }

    setupSaveButton() {
        const saveBtn = document.getElementById('saveBtn');
        if (saveBtn) {
            saveBtn.addEventListener('click', () => {
                if (window.currentImageData && window.currentImageSource) {
                    this.addImage(window.currentImageData, window.currentImageSource);
                }
            });
        }
    }

    setupEventListeners() {
        this.container.addEventListener('click', (e) => {
            const button = e.target.closest('button[data-action]');
            if (!button) return;

            const action = button.dataset.action;
            const imageId = button.dataset.imageId;

            switch (action) {
                case 'share':
                    this.shareImage(imageId);
                    break;
                case 'delete':
                    this.deleteImage(imageId);
                    break;
            }
        });
    }
}

// Initialize the component when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.historyComponent = new HistoryComponent();
});

// Expose method globally for other components
window.saveToHistory = (imageData, source) => {
    if (window.historyComponent) {
        window.historyComponent.addImage(imageData, source);
    }
};