<?php
$pageTitle = 'Verify Email';
require_once 'includes/header.php';

require_once 'models/User.php';

$userModel = new User();
$token = $_GET['token'] ?? '';
$verified = false;
$error = '';

if (empty($token)) {
    $error = 'Invalid verification link';
} else {
    if ($userModel->verifyEmail($token)) {
        $verified = true;
    } else {
        $error = 'Invalid or expired verification token';
    }
}
?>

<!-- Main Content -->
<main class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto">
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body text-center">
                <?php if ($verified): ?>
                    <div class="text-6xl mb-4">✅</div>
                    <h1 class="text-2xl font-bold mb-4">Email Verified!</h1>
                    <p class="text-base-content/70 mb-6">
                        Your email has been successfully verified. You can now login to your account.
                    </p>
                    <a href="login.php" class="btn btn-primary">Login Now</a>
                <?php else: ?>
                    <div class="text-6xl mb-4">❌</div>
                    <h1 class="text-2xl font-bold mb-4">Verification Failed</h1>
                    <p class="text-base-content/70 mb-6">
                        <?php echo htmlspecialchars($error); ?>
                    </p>
                    <div class="space-y-2">
                        <a href="login.php" class="btn btn-primary btn-block">Login</a>
                        <a href="register.php" class="btn btn-secondary btn-block">Register</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>