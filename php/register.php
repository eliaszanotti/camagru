<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/controllers/AuthController.php';

AuthMiddleware::requireGuest();

$authController = new AuthController();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    AuthMiddleware::requireCSRF();

    $result = $authController->register($_POST);

    if ($result['success']) {
        $success = $result['message'];
        $_POST = [];
    } else {
        $errors = $result['errors'];
    }
}

$pageTitle = 'Register - Camagru';
?>
<div class="p-16">
    <div class="max-w-md mx-auto">
        <div class="card bg-base-200">
            <div class="card-body">
                <h1 class="card-title">Register</h1>
                <form method="POST" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo AuthMiddleware::getCSRFToken(); ?>">

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <!-- TODO mettre lucide -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span><?php echo htmlspecialchars($success); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-error">
                            <!-- TODO mettre lucide -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span><?php echo htmlspecialchars($errors['general']); ?></span>
                        </div>
                    <?php endif; ?>

                    <fieldset class="fieldset w-full">
                        <legend class="fieldset-legend">Username</legend>
                        <input type="text"
                            name="username"
                            class="input input-bordered w-full validator-required"
                            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                            placeholder="Choose a username"
                            pattern="[a-zA-Z0-9_]+"
                            minlength="3"
                            maxlength="50"
                            required />
                        <p class="label">Must contain letters, numbers, and underscores only</p>
                        <?php if (isset($errors['username'])): ?>
                            <p class="label text-error"><?php echo htmlspecialchars($errors['username']); ?></p>
                        <?php endif; ?>
                    </fieldset>

                    <fieldset class="fieldset w-full">
                        <legend class="fieldset-legend">Email</legend>
                        <input type="email"
                            name="email"
                            class="input input-bordered w-full validator-required"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                            placeholder="your@email.com"
                            required />
                        <p class="label">We'll send you a verification link</p>
                        <?php if (isset($errors['email'])): ?>
                            <p class="label text-error"><?php echo htmlspecialchars($errors['email']); ?></p>
                        <?php endif; ?>
                    </fieldset>

                    <fieldset class="fieldset w-full">
                        <legend class="fieldset-legend">Password</legend>
                        <input type="password"
                            name="password"
                            class="input input-bordered w-full validator-required"
                            placeholder="Create a strong password"
                            minlength="8"
                            pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?" :{}|<>]).{8,}"
                        required />
                        <p class="label">8+ chars with uppercase, lowercase, number, and special character</p>
                        <?php if (isset($errors['password'])): ?>
                            <p class="label text-error"><?php echo htmlspecialchars($errors['password']); ?></p>
                        <?php endif; ?>
                    </fieldset>

                    <fieldset class="fieldset w-full">
                        <legend class="fieldset-legend">Confirm Password</legend>
                        <input type="password"
                            name="confirm_password"
                            class="input input-bordered w-full validator-required"
                            placeholder="Confirm your password"
                            required />
                        <p class="label">Must match your password</p>
                        <?php if (isset($errors['confirm_password'])): ?>
                            <p class="label text-error"><?php echo htmlspecialchars($errors['confirm_password']); ?></p>
                        <?php endif; ?>
                    </fieldset>

                    <div class="form-control mt-6">
                        <button type="submit" class="btn btn-primary w-full">
                            Create Account
                        </button>
                    </div>

                    <div class="divider">OR</div>

                    <div class="text-center">
                        <p class="text-base-content/70">
                            Already have an account?
                            <a href="login.php" class="link link-primary link-hover">Sign in</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
    // Client-side validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const form = e.target;
        const password = form.querySelector('input[name="password"]').value;
        const confirmPassword = form.querySelector('input[name="confirm_password"]').value;
        const username = form.querySelector('input[name="username"]').value;

        // Clear previous client-side errors
        document.querySelectorAll('.client-error').forEach(el => el.remove());

        let hasError = false;

        // Validate username
        if (!/^[a-zA-Z0-9_]+$/.test(username)) {
            showError('username', 'Username can only contain letters, numbers, and underscores');
            hasError = true;
        }

        // Validate password match
        if (password !== confirmPassword) {
            showError('confirm_password', 'Passwords do not match');
            hasError = true;
        }

        // Validate password strength
        if (password.length < 8) {
            showError('password', 'Password must be at least 8 characters long');
            hasError = true;
        } else if (!/[A-Z]/.test(password)) {
            showError('password', 'Password must contain at least one uppercase letter');
            hasError = true;
        } else if (!/[a-z]/.test(password)) {
            showError('password', 'Password must contain at least one lowercase letter');
            hasError = true;
        } else if (!/[0-9]/.test(password)) {
            showError('password', 'Password must contain at least one number');
            hasError = true;
        } else if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
            showError('password', 'Password must contain at least one special character');
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

    // Real-time password validation feedback
    const passwordInput = document.querySelector('input[name="password"]');
    const confirmPasswordInput = document.querySelector('input[name="confirm_password"]');

    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let isValid = true;
            let message = '';

            if (password.length < 8) {
                isValid = false;
                message = 'Too short (min 8 chars)';
            } else if (!/[A-Z]/.test(password)) {
                isValid = false;
                message = 'Needs uppercase';
            } else if (!/[a-z]/.test(password)) {
                isValid = false;
                message = 'Needs lowercase';
            } else if (!/[0-9]/.test(password)) {
                isValid = false;
                message = 'Needs number';
            } else if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                isValid = false;
                message = 'Needs special char';
            }

            if (password.length > 0) {
                this.className = isValid ?
                    'input input-bordered w-full validator-required input-success' :
                    'input input-bordered w-full validator-required input-error';
            }
        });
    }

    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirmPassword = this.value;

            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    this.className = 'input input-bordered w-full validator-required input-success';
                } else {
                    this.className = 'input input-bordered w-full validator-required input-error';
                }
            }
        });
    }
</script>