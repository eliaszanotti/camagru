<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../services/FormService.php';

class VerifyHandler {
    private AuthController $authController;
    private FormService $formService;
    private bool $isVerified;
    private string $message;

    public function __construct(string $token) {
        $this->authController = new AuthController();
        $this->formService = FormService::create();

        $result = $this->authController->verifyEmail($token);
        $this->isVerified = $result['success'];
        $this->message = $result['message'];
    }

    public function getFormService(): FormService {
        return $this->formService;
    }

    public function isVerified(): bool {
        return $this->isVerified;
    }

    public function getMessage(): string {
        return $this->message;
    }
}
