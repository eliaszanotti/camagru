<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../services/FormService.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../services/EmailService.php';

class ProfileHandler {
    private User $userModel;
    private Post $postModel;
    private AuthController $authController;
    private FormService $formService;
    private array $user;
    private array $userPosts;

    public function __construct() {
        AuthMiddleware::requireAuth();

        $this->userModel = new User();
        $this->postModel = new Post();
        $this->authController = new AuthController();
        $this->formService = FormService::create();

        $this->user = $this->userModel->findById($_SESSION['user_id']);
        $this->userPosts = $this->postModel->getByUserId($_SESSION['user_id']);

        $this->handleRequest();
    }

    public function getUser(): array {
        return $this->user;
    }

    public function getUserPosts(): array {
        return $this->userPosts;
    }

    public function getFormService(): FormService {
        return $this->formService;
    }

    private function handleRequest(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        AuthMiddleware::requireCSRF();

        $formType = $_POST['form_type'] ?? '';

        switch ($formType) {
            case 'profile_username':
                $this->handleUsernameUpdate();
                break;
            case 'profile_email':
                $this->handleEmailUpdate();
                break;
            case 'password_change':
                $this->handlePasswordChange();
                break;
            case 'notifications':
                $this->handleNotificationsUpdate();
                break;
        }
    }

    private function handleUsernameUpdate(): void {
        $username = trim($_POST['username'] ?? '');

        $updateData = ['username' => $username];
        $validationResult = $this->authController->updateProfile($_SESSION['user_id'], $updateData);

        if (!$validationResult['success']) {
            $this->formService->addErrors($validationResult['errors']);
        } else {
            $this->formService->setSuccess($validationResult['message']);
            $_SESSION['username'] = $username;
            $this->user = $this->userModel->findById($_SESSION['user_id']);
        }
    }

    private function handleEmailUpdate(): void {
        $email = trim($_POST['email'] ?? '');

        if ($email !== $this->user['email']) {
            $verificationToken = bin2hex(random_bytes(32));
            $updateData = [
                'email' => $email,
                'email_verification_token' => $verificationToken,
                'is_verified' => false
            ];

            $validationResult = $this->authController->updateProfile($_SESSION['user_id'], $updateData);

            if (!$validationResult['success']) {
                $this->formService->addErrors($validationResult['errors']);
            } else {
                $emailService = new EmailService();
                if (!$emailService->sendVerificationEmail($email, $this->user['username'], $verificationToken)) {
                    error_log("Failed to send verification email to: {$email}");
                }

                $this->formService->setSuccess('Email updated successfully! Please check your inbox to verify your new email address.');
                $_SESSION['email'] = $email;
                $_SESSION['is_verified'] = false;
                $this->user = $this->userModel->findById($_SESSION['user_id']);
            }
        } else {
            $updateData = ['email' => $email];
            $validationResult = $this->authController->updateProfile($_SESSION['user_id'], $updateData);

            if (!$validationResult['success']) {
                $this->formService->addErrors($validationResult['errors']);
            } else {
                $this->formService->setSuccess($validationResult['message']);
                $this->user = $this->userModel->findById($_SESSION['user_id']);
            }
        }
    }

    private function handlePasswordChange(): void {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword)) {
            $this->formService->addError('current_password', 'Current password is required to change password');
        } elseif (!password_verify($currentPassword, $this->user['password_hash'])) {
            $this->formService->addError('current_password', 'Current password is incorrect');
        } else {
            $updateData = [
                'password' => $newPassword,
                'confirm_password' => $confirmPassword
            ];

            $validationResult = $this->authController->updateProfile($_SESSION['user_id'], $updateData);

            if (!$validationResult['success']) {
                $this->formService->addErrors($validationResult['errors']);
            } else {
                $this->formService->setSuccess($validationResult['message']);
                $this->user = $this->userModel->findById($_SESSION['user_id']);
            }
        }
    }

    private function handleNotificationsUpdate(): void {
        $emailNotifications = isset($_POST['email_notifications']) ? 1 : 0;
        $updateData = ['email_notifications' => $emailNotifications];

        $validationResult = $this->authController->updateProfile($_SESSION['user_id'], $updateData);

        if (!$validationResult['success']) {
            $this->formService->addErrors($validationResult['errors']);
        } else {
            $this->formService->setSuccess($validationResult['message']);
            $this->user = $this->userModel->findById($_SESSION['user_id']);
        }
    }
}