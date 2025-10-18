<?php
require_once __DIR__ . '/../../components/Fieldset.php';
?>
<div class="card bg-base-200">
    <div class="card-body">
        <h2 class="card-title">Change Email</h2>
        <form method="POST" class="space-y-4">
            <?php
            Fieldset::csrfToken();
            Fieldset::formType('profile_email');
            Fieldset::email();
            Fieldset::submit('Update Email');
            ?>
        </form>
    </div>
</div>