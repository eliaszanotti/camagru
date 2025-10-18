<div class="card bg-base-200">
    <div class="card-body">
        <h2 class="card-title">Username & Email</h2>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo AuthMiddleware::getCSRFToken(); ?>">

            <fieldset class="fieldset w-full">
                <legend class="fieldset-legend">Username</legend>
                <input type="text" name="username" class="input w-full"
                       value="<?php echo htmlspecialchars($user['username']); ?>" required />
                <?php if (isset($errors['username'])): ?>
                    <p class="label text-error"><?php echo htmlspecialchars($errors['username']); ?></p>
                <?php endif; ?>
            </fieldset>

            <fieldset class="fieldset w-full">
                <legend class="fieldset-legend">Email</legend>
                <input type="email" name="email" class="input w-full"
                       value="<?php echo htmlspecialchars($user['email']); ?>" required />
                <?php if (isset($errors['email'])): ?>
                    <p class="label text-error"><?php echo htmlspecialchars($errors['email']); ?></p>
                <?php endif; ?>
            </fieldset>

            <button type="submit" class="btn btn-primary w-full">Update Profile</button>
        </form>
    </div>
</div>