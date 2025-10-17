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
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-error">
                        <!-- TODO mettre lucide -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span><?php echo htmlspecialchars($errors['general']); ?></span>
                    </div>
                <?php endif; ?>
                <form method="POST" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo AuthMiddleware::getCSRFToken(); ?>">

                    <fieldset class="fieldset w-full">
                        <legend class="fieldset-legend">Username</legend>
                        <input type="text"
                            name="username"
                            class="input w-full"
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
                            class="input w-full"
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
                            class="input w-full"
                            placeholder="Create a strong password"
                            minlength="8"
                            pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?:{}|<>]).{8,}"
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
                            class="input w-full"
                            placeholder="Confirm your password"
                            required />
                        <p class="label">Must match your password</p>
                        <?php if (isset($errors['confirm_password'])): ?>
                            <p class="label text-error"><?php echo htmlspecialchars($errors['confirm_password']); ?></p>
                        <?php endif; ?>
                    </fieldset>

                    <div class="form-control mt-4">
                        <button type="submit" class="btn btn-primary w-full">
                            Create Account
                        </button>
                    </div>
                </form>
                <div class="divider">OR</div>
                <p class="text-center">
                    Already have an account?
                    <a href="login.php" class="link link-primary">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>