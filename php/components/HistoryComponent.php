<?php
class HistoryComponent
{
    public static function render(): void
    {
?>
        <div class="card bg-base-200">
            <div class="card-body">
                <h2 class="card-title">History</h2>
                <div id="historyContainer">
                    <div class="text-center py-8 text-base-content/50">
                        <div class="text-4xl mb-2">📚</div>
                        <p>No images saved yet</p>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }

    public static function renderScripts(): void
    {
    ?>
        <script>
            let imageHistory = JSON.parse(localStorage.getItem('camagru_history') || '[]');

            function renderHistory() {
                const container = document.getElementById('historyContainer');

                if (imageHistory.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-8 text-base-content/50">
                            <div class="text-4xl mb-2">📚</div>
                            <p>No images saved yet</p>
                        </div>
                    `;
                    return;
                }

                const grid = document.createElement('div');
                grid.className = 'grid grid-cols-3 gap-2';

                imageHistory.slice().reverse().forEach((image, index) => {
                    const item = document.createElement('div');
                    item.className = 'flex flex-col gap-2';
                    item.innerHTML = `
                        <img src="${image.data}" alt="History image ${index + 1}"
                             class="w-full aspect-square object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity"
                             onclick="selectFromHistory('${image.id}')">
                        <div class="flex gap-2">
                            <button onclick="shareImage('${image.id}')"
                                    class="btn btn-primary flex-1">
                                Share
                            </button>
                            <button onclick="deleteFromHistory('${image.id}')"
                                    class="btn btn-error flex-1">
                                Delete
                            </button>
                        </div>
                    `;
                    grid.appendChild(item);
                });

                container.innerHTML = '';
                container.appendChild(grid);
            }

            function saveToHistory(imageData, source) {
                const imageId = Date.now().toString();
                const imageEntry = {
                    id: imageId,
                    data: imageData,
                    source: source,
                    timestamp: new Date().toISOString()
                };

                imageHistory.push(imageEntry);
                localStorage.setItem('camagru_history', JSON.stringify(imageHistory));
                renderHistory();
            }

            function deleteFromHistory(imageId) {
                imageHistory = imageHistory.filter(img => img.id !== imageId);
                localStorage.setItem('camagru_history', JSON.stringify(imageHistory));
                renderHistory();
            }

            function selectFromHistory(imageId) {
                const image = imageHistory.find(img => img.id === imageId);
                if (image) {
                    updatePreview(image.data, 'history');
                }
            }

            function shareImage(imageId) {
                const image = imageHistory.find(img => img.id === imageId);
                if (image) {
                    // Create a temporary link to download the image
                    const link = document.createElement('a');
                    link.download = `camagru-${imageId}.png`;
                    link.href = image.data;
                    link.click();
                }
            }

            // Make functions globally available
            window.saveToHistory = saveToHistory;
            window.deleteFromHistory = deleteFromHistory;
            window.selectFromHistory = selectFromHistory;
            window.shareImage = shareImage;

            // Initialize save button event listener after all functions are available
            const saveBtn = document.getElementById('saveBtn');
            if (saveBtn) {
                saveBtn.addEventListener('click', function() {
                    if (window.currentImageData && window.currentImageSource) {
                        saveToHistory(window.currentImageData, window.currentImageSource);

                        // Show success feedback
                        this.textContent = '✅ Saved!';
                        this.classList.remove('btn-success');
                        this.classList.add('btn-success', 'btn-disabled');

                        setTimeout(() => {
                            this.textContent = '💾 Save to History';
                            this.classList.remove('btn-disabled');
                            // Reset preview after saving
                            discardPreview();
                        }, 1500);
                    }
                });
            }

            // Initial render
            renderHistory();
        </script>
<?php
    }
}
?>