<?php
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Post.php';

AuthMiddleware::requireAuth();

$userModel = new User();
$postModel = new Post();

$user = $userModel->findById($_SESSION['user_id']);
$userPosts = $postModel->getByUserId($_SESSION['user_id']);

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    AuthMiddleware::requireCSRF();

    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $emailNotifications = isset($_POST['email_notifications']) ? 1 : 0;

    // Basic validation
    if (empty($username)) {
        $errors[] = 'Username is required';
    }

    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email';
    }

    // Check if changing password
    if (!empty($currentPassword) || !empty($newPassword) || !empty($confirmPassword)) {
        if (empty($currentPassword)) {
            $errors[] = 'Current password is required to change password';
        } elseif (!password_verify($currentPassword, $user['password_hash'])) {
            $errors[] = 'Current password is incorrect';
        } elseif (empty($newPassword)) {
            $errors[] = 'New password is required';
        } elseif (strlen($newPassword) < 6) {
            $errors[] = 'New password must be at least 6 characters';
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = 'New passwords do not match';
        }
    }

    if (empty($errors)) {
        // Check if username/email are already taken by other users
        if ($username !== $user['username'] && $userModel->findByUsername($username)) {
            $errors[] = 'Username already exists';
        }

        if ($email !== $user['email'] && $userModel->findByEmail($email)) {
            $errors[] = 'Email already exists';
        }
    }

    if (empty($errors)) {
        $updateData = [
            'username' => $username,
            'email' => $email,
            'email_notifications' => $emailNotifications
        ];

        if ($userModel->updateProfile($_SESSION['user_id'], $updateData)) {
            // Update session
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;

            // Update password if provided
            if (!empty($newPassword)) {
                $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $userModel->updatePassword($_SESSION['user_id'], $passwordHash);
            }

            // Refresh user data
            $user = $userModel->findById($_SESSION['user_id']);
            $success = 'Profile updated successfully!';
        } else {
            $errors[] = 'Failed to update profile';
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
            </div>
            <div class="space-y-8">
                <?php require_once __DIR__ . '/includes/profile/password-card.php'; ?>
                <?php require_once __DIR__ . '/includes/profile/notifications-card.php'; ?>
            </div>
        </div>
        <?php require_once __DIR__ . '/includes/profile/my-photos.php'; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>