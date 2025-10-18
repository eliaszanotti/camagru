<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../services/FormService.php';

class LoginHandler {
    private AuthController $authController;
    private FormService $formService;
    private string $redirectTo;

    public function __construct() {
        AuthMiddleware::requireGuest();

        $this->authController = new AuthController();
        $this->formService = FormService::create();
        $this->redirectTo = $_SESSION['redirect_after_login'] ?? 'index.php';
        unset($_SESSION['redirect_after_login']);

        $this->handleRequest();
    }

    public function getFormService(): FormService {
        return $this->formService;
    }

    public function shouldRedirect(): bool {
        return $this->formService->hasSuccess();
    }

    public function getRedirectUrl(): string {
        return $this->redirectTo;
    }

    private function handleRequest(): void {
        if (isset($_GET['timeout'])) {
            $this->formService->addError('general', 'Your session has expired. Please login again.');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        AuthMiddleware::requireCSRF();

        if (!AuthMiddleware::checkLoginRateLimit()) {
            $this->formService->addError('general', 'Too many login attempts. Please try again later.');
            return;
        }

        $result = $this->authController->login($_POST);

        if (!$result['success']) {
            $this->formService->addErrors($result['errors']);
        } else {
            AuthMiddleware::clearLoginRateLimit();
            $this->formService->setSuccess($result['message']);
        }
    }
}