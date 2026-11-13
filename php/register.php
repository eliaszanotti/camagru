<?php
require_once __DIR__ . '/handlers/RegisterHandler.php';

$registerHandler = new RegisterHandler();
$formService = $registerHandler->getFormService();

if ($registerHandler->shouldClearForm()) {
    $_POST = [];
}

$pageTitle = 'Register - Camagru';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/components/Fieldset.php';
?>
<div class="max-w-md mx-auto">
    <div class="card bg-base-200">
        <div class="card-body space-y-4">
            <h1 class="card-title">Register</h1>
            <?php if ($formService->hasErrors()): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        window.showToast(<?= json_encode(implode("\n", $formService->getErrors()), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'error');
                    });
                </script>
            <?php elseif ($formService->hasSuccess()): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        window.showToast(<?= json_encode($formService->getSuccess(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'success');
                    });
                </script>
            <?php endif; ?>
            <form method="POST" novalidate>
                <?php
                Fieldset::csrfToken();
                Fieldset::username();
                Fieldset::email();
                Fieldset::password();
                Fieldset::confirmPassword();
                ?>
                <div class="form-control mt-4">
                    <?php Fieldset::submit('Create Account'); ?>
                </div>
            </form>
            <div class="divider">OR</div>
            <p class="text-center">
                Already have an account?
                <a href="login.php" class="link link-secondary">Sign in</a>
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
