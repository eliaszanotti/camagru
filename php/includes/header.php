<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

    <div class="w-full">
        <header class="navbar mx-auto max-w-6xl px-4">
            <div class="navbar-start">
                <a href="index.php" class="<?php echo $currentPage === 'index' ? 'active' : ''; ?> btn md:btn-md btn-sm btn-ghost">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6 lucide lucide-house-icon lucide-house">
                        <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8" />
                        <path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    </svg></a>
            </div>
            <div class="navbar-end gap-2">
                <?php if ($isLoggedIn): ?>
                    <a href="gallery.php" class="<?php echo $currentPage === 'gallery' ? 'active' : ''; ?> btn md:btn-md btn-sm btn-ghost">Gallery</a>
                    <a href="profile.php" class="btn md:btn-md btn-sm btn-ghost">Profile</a>
                    <a href="logout.php" class="btn md:btn-md btn-sm btn-ghost">Logout</a>
                    <a href="create.php" class="<?php echo $currentPage === 'create' ? 'active' : ''; ?> btn md:btn-md btn-sm btn-square btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-6 lucide lucide-plus-icon lucide-plus">
                            <path d="M5 12h14" />
                            <path d="M12 5v14" />
                        </svg>
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn md:btn-md btn-sm btn-ghost">Login</a>
                    <a href="register.php" class="btn md:btn-md btn-sm btn-primary">Register</a>
                <?php endif; ?>
            </div>
        </header>
    </div>

    <main class="lg:p-16 md:p-8 p-4">
        <div class="container mx-auto">