<?php

/**
 * Reusable fieldset components for forms
 */
class Fieldset
{

    /**
     * Username fieldset - fully configured
     */
    public static function username(): void
    {
?>
        <fieldset class="fieldset w-full">
            <legend class="fieldset-legend">Username</legend>
            <input type="text" name="username" class="input w-full"
                placeholder="Enter your username"
                pattern="[a-zA-Z0-9_]+"
                minlength="3"
                maxlength="50"
                required />
            <p class="label text-base-content/50 whitespace-break-spaces">Must contain letters, numbers, and underscores only</p>
            <?php if (isset($GLOBALS['errors']['username'])): ?>
                <p class="label text-error"><?php echo htmlspecialchars($GLOBALS['errors']['username']); ?></p>
            <?php endif; ?>
        </fieldset>
    <?php
    }

    /**
     * Email fieldset - fully configured
     */
    public static function email(): void
    {
    ?>
        <fieldset class="fieldset w-full">
            <legend class="fieldset-legend">Email</legend>
            <input type="email" name="email" class="input w-full"
                placeholder="your@email.com"
                required />
            <p class="label text-base-content/50 whitespace-break-spaces">We'll send you a verification link</p>
            <?php if (isset($GLOBALS['errors']['email'])): ?>
                <p class="label text-error"><?php echo htmlspecialchars($GLOBALS['errors']['email']); ?></p>
            <?php endif; ?>
        </fieldset>
    <?php
    }

    /**
     * Password fieldset - fully configured
     */
    public static function password(): void
    {
    ?>
        <fieldset class="fieldset w-full">
            <legend class="fieldset-legend">Password</legend>
            <input type="password" name="password" class="input w-full"
                placeholder="Enter your password"
                pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?:{}|<>]).{8,}"
                minlength="8"
                required />
            <p class="label text-base-content/50 whitespace-break-spaces">8+ chars with uppercase, lowercase, number, and special character</p>
            <?php if (isset($GLOBALS['errors']['password'])): ?>
                <p class="label text-error"><?php echo htmlspecialchars($GLOBALS['errors']['password']); ?></p>
            <?php endif; ?>
        </fieldset>
    <?php
    }

    /**
     * Confirm password fieldset - fully configured
     */
    public static function confirmPassword(): void
    {
    ?>
        <fieldset class="fieldset w-full">
            <legend class="fieldset-legend">Confirm Password</legend>
            <input type="password" name="confirm_password" class="input w-full"
                placeholder="Confirm your password"
                minlength="8"
                required />
            <p class="label text-base-content/50">Must match your password</p>
            <?php if (isset($GLOBALS['errors']['confirm_password'])): ?>
                <p class="label text-error"><?php echo htmlspecialchars($GLOBALS['errors']['confirm_password']); ?></p>
            <?php endif; ?>
        </fieldset>
    <?php
    }

    /**
     * Current password fieldset - fully configured
     */
    public static function currentPassword(): void
    {
    ?>
        <fieldset class="fieldset w-full">
            <legend class="fieldset-legend">Current Password</legend>
            <input type="password" name="current_password" class="input w-full"
                placeholder="Enter your current password" />
            <p class="label text-base-content/50">Leave blank to keep current password</p>
            <?php if (isset($GLOBALS['errors']['current_password'])): ?>
                <p class="label text-error"><?php echo htmlspecialchars($GLOBALS['errors']['current_password']); ?></p>
            <?php endif; ?>
        </fieldset>
    <?php
    }

    /**
     * New password fieldset - fully configured
     */
    public static function newPassword(): void
    {
    ?>
        <fieldset class="fieldset w-full">
            <legend class="fieldset-legend">New Password</legend>
            <input type="password" name="new_password" class="input w-full"
                placeholder="Enter your new password"
                pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*(),.?:{}|<>]).{8,}"
                minlength="8" />
            <p class="label text-base-content/50">Leave blank to keep current password</p>
            <?php if (isset($GLOBALS['errors']['new_password'])): ?>
                <p class="label text-error"><?php echo htmlspecialchars($GLOBALS['errors']['new_password']); ?></p>
            <?php endif; ?>
        </fieldset>
    <?php
    }

    /**
     * Login identifier fieldset - fully configured
     */
    public static function loginIdentifier(): void
    {
    ?>
        <fieldset class="fieldset w-full">
            <legend class="fieldset-legend">Email or Username</legend>
            <input type="text" name="login_identifier" class="input w-full"
                value="<?php echo htmlspecialchars($_POST['login_identifier'] ?? ''); ?>"
                placeholder="Enter your email or username" required />
            <p class="label text-base-content/50 whitespace-break-spaces">You can use either your email or username</p>
            <?php if (isset($GLOBALS['errors']['login_identifier'])): ?>
                <p class="label text-error"><?php echo htmlspecialchars($GLOBALS['errors']['login_identifier']); ?></p>
            <?php endif; ?>
        </fieldset>
    <?php
    }

    /**
     * Checkbox fieldset
     */
    public static function checkbox(string $name, string $label, bool $checked = false): void
    {
    ?>
        <fieldset class="fieldset">
            <label class="cursor-pointer label">
                <input type="checkbox" name="<?php echo htmlspecialchars($name); ?>" class="checkbox checkbox-primary"
                    <?php echo $checked ? 'checked' : ''; ?>>
                <span class="label-text whitespace-break-spaces"><?php echo htmlspecialchars($label); ?></span>
            </label>
            <?php if (isset($GLOBALS['errors'][$name])): ?>
                <p class="label text-error"><?php echo htmlspecialchars($GLOBALS['errors'][$name]); ?></p>
            <?php endif; ?>
        </fieldset>
    <?php
    }

    /**
     * Text area fieldset TODO faire en sorte de pas avoir de truc comme ca generique mais jsute de lappeler sharedescritiption textarea
     */
    public static function textArea(array $config): void
    {
        $name = $config['name'] ?? '';
        $label = $config['label'] ?? '';
        $placeholder = $config['placeholder'] ?? '';
        $rows = $config['rows'] ?? 4;
        $required = $config['required'] ?? false;
        $help = $config['help'] ?? '';
    ?>
        <fieldset class="fieldset w-full">
            <legend class="fieldset-legend"><?php echo htmlspecialchars($label); ?></legend>
            <textarea name="<?php echo htmlspecialchars($name); ?>" class="textarea w-full"
                placeholder="<?php echo htmlspecialchars($placeholder); ?>"
                rows="<?php echo $rows; ?>"
                <?php echo $required ? 'required' : ''; ?>><?php echo htmlspecialchars($_POST[$name] ?? ''); ?></textarea>
            <?php if ($help): ?>
                <p class="label text-base-content/50"><?php echo htmlspecialchars($help); ?></p>
            <?php endif; ?>
            <?php if (isset($GLOBALS['errors'][$name])): ?>
                <p class="label text-error"><?php echo htmlspecialchars($GLOBALS['errors'][$name]); ?></p>
            <?php endif; ?>
        </fieldset>
    <?php
    }

    /**
     * Submit button
     */
    public static function submit(string $text = 'Submit'): void
    {
    ?>
        <button type="submit" class="btn btn-primary w-full">
            <?php echo htmlspecialchars($text); ?>
        </button>
    <?php
    }

    /**
     * CSRF token field
     */
    public static function csrfToken(): void
    {
    ?>
        <input type="hidden" name="csrf_token" value="<?php echo AuthMiddleware::getCSRFToken(); ?>">
    <?php
    }

    /**
     * Form type hidden field
     */
    public static function formType(string $type): void
    {
    ?>
        <input type="hidden" name="form_type" value="<?php echo htmlspecialchars($type); ?>">
<?php
    }
}
