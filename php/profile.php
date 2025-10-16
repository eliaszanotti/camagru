<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Profile';
require_once 'includes/header.php';

require_once 'models/User.php';
require_once 'models/Post.php';

$userModel = new User();
$postModel = new Post();

$user = $userModel->findById($_SESSION['user_id']);
$userPosts = $postModel->getByUserId($_SESSION['user_id']);

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
?>

<!-- Main Content -->
<main class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8">My Profile</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error mb-6">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success mb-6">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Form -->
            <div class="lg:col-span-2">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title">Edit Profile</h2>
                        <form method="POST" class="space-y-4">
                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Username</span>
                                </label>
                                <input type="text" name="username" class="input input-bordered w-full"
                                       value="<?php echo htmlspecialchars($user['username']); ?>" required>
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Email</span>
                                </label>
                                <input type="email" name="email" class="input input-bordered w-full"
                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Current Password</span>
                                </label>
                                <input type="password" name="current_password" class="input input-bordered w-full">
                                <label class="label">
                                    <span class="label-text-alt">Leave blank to keep current password</span>
                                </label>
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">New Password</span>
                                </label>
                                <input type="password" name="new_password" class="input input-bordered w-full">
                                <label class="label">
                                    <span class="label-text-alt">Leave blank to keep current password</span>
                                </label>
                            </div>

                            <div class="form-control">
                                <label class="label">
                                    <span class="label-text">Confirm New Password</span>
                                </label>
                                <input type="password" name="confirm_password" class="input input-bordered w-full">
                            </div>

                            <div class="form-control">
                                <label class="cursor-pointer label">
                                    <span class="label-text">Email Notifications</span>
                                    <input type="checkbox" name="email_notifications" class="checkbox checkbox-primary"
                                           <?php echo ($user['email_notifications'] ?? 1) ? 'checked' : ''; ?>>
                                </label>
                                <label class="label">
                                    <span class="label-text-alt">Receive email notifications for new comments</span>
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-full">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Stats Sidebar -->
            <div class="space-y-6">
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title">Account Info</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Member Since:</span>
                                <span><?php echo date('M j, Y', strtotime($user['created_at'])); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Email Verified:</span>
                                <span><?php echo $user['email_verified'] ? '✅ Yes' : '❌ No'; ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/70">Photos Created:</span>
                                <span><?php echo count($userPosts); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h3 class="card-title">Quick Actions</h3>
                        <div class="space-y-2">
                            <a href="create.php" class="btn btn-primary btn-block">Create New Photo</a>
                            <a href="gallery.php" class="btn btn-secondary btn-block">Browse Gallery</a>
                            <a href="logout.php" class="btn btn-error btn-outline btn-block">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Photos -->
        <div class="mt-8">
            <h2 class="text-2xl font-bold mb-4">My Photos</h2>
            <?php if (empty($userPosts)): ?>
                <div class="text-center py-16 bg-base-200 rounded-lg">
                    <h3 class="text-xl font-semibold mb-2">No photos yet</h3>
                    <p class="text-base-content/70 mb-4">Start creating and sharing your photos!</p>
                    <a href="create.php" class="btn btn-primary">Create Your First Photo</a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <?php foreach ($userPosts as $post): ?>
                        <div class="card bg-base-100 shadow-lg">
                            <figure class="h-32">
                                <img src="<?php echo htmlspecialchars($post['image_path']); ?>"
                                     alt="Your photo" class="w-full h-full object-cover">
                            </figure>
                            <div class="card-body p-3">
                                <p class="text-xs text-base-content/70">
                                    <?php echo date('M j, Y', strtotime($post['created_at'])); ?>
                                </p>
                                <div class="flex gap-1">
                                    <a href="post.php?id=<?php echo $post['id']; ?>" class="btn btn-primary btn-xs flex-1">View</a>
                                    <form method="POST" action="delete-post.php" class="inline">
                                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                        <button type="submit" class="btn btn-error btn-xs"
                                                onclick="return confirm('Delete this photo?')">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>