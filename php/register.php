<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/handlers/RegisterHandler.php';
require_once __DIR__ . '/components/Fieldset.php';

$registerHandler = new RegisterHandler();
$formService = $registerHandler->getFormService();

if ($registerHandler->shouldClearForm()) {
    $_POST = [];
}

$pageTitle = 'Register - Camagru';
?>
<div class="p-16">
    <div class="max-w-md mx-auto">
        <div class="card bg-base-200">
            <div class="card-body space-y-4">
                <h1 class="card-title">Register</h1>
                <?php require_once __DIR__ . '/includes/alerts.php'; ?>
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
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>