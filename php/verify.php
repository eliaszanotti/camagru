<?php

require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/controllers/AuthController.php';

$authController = new AuthController();
$token = $_GET['token'] ?? '';
$result = ['success' => false, 'message' => ''];

if (empty($token)) {
    $result['message'] = 'Invalid verification link';
} else {
    $result = $authController->verifyEmail($token);
}

$pageTitle = 'Verify Email - Camagru';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="min-h-screen bg-base-200">
    <div class="hero min-h-screen">
        <div class="hero-content text-center">
            <div class="card shrink-0 w-full max-w-md shadow-2xl bg-base-100">
                <div class="card-body">
                    <?php if ($result['success']): ?>
                        <div class="text-center">
                            <div class="text-6xl mb-4 text-success">✅</div>
                            <h1 class="text-3xl font-bold mb-4 text-success">Email Verified!</h1>
                            <p class="text-base-content/70 mb-8">
                                <?php echo htmlspecialchars($result['message']); ?>
                            </p>
                            <div class="form-control">
                                <a href="login.php" class="btn btn-primary w-full">
                                    Login Now
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <div class="text-6xl mb-4 text-error">❌</div>
                            <h1 class="text-3xl font-bold mb-4 text-error">Verification Failed</h1>
                            <p class="text-base-content/70 mb-8">
                                <?php echo htmlspecialchars($result['message']); ?>
                            </p>
                            <div class="divider">OR</div>
                            <div class="space-y-3">
                                <a href="login.php" class="btn btn-outline btn-primary w-full">
                                    Login
                                </a>
                                <a href="register.php" class="btn btn-outline btn-secondary w-full">
                                    Register New Account
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-redirect after 5 seconds on successful verification
        <?php if ($result['success']): ?>
        setTimeout(function() {
            window.location.href = 'login.php';
        }, 5000);
        <?php endif; ?>

        // Add some interactive animations
        document.addEventListener('DOMContentLoaded', function() {
            const card = document.querySelector('.card');
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';

            setTimeout(function() {
                card.style.transition = 'all 0.5s ease-out';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>