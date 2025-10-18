<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/components/Fieldset.php';

AuthMiddleware::requireGuest();

$authController = new AuthController();
$GLOBALS['errors'] = [];

// Check for session timeout
if (isset($_GET['timeout'])) {
    $GLOBALS['errors']['general'] = 'Your session has expired. Please login again.';
}

// Check for redirect after login
$redirectTo = $_SESSION['redirect_after_login'] ?? 'index.php';
unset($_SESSION['redirect_after_login']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    AuthMiddleware::requireCSRF();

    // Check rate limiting
    if (!AuthMiddleware::checkLoginRateLimit()) {
        $GLOBALS['errors']['general'] = 'Too many login attempts. Please try again later.';
    } else {
        $result = $authController->login($_POST);

        if (!$result['success']) {
            $GLOBALS['errors'] = $result['errors'];
        } else {
            AuthMiddleware::clearLoginRateLimit();
            header('Location: ' . $redirectTo);
            exit;
        }
    }
}

$pageTitle = 'Login - Camagru';
?>
<div class="p-16">
    <div class="max-w-md mx-auto">
        <div class="card bg-base-200">
            <div class="card-body space-y-4">
                <h1 class="card-title">Login</h1>
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