<?php
require_once __DIR__ . '/../../components/Fieldset.php';
?>
<div class="card bg-base-200">
    <div class="card-body">
        <h3 class="card-title">Account Info</h3>
        <div class="space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-base-content/50">Username:</span>
                <span><?php echo htmlspecialchars($user['username']); ?></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-base-content/50">Email:</span>
                <span><?php echo htmlspecialchars($user['email']); ?></span>
            </div>
            <div class="flex flex-col justify-between items-center">
                <span class="text-base-content/50">Email Verified:</span>
                <div class="flex gap-2">
                    <span class="badge <?php echo ($user['is_verified'] ?? false) ? 'badge-success' : 'badge-error'; ?>"><?php echo ($user['is_verified'] ?? false) ? 'Verified' : 'Not verified'; ?></span>
                    <?php if (!($user['is_verified'] ?? false)): ?>
                        <form method="POST" class="mt-2">
                            <?php
                            Fieldset::csrfToken();
                            Fieldset::formType('resend_verification');
                            ?>
                            <button type="submit" class="btn btn-sm btn-secondary">Resend</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-base-content/50">Member Since:</span>
                <span><?php echo date('j M, Y', strtotime($user['created_at'])); ?></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-base-content/50">Posts Created:</span>
                <span><?php echo count($userPosts); ?></span>
            </div>
        </div>
    </div>
</div>