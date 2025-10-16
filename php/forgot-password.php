<?php
$pageTitle = 'Forgot Password';
require_once 'includes/header.php';

require_once 'models/User.php';

$userModel = new User();
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email';
    }

    if (empty($errors)) {
        $user = $userModel->findByEmail($email);

        if ($user) {
            // Create reset token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            if ($userModel->createPasswordResetToken($email, $token, $expires)) {
                $success = 'Password reset link has been sent to your email.';
                // TODO: Send actual email with reset link
            } else {
                $errors[] = 'Failed to create reset token';
            }
        } else {
            // Don't reveal if email exists or not
            $success = 'If the email exists, a password reset link has been sent.';
        }
    }
}
?>

<!-- Main Content -->
<main class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8">Forgot Password</h1>

        <div class="text-center mb-6">
            <p class="text-base-content/70">Enter your email address and we'll send you a link to reset your password.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error mb-6">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success mb-6">
                <?php echo htmlspecialchars($success); ?>
                <div class="mt-4">
                    <a href="login.php" class="btn btn-primary btn-sm">Back to Login</a>
                </div>
            </div>
        <?php else: ?>
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