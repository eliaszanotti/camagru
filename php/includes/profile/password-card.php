<div class="card bg-base-200">
    <div class="card-body">
        <h2 class="card-title">Change Password</h2>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo AuthMiddleware::getCSRFToken(); ?>">
            <input type="hidden" name="form_type" value="password_change">

            <fieldset class="fieldset w-full">
                <legend class="fieldset-legend">Current Password</legend>
                <input type="password" name="current_password" class="input w-full">
                <p class="label text-base-content/50">Leave blank to keep current password</p>
                <?php if (isset($errors['current_password'])): ?>
                    <p class="label text-error"><?php echo htmlspecialchars($errors['current_password']); ?></p>
                <?php endif; ?>
            </fieldset>

            <fieldset class="fieldset w-full">
                <legend class="fieldset-legend">New Password</legend>
                <input type="password" name="new_password" class="input w-full">
                <p class="label text-base-content/50">Leave blank to keep current password</p>
                <?php if (isset($errors['new_password'])): ?>
                    <p class="label text-error"><?php echo htmlspecialchars($errors['new_password']); ?></p>
                <?php endif; ?>
            </fieldset>

            <fieldset class="fieldset w-full">
                <legend class="fieldset-legend">Confirm New Password</legend>
                <input type="password" name="confirm_password" class="input w-full">
                <?php if (isset($errors['confirm_password'])): ?>
                    <p class="label text-error"><?php echo htmlspecialchars($errors['confirm_password']); ?></p>
                <?php endif; ?>
            </fieldset>

            <button type="submit" class="btn btn-primary w-full">Update Password</button>
        </form>
    </div>
</div>