<?php
require_once __DIR__ . '/handlers/LoginHandler.php';
require_once __DIR__ . '/components/Fieldset.php';

$loginHandler = new LoginHandler();
$formService = $loginHandler->getFormService();

if ($loginHandler->shouldRedirect()) {
    header('Location: ' . $loginHandler->getRedirectUrl());
    exit;
}

require_once __DIR__ . '/includes/header.php';

$pageTitle = 'Login - Camagru';
?>
<div class="max-w-md mx-auto">
    <div class="card bg-base-200">
        <div class="card-body space-y-4">
            <h1 class="card-title">Login</h1>
            <?php if ($formService->hasErrors()): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        window.showToast(<?= json_encode(implode("\n", $formService->getErrors()), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'error');
                    });
                </script>
            <?php endif; ?>
            <?php if ($formService->hasSuccess()): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        window.showToast(<?= json_encode($formService->getSuccess(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'success');
                    });
                </script>
            <?php endif; ?>
            <form method="POST" novalidate>
                <?php
                Fieldset::csrfToken();
                Fieldset::formType('login');
                Fieldset::loginIdentifier();
                Fieldset::password();
                ?>
                <div class="form-control mt-4">
                    <?php Fieldset::submit('Sign In'); ?>
                </div>
            </form>
            <?php if ($loginHandler->shouldShowResendVerification()): ?>
                <form method="POST" class="text-center">
                    <?php
                    Fieldset::csrfToken();
                    Fieldset::formType('resend_verification');
                    ?>
                    <button type="submit" class="link link-primary">Resend verification email</button>
                </form>
            <?php endif; ?>
            <div class="divider">OR</div>
            <div class="space-y-2 text-center">
                <p>
                    Don't have an account?
                    <a href="register.php" class="link link-primary">Create one</a>
                </p>
                <a href="forgot-password.php" class="link link-primary">Forgot your password?</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
