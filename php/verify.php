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
            <div class="text-center space-y-4">
                <h1 class="text-3xl font-bold">
                    <?php if ($verifyHandler->isVerified()): ?>
                        Email Verified!
                    <?php else: ?>
                        Verification Failed
                    <?php endif; ?>
                </h1>
                <p class="text-base-content/70 mt-4">
                    <?php echo htmlspecialchars($verifyHandler->getMessage()); ?>
                </p>
                <div class="form-control">
                    <a href="login.php" class="btn btn-primary">Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>