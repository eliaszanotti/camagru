<?php
class HistoryComponent
{
    public static function render(): void
    {
?>
        <div class="card bg-base-200">
            <div class="card-body">
                <h2 class="card-title">History</h2>
                <div id="historyContainer"></div>
            </div>
        </div>
    <?php
    }

    public static function renderScripts(): void
    {
    ?>
        <script src="assets/js/history-component.js"></script>
<?php
    }
}
?>