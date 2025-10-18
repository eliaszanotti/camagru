<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../services/FormService.php';

class ForgotPasswordHandler {
    private AuthController $authController;
    private FormService $formService;

    public function __construct() {
        AuthMiddleware::requireGuest();

        $this->authController = new AuthController();
        $this->formService = FormService::create();

        $this->handleRequest();
    }

    public function getFormService(): FormService {
        return $this->formService;
    }

    public function shouldShowForm(): bool {
        return !$this->formService->hasSuccess();
    }

    private function handleRequest(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $data = [
            'email' => trim($_POST['email'] ?? '')
        ];

        $result = $this->authController->requestPasswordReset($data);

        if (!$result['success']) {
            $this->formService->addErrors($result['errors']);
        } else {
            $this->formService->setSuccess($result['message']);
        }
    }
}