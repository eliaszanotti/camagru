<?php
require_once __DIR__ . '/handlers/ForgotPasswordHandler.php';

$forgotPasswordHandler = new ForgotPasswordHandler();
$formService = $forgotPasswordHandler->getFormService();

require_once __DIR__ . '/includes/header.php';

$pageTitle = 'Forgot Password - Camagru';
?>
<div class="max-w-md mx-auto">
    <div class="card bg-base-200">
        <div class="card-body space-y-4">
            <h1 class="card-title">Forgot Password</h1>
            <p class="text-base-content/70">Enter your email address and we'll send you a link to reset your password.</p>
            <?php if ($formService->hasErrors()): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        window.showToast(<?= json_encode(implode("\n", $formService->getErrors()), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'error');
                    });
                </script>
            <?php elseif ($formService->hasSuccess()): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        window.showToast(<?= json_encode($formService->getSuccess(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>, 'success');
                    });
                </script>
            <?php endif; ?>

            <?php if ($forgotPasswordHandler->shouldShowForm()): ?>
                <form method="POST" novalidate>
                    <fieldset class="fieldset w-full">
                        <legend class="fieldset-legend">Email</legend>
                        <input type="email" name="email" class="input w-full"
                            placeholder="your@email.com"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            required />
                    </fieldset>
                    <div class="form-control mt-4">
                        <button type="submit" class="btn btn-primary w-full">Send Reset Link</button>
                    </div>
                </form>
            <?php endif; ?>

            <div class="text-center">
                <a href="login.php" class="link link-primary">Back to Login</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
