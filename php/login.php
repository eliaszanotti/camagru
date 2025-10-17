<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/controllers/AuthController.php';

AuthMiddleware::requireGuest();

$authController = new AuthController();
$errors = [];
$success = '';

// Check for session timeout
if (isset($_GET['timeout'])) {
    $errors['general'] = 'Your session has expired. Please login again.';
}

// Check for redirect after login
$redirectTo = $_SESSION['redirect_after_login'] ?? 'index.php';
unset($_SESSION['redirect_after_login']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    AuthMiddleware::requireCSRF();

    // Check rate limiting
    if (!AuthMiddleware::checkLoginRateLimit()) {
        $errors['general'] = 'Too many login attempts. Please try again later.';
    } else {
        $result = $authController->login($_POST);

        if ($result['success']) {
            AuthMiddleware::clearLoginRateLimit();
            header('Location: ' . $redirectTo);
            exit;
        } else {
            $errors = $result['errors'];
        }
    }
}

$pageTitle = 'Login - Camagru';
?>
<div class="hero min-h-screen">
    <div class="hero-content flex-col lg:flex-row-reverse">
        <div class="text-center lg:text-left">
            <h1 class="text-5xl font-bold text-primary">Welcome Back!</h1>
            <p class="py-6 text-base-content/70">
                Sign in to continue creating amazing photos<br>
                and connect with the Camagru community.
            </p>
        </div>
        <div class="card shrink-0 w-full max-w-md shadow-2xl bg-base-100">
            <form class="card-body" method="POST" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo AuthMiddleware::getCSRFToken(); ?>">

                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-error">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span><?php echo htmlspecialchars($errors['general']); ?></span>
                    </div>
                <?php endif; ?>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Email or Username</legend>
                    <label class="input validator">
                        <input type="text"
                            name="login_identifier"
                            class="input input-bordered w-full validator-required"
                            value="<?php echo htmlspecialchars($_POST['login_identifier'] ?? ''); ?>"
                            placeholder="Enter your email or username"
                            required />
                        <div class="validator-hint">You can use either your email or username</div>
                    </label>
                    <?php if (isset($errors['login_identifier'])): ?>
                        <p class="text-error text-sm mt-1"><?php echo htmlspecialchars($errors['login_identifier']); ?></p>
                    <?php endif; ?>
                </fieldset>

                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Password</legend>
                    <label class="input validator">
                        <input type="password"
                            name="password"
                            class="input input-bordered w-full validator-required"
                            placeholder="Enter your password"
                            required />
                        <div class="validator-hint">Enter your account password</div>
                    </label>
                    <?php if (isset($errors['password'])): ?>
                        <p class="text-error text-sm mt-1"><?php echo htmlspecialchars($errors['password']); ?></p>
                    <?php endif; ?>
                </fieldset>

                <div class="form-control mt-6">
                    <button type="submit" class="btn btn-primary w-full">
                        Sign In
                    </button>
                </div>

                <div class="divider">OR</div>

                <div class="text-center space-y-2">
                    <p class="text-base-content/70">
                        Don't have an account?
                        <a href="register.php" class="link link-primary link-hover">Create one</a>
                    </p>
                    <p>
                        <a href="forgot-password.php" class="link link-secondary link-hover">Forgot your password?</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
    // Client-side validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const form = e.target;
        const identifier = form.querySelector('input[name="login_identifier"]').value.trim();
        const password = form.querySelector('input[name="password"]').value;

        // Clear previous client-side errors
        document.querySelectorAll('.client-error').forEach(el => el.remove());

        let hasError = false;

        // Validate identifier
        if (!identifier) {
            showError('login_identifier', 'Email or username is required');
            hasError = true;
        }

        // Validate password
        if (!password) {
            showError('password', 'Password is required');
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
        }
    });

    function showError(fieldName, message) {
        const field = document.querySelector(`input[name="${fieldName}"]`);
        const errorDiv = document.createElement('div');
        errorDiv.className = 'text-error text-sm mt-1 client-error';
        errorDiv.textContent = message;
        field.parentNode.parentNode.insertBefore(errorDiv, field.parentNode.nextSibling);
    }

    // Auto-focus on first field
    document.addEventListener('DOMContentLoaded', function() {
        const firstInput = document.querySelector('input[name="login_identifier"]');
        if (firstInput && !firstInput.value) {
            firstInput.focus();
        }
    });

    // Add keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const activeElement = document.activeElement;
            const form = document.querySelector('form');

            if (activeElement && form.contains(activeElement)) {
                // Check if all required fields are filled
                const identifier = form.querySelector('input[name="login_identifier"]').value.trim();
                const password = form.querySelector('input[name="password"]').value;

                if (identifier && password) {
                    form.submit();
                }
            }
        }
    });
</script>