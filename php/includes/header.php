<?php
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$showLogoutMessage = isset($_GET['logout']) && $_GET['logout'] == '1';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en" data-theme="cupcake">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camagru - <?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Home'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <?php if ($showLogoutMessage): ?>
        <div class="toast toast-top toast-center z-50">
            <div class="alert alert-success">
                <span>You have been successfully logged out.</span>
            </div>
        </div>
    <?php endif; ?>

    <div class="w-full px-8">
        <header class="navbar mx-auto max-w-6xl">
            <div class="navbar-start">
                <a href="index.php" class="btn btn-ghost">Camagru</a>
            </div>
            <div class="navbar-center gap-2">
                <a href="index.php" class="<?php echo $currentPage === 'index' ? 'active' : ''; ?> btn btn-ghost">Home</a>
                <a href="gallery.php" class="<?php echo $currentPage === 'gallery' ? 'active' : ''; ?> btn btn-ghost">Gallery</a>
                <?php if ($isLoggedIn): ?>
                    <a href="create.php" class="<?php echo $currentPage === 'create' ? 'active' : ''; ?> btn btn-ghost">Create</a>
                <?php endif; ?>
            </div>
            <div class="navbar-end gap-2">
                <?php if ($isLoggedIn): ?>
                    <a href="profile.php" class="btn btn-ghost">Profile</a>
                    <a href="logout.php" class="btn btn-ghost">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-ghost">Login</a>
                    <a href="register.php" class="btn btn-primary">Register</a>
                <?php endif; ?>
            </div>
        </header>
    </div>