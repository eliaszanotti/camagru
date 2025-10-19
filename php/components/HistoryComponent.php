<?php
class HistoryComponent {
    public static function render(): void {
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

    public static function renderScripts(): void {
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
                    item.className = 'relative group cursor-pointer';
                    item.innerHTML = `
                        <img src="${image.data}" alt="History image ${index + 1}"
                             class="w-full h-24 object-cover rounded-lg hover:opacity-80 transition-opacity">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-opacity rounded-lg flex items-center justify-center">
                            <button onclick="selectFromHistory('${image.id}')"
                                    class="opacity-0 group-hover:opacity-100 transition-opacity btn btn-xs btn-primary">
                                Select
                            </button>
                        </div>
                        <div class="absolute top-1 right-1">
                            <button onclick="deleteFromHistory('${image.id}')"
                                    class="btn btn-xs btn-circle btn-error opacity-0 group-hover:opacity-100 transition-opacity">
                                ×
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

            // Make functions globally available
            window.saveToHistory = saveToHistory;
            window.deleteFromHistory = deleteFromHistory;
            window.selectFromHistory = selectFromHistory;

            // Initial render
            renderHistory();
        </script>
        <?php
    }
}
?>