<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/components/Fieldset.php';

AuthMiddleware::requireGuest();

$authController = new AuthController();
$GLOBALS['errors'] = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    AuthMiddleware::requireCSRF();

    $result = $authController->register($_POST);

    if (!$result['success']) {
        $GLOBALS['errors'] = $result['errors'];
    } else {
        $success = $result['message'];
        $_POST = [];
    }
}

$pageTitle = 'Register - Camagru';
?>
<div class="p-16">
    <div class="max-w-md mx-auto">
        <div class="card bg-base-200">
            <div class="card-body space-y-4">
                <h1 class="card-title">Register</h1>
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <!-- TODO mettre lucide -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span><?php echo htmlspecialchars($success); ?></span>
                    </div>
                <?php endif; ?>
                <?php if (isset($GLOBALS['errors']['general'])): ?>
                    <div class="alert alert-error">
                        <!-- TODO mettre lucide -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span><?php echo htmlspecialchars($GLOBALS['errors']['general']); ?></span>
                    </div>
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
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>