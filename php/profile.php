<?php
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Post.php';
require_once __DIR__ . '/controllers/AuthController.php';

AuthMiddleware::requireAuth();

$userModel = new User();
$postModel = new Post();
$authController = new AuthController();

$user = $userModel->findById($_SESSION['user_id']);
$userPosts = $postModel->getByUserId($_SESSION['user_id']);

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    AuthMiddleware::requireCSRF();

    $formType = $_POST['form_type'] ?? '';

    if ($formType === 'profile_username') {
        // Handle username form
        $username = trim($_POST['username'] ?? '');

        $updateData = [
            'username' => $username
        ];

        $validationResult = $authController->updateProfile($_SESSION['user_id'], $updateData);

        if (!$validationResult['success']) {
            $errors = $validationResult['errors'];
        } else {
            $success = $validationResult['message'];
            // Update session variable
            $_SESSION['username'] = $username;
            // Refresh user data
            $user = $userModel->findById($_SESSION['user_id']);
        }
    } elseif ($formType === 'profile_email') {
        // Handle email form
        $email = trim($_POST['email'] ?? '');

        // Check if email is actually changing
        if ($email !== $user['email']) {
            // Generate new verification token
            $verificationToken = bin2hex(random_bytes(32));

            $updateData = [
                'email' => $email,
                'email_verification_token' => $verificationToken,
                'is_verified' => false
            ];

            $validationResult = $authController->updateProfile($_SESSION['user_id'], $updateData);

            if (!$validationResult['success']) {
                $errors = $validationResult['errors'];
            } else {
                // Send new verification email
                require_once __DIR__ . '/services/EmailService.php';
                $emailService = new EmailService();

                if (!$emailService->sendVerificationEmail($email, $user['username'], $verificationToken)) {
                    error_log("Failed to send verification email to: " . $email);
                    // Don't fail the update, but log it
                }

                $success = 'Email updated successfully! Please check your inbox to verify your new email address.';

                // Update session variable (but mark as unverified)
                $_SESSION['email'] = $email;
                $_SESSION['is_verified'] = false;
                // Refresh user data
                $user = $userModel->findById($_SESSION['user_id']);
            }
        } else {
            // Email didn't change, just update profile normally
            $updateData = [
                'email' => $email
            ];

            $validationResult = $authController->updateProfile($_SESSION['user_id'], $updateData);

            if (!$validationResult['success']) {
                $errors = $validationResult['errors'];
            } else {
                $success = $validationResult['message'];
                // Refresh user data
                $user = $userModel->findById($_SESSION['user_id']);
            }
        }
    } elseif ($formType === 'password_change') {
        // Handle password change form
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Verify current password first
        if (empty($currentPassword)) {
            $errors['current_password'] = 'Current password is required to change password';
        } elseif (!password_verify($currentPassword, $user['password_hash'])) {
            $errors['current_password'] = 'Current password is incorrect';
        } else {
            $updateData = [
                'password' => $newPassword,
                'confirm_password' => $confirmPassword
            ];

            $validationResult = $authController->updateProfile($_SESSION['user_id'], $updateData);

            if (!$validationResult['success']) {
                $errors = $validationResult['errors'];
            } else {
                $success = $validationResult['message'];
                // Refresh user data
                $user = $userModel->findById($_SESSION['user_id']);
            }
        }
    } elseif ($formType === 'notifications') {
        // Handle notifications form
        $emailNotifications = isset($_POST['email_notifications']) ? 1 : 0;

        $updateData = [
            'email_notifications' => $emailNotifications
        ];

        $validationResult = $authController->updateProfile($_SESSION['user_id'], $updateData);

        if (!$validationResult['success']) {
            $errors = $validationResult['errors'];
        } else {
            $success = $validationResult['message'];
            // Refresh user data
            $user = $userModel->findById($_SESSION['user_id']);
        }
    }
}

require_once __DIR__ . '/includes/header.php';
$pageTitle = 'Profile - Camagru';
?>

<main class="p-16">
    <div class="max-w-4xl mx-auto space-y-8">
        <h1 class="text-3xl font-bold">My Profile</h1>
        <?php require_once __DIR__ . '/includes/profile/alerts.php'; ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="space-y-8">
                <?php require_once __DIR__ . '/includes/profile/account-info-card.php'; ?>
                <?php require_once __DIR__ . '/includes/profile/username-card.php'; ?>
                <?php require_once __DIR__ . '/includes/profile/email-card.php'; ?>
            </div>
            <div class="space-y-8">
                <?php require_once __DIR__ . '/includes/profile/password-card.php'; ?>
                <?php require_once __DIR__ . '/includes/profile/notifications-card.php'; ?>
            </div>
        </div>
        <h1 class="text-3xl font-bold">My Photos</h1>
        <?php require_once __DIR__ . '/includes/profile/my-photos.php'; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>