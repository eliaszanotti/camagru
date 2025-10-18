<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/handlers/LoginHandler.php';
require_once __DIR__ . '/components/Fieldset.php';

$loginHandler = new LoginHandler();
$formService = $loginHandler->getFormService();

if ($loginHandler->shouldRedirect()) {
    header('Location: ' . $loginHandler->getRedirectUrl());
    exit;
}

$pageTitle = 'Login - Camagru';
?>
<div class="p-16">
    <div class="max-w-md mx-auto">
        <div class="card bg-base-200">
            <div class="card-body space-y-4">
                <h1 class="card-title">Login</h1>
                <?php if ($formService->getError('general')): ?>
                    <div class="alert alert-error">
                        <!-- TODO mettre lucide -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span><?php echo htmlspecialchars($formService->getError('general')); ?></span>
                    </div>
                <?php endif; ?>
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