<?php
class StickerComponent
{
	private static array $stickers = [
		'beaming-face-with-smiling-eyes.png',
		'crying-face.png',
		'exploding-head.png',
		'face-blowing-a-kiss.png',
		'nerd-face.png',
		'neutral-face.png',
		'smiling-face-with-heart-eyes.png',
		'winking-face.png',
		'winking-face-with-tongue.png',
	];

	public static function render(): void
	{
?>
		<div class="grid grid-cols-1 justify-items-center gap-2">
			<?php foreach (self::$stickers as $sticker): ?>
				<button
					type="button"
					class="sticker-btn flex h-16 w-16 cursor-pointer items-center justify-center rounded-box bg-base-300 transition-all hover:bg-base-300/80 disabled:cursor-not-allowed"
					disabled
					aria-pressed="false"
					aria-label="Add <?= htmlspecialchars(str_replace('-', ' ', pathinfo($sticker, PATHINFO_FILENAME))) ?> sticker"
					data-sticker="<?= htmlspecialchars($sticker) ?>">
					<img
						src="assets/stickers/<?= htmlspecialchars($sticker) ?>"
						alt="<?= htmlspecialchars(pathinfo($sticker, PATHINFO_FILENAME)) ?>"
						class="h-12 w-12 object-contain">
				</button>
			<?php endforeach; ?>
		</div>
	<?php
	}

	public static function renderScripts(): void
	{
	?>
		<script src="assets/js/sticker-component.js"></script>
<?php
	}
}
?>
