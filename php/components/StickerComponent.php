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
		<div class="grid grid-cols-9 gap-2">
			<?php foreach (self::$stickers as $sticker): ?>
				<button
					type="button"
					class="sticker-btn aspect-square rounded-box bg-base-300 hover:bg-base-300/80 transition-all"
					data-sticker="<?= htmlspecialchars($sticker) ?>">
					<img
						src="assets/stickers/<?= htmlspecialchars($sticker) ?>"
						alt="<?= htmlspecialchars(pathinfo($sticker, PATHINFO_FILENAME)) ?>"
						class="w-full h-full object-contain p-1">
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