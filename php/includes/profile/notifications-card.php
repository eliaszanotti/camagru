<?php
require_once __DIR__ . '/../../components/Fieldset.php';
?>
<div class="card bg-base-200">
    <div class="card-body">
        <h2 class="card-title">Notifications</h2>
        <form method="POST" class="space-y-4">
            <?php
            Fieldset::csrfToken();
            Fieldset::formType('notifications');
            Fieldset::checkbox('email_notifications', 'Receive email notifications for new comments', ($user['email_notifications'] ?? 1));
            Fieldset::submit('Update Preferences');
            ?>
        </form>
    </div>
</div>