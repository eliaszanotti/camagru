<div class="card bg-base-200">
    <div class="card-body">
        <h2 class="card-title">Notifications</h2>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo AuthMiddleware::getCSRFToken(); ?>">

            <fieldset class="fieldset">
                <legend class="fieldset-legend">Email Notifications</legend>
                <label class="cursor-pointer label">
                    <input type="checkbox" name="email_notifications" class="checkbox checkbox-primary"
                        <?php echo ($user['email_notifications'] ?? 1) ? 'checked' : ''; ?>>
                    <span class="label-text">Receive email notifications for new comments</span>
                </label>
            </fieldset>

            <button type="submit" class="btn btn-primary w-full">Update Preferences</button>
        </form>
    </div>
</div>