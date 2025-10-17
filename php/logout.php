<?php

require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/controllers/AuthController.php';

// Only allow logout if user is logged in
if (AuthController::isLoggedIn()) {
    // Log the logout event
    AuthMiddleware::logSecurityEvent('user_logout', [
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username']
    ]);

    // Perform logout
    $authController = new AuthController();
    $authController->logout();
}

// Redirect to home page with a logout message
header('Location: index.php?logout=1');
exit;
?>