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
<div class="p-16">
    <div class="max-w-md mx-auto">
        <div class="card bg-base-200">
            <div class="card-body space-y-4">
                <h1 class="card-title">Login</h1>
                <?php require_once __DIR__ . '/includes/alerts.php'; ?>
                <form method="POST" novalidate>
                    <?php
                    Fieldset::csrfToken();
                    Fieldset::loginIdentifier();
                    Fieldset::password();
                    ?>
                    <div class="form-control mt-4">
                        <?php Fieldset::submit('Sign In'); ?>
                    </div>
                </form>
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
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>