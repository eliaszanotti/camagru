<?php
require_once __DIR__ . '/../../components/Fieldset.php';
?>
<div class="card bg-base-200">
    <div class="card-body">
        <h2 class="card-title">Change Password</h2>
        <form method="POST" class="space-y-4">
            <?php
            Fieldset::csrfToken();
            Fieldset::formType('password_change');
            Fieldset::currentPassword($errors['current_password'] ?? '');
            Fieldset::newPassword($errors['new_password'] ?? '');
            Fieldset::confirmPassword($errors['confirm_password'] ?? '');
            Fieldset::submit('Update Password');
            ?>
        </form>
    </div>
</div>