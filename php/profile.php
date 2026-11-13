<?php
require_once __DIR__ . '/handlers/ProfileHandler.php';

$profileHandler = new ProfileHandler();
$user = $profileHandler->getUser();
$userPosts = $profileHandler->getUserPosts();
$formService = $profileHandler->getFormService();

require_once __DIR__ . '/includes/header.php';
$pageTitle = 'Profile - Camagru';
?>

<div class="max-w-4xl mx-auto space-y-8">
    <h1 class="text-3xl font-bold">My Profile</h1>
    <?php if ($formService->hasErrors()): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                window.showToast(<?= json_encode(implode(' ', $formService->getErrors()), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'error');
            });
        </script>
    <?php elseif ($formService->hasSuccess()): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                window.showToast(<?= json_encode($formService->getSuccess(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>);
            });
        </script>
    <?php endif; ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="space-y-8">
            <?php require_once __DIR__ . '/includes/profile/account-info-card.php'; ?>
            <?php require_once __DIR__ . '/includes/profile/username-card.php'; ?>
            <?php require_once __DIR__ . '/includes/profile/email-card.php'; ?>
        </div>
        <div class="space-y-8">
            <?php require_once __DIR__ . '/includes/profile/password-card.php'; ?>
            <?php require_once __DIR__ . '/includes/profile/notifications-card.php'; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
