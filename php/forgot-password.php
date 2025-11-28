<?php
$pageTitle = 'Forgot Password';
require_once 'includes/header.php';
require_once 'handlers/ForgotPasswordHandler.php';

$forgotPasswordHandler = new ForgotPasswordHandler();
$formService = $forgotPasswordHandler->getFormService();
?>

<main class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8">Forgot Password</h1>

        <div class="text-center mb-6">
            <p class="text-base-content/70">Enter your email address and we'll send you a link to reset your password.</p>
        </div>

        <?php require_once __DIR__ . '/includes/alerts.php'; ?>

        <?php if ($formService->hasSuccess()): ?>
            <div class="mt-4">
                <a href="login.php" class="btn btn-primary btn-sm">Back to Login</a>
            </div>
        <?php endif; ?>

        <?php if ($forgotPasswordHandler->shouldShowForm()): ?>
            <form method="POST" class="space-y-6">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Email</span>
                    </label>
                    <input type="email" name="email" class="input input-bordered w-full"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>

                <button type="submit" class="btn btn-primary w-full">Send Reset Link</button>
            </form>

            <div class="text-center mt-6">
                <a href="login.php" class="link link-primary">Back to Login</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>