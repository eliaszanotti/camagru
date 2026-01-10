<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../services/FormService.php';

class ResetPasswordHandler {
    private AuthController $authController;
    private FormService $formService;
    private ?array $user;

    public function __construct(string $token) {
        AuthMiddleware::requireGuest();

        $this->authController = new AuthController();
        $this->formService = FormService::create();
        $this->user = $this->validateToken($token);

        $this->handleRequest();
    }

    public function getFormService(): FormService {
        return $this->formService;
    }

    public function isTokenValid(): bool {
        return $this->user !== null;
    }

    public function shouldRedirect(): bool {
        return $this->formService->hasSuccess();
    }

    private function validateToken(string $token): ?array {
        require_once __DIR__ . '/../models/User.php';
        $userModel = new User();

        if (empty($token)) {
            $this->formService->addError('general', 'Invalid reset token');
            return null;
        }

        $user = $userModel->findByPasswordResetToken($token);

        if (!$user) {
            $this->formService->addError('general', 'Invalid or expired reset token');
            return null;
        }

        return $user;
    }

    private function handleRequest(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        if (!$this->isTokenValid()) {
            return;
        }

        AuthMiddleware::requireCSRF();

        $data = [
            'token' => $_GET['token'] ?? '',
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? ''
        ];

        $result = $this->authController->resetPassword($data);

        if (!$result['success']) {
            $this->formService->addErrors($result['errors']);
        } else {
            $this->formService->setSuccess($result['message']);
        }
    }
}
