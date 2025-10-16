<?php
$pageTitle = 'Login';
require_once 'includes/header.php';

require_once 'models/User.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation
    if (empty($email)) {
        $errors[] = 'Email is required';
    }

    if (empty($password)) {
        $errors[] = 'Password is required';
    }

    if (empty($errors)) {
        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {
            if (!$user['email_verified']) {
                $errors[] = 'Please verify your email before logging in';
            } else {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];

                header('Location: index.php');
                exit;
            }
        } else {
            $errors[] = 'Invalid email or password';
        }
    }
}
?>

<!-- Main Content -->
<main class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8">Login</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error mb-6">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Email</span>
                </label>
                <input type="email" name="email" class="input input-bordered w-full"
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Password</span>
                </label>
                <input type="password" name="password" class="input input-bordered w-full" required>
            </div>

            <button type="submit" class="btn btn-primary w-full">Login</button>
        </form>

        <div class="text-center mt-6 space-y-2">
            <p>Don't have an account? <a href="register.php" class="link link-primary">Register here</a></p>
            <p><a href="forgot-password.php" class="link link-secondary">Forgot password?</a></p>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>