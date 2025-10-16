<?php
$pageTitle = 'Register';
require_once 'includes/header.php';

require_once 'models/User.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validation
    if (empty($username)) {
        $errors[] = 'Username is required';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters';
    }

    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email';
    }

    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    } elseif ($password !== $password_confirm) {
        $errors[] = 'Passwords do not match';
    }

    if (empty($errors)) {
        $userModel = new User();

        if ($userModel->emailExists($email)) {
            $errors[] = 'Email already exists';
        }

        if ($userModel->usernameExists($username)) {
            $errors[] = 'Username already exists';
        }
    }

    if (empty($errors)) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $verificationToken = bin2hex(random_bytes(32));

        $userData = [
            'username' => $username,
            'email' => $email,
            'password_hash' => $passwordHash,
            'verification_token' => $verificationToken
        ];

        if ($userModel->create($userData)) {
            $success = 'Registration successful! Please check your email to verify your account.';
            // TODO: Send verification email
        } else {
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}
?>

<!-- Main Content -->
<main class="container mx-auto px-4 py-8">
    <div class="max-w-md mx-auto">
        <h1 class="text-3xl font-bold text-center mb-8">Register</h1>

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

        <form method="POST" class="space-y-6">
            <div class="form-control">
                <label class="label">
                    <span class="label-text">Username</span>
                </label>
                <input type="text" name="username" class="input input-bordered w-full"
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
            </div>

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

            <div class="form-control">
                <label class="label">
                    <span class="label-text">Confirm Password</span>
                </label>
                <input type="password" name="password_confirm" class="input input-bordered w-full" required>
            </div>

            <button type="submit" class="btn btn-primary w-full">Register</button>
        </form>

        <div class="text-center mt-6">
            <p>Already have an account? <a href="login.php" class="link link-primary">Login here</a></p>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>