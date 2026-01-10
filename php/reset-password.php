<?php
require_once __DIR__ . '/handlers/ResetPasswordHandler.php';
require_once __DIR__ . '/components/Fieldset.php';

$token = $_GET['token'] ?? '';
$resetPasswordHandler = new ResetPasswordHandler($token);
$formService = $resetPasswordHandler->getFormService();

if ($resetPasswordHandler->shouldRedirect()) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/includes/header.php';

$pageTitle = 'Reset Password - Camagru';
?>
<div class="max-w-md mx-auto">
    <div class="card bg-base-200">
        <div class="card-body space-y-4">
            <h1 class="card-title">Reset Password</h1>
            <?php require_once __DIR__ . '/includes/alerts.php'; ?>

            <?php if ($resetPasswordHandler->isTokenValid()): ?>
                <form method="POST" novalidate>
                    <?php
                    Fieldset::csrfToken();
                    Fieldset::password();
                    Fieldset::confirmPassword();
                    ?>
                    <div class="form-control mt-4">
                        <?php Fieldset::submit('Reset Password'); ?>
                    </div>
                </form>
                <div class="text-center">
                    <a href="login.php" class="link link-primary">Back to Login</a>
                </div>
            <?php else: ?>
                <div class="text-center">
                    <a href="forgot-password.php" class="btn btn-primary">Request New Reset Link</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
