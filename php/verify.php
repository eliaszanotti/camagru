<?php

require_once __DIR__ . '/handlers/VerifyHandler.php';

$verifyHandler = new VerifyHandler($_GET['token'] ?? '');
$formService = $verifyHandler->getFormService();

require_once __DIR__ . '/includes/header.php';

$pageTitle = 'Verify Email - Camagru';
?>
<div class="max-w-md mx-auto">
    <div class="card bg-base-200">
        <div class="card-body space-y-4">
            <?php if ($verifyHandler->isVerified()): ?>
                <div class="text-center">
                    <h1 class="card-title justify-center text-success">Email Verified!</h1>
                    <p class="text-base-content/70 mt-4">
                        <?php echo htmlspecialchars($verifyHandler->getMessage()); ?>
                    </p>
                    <div class="form-control mt-6">
                        <a href="login.php" class="btn btn-primary">Login Now</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center">
                    <h1 class="card-title justify-center text-error">Verification Failed</h1>
                    <p class="text-base-content/70 mt-4">
                        <?php echo htmlspecialchars($verifyHandler->getMessage()); ?>
                    </p>
                    <div class="divider">OR</div>
                    <div class="space-y-3">
                        <a href="login.php" class="btn btn-outline btn-primary w-full">Login</a>
                        <a href="register.php" class="btn btn-outline btn-secondary w-full">Register New Account</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>