<?php

require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../services/FormService.php';

class LoginHandler {
    private AuthController $authController;
    private FormService $formService;
    private string $redirectTo;
    private bool $loginSucceeded = false;
    private bool $showResendVerification = false;

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
        return $this->loginSucceeded;
    }

    public function getRedirectUrl(): string {
        return $this->redirectTo;
    }

    public function shouldShowResendVerification(): bool {
        return $this->showResendVerification;
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
            $this->formService->addError('general', 'Too many attempts. Please try again later.');
            return;
        }

        if (($_POST['form_type'] ?? '') === 'resend_verification') {
            $grant = $_SESSION['verification_resend_grant'] ?? null;
            if (is_array($grant) && ($grant['expires_at'] ?? 0) >= time()) {
                $this->authController->resendVerification((int) $grant['user_id']);
            }
            unset($_SESSION['verification_resend_grant']);
            $this->formService->setSuccess('If the account is still unverified, a verification email has been sent. Please wait a minute before requesting another.');
            return;
        }

        $result = $this->authController->login($_POST);

        if (!$result['success']) {
            $this->formService->addErrors($result['errors']);
            if (!empty($result['resend_verification_user_id'])) {
                $_SESSION['verification_resend_grant'] = [
                    'user_id' => (int) $result['resend_verification_user_id'],
                    'expires_at' => time() + 300
                ];
                $this->showResendVerification = true;
            }
        } else {
            AuthMiddleware::clearLoginRateLimit();
            $this->loginSucceeded = true;
            $this->formService->setSuccess($result['message']);
        }
    }
}
