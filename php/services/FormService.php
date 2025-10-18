<?php

class FormService {
    private array $errors = [];
    private string $success = '';

    public function addError(string $field, string $message): void {
        $this->errors[$field] = $message;
    }

    public function addErrors(array $errors): void {
        $this->errors = array_merge($this->errors, $errors);
    }

    public function setSuccess(string $message): void {
        $this->success = $message;
    }

    public function hasErrors(): bool {
        return !empty($this->errors);
    }

    public function hasSuccess(): bool {
        return !empty($this->success);
    }

    public function getErrors(): array {
        return $this->errors;
    }

    public function getSuccess(): string {
        return $this->success;
    }

    public function getError(string $field): ?string {
        return $this->errors[$field] ?? null;
    }

    public function clear(): void {
        $this->errors = [];
        $this->success = '';
    }

    public function renderAlerts(): void {
        if ($this->hasErrors()) {
            echo '<div class="alert alert-error mb-6">';
            echo '<ul class="list-disc list-inside">';
            foreach ($this->errors as $error) {
                echo '<li>' . htmlspecialchars($error) . '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }

        if ($this->hasSuccess()) {
            echo '<div class="alert alert-success">';
            echo htmlspecialchars($this->success);
            echo '</div>';
        }
    }

    public function renderFieldError(string $field): void {
        if ($this->getError($field)) {
            echo '<p class="label text-error">' . htmlspecialchars($this->getError($field)) . '</p>';
        }
    }

    public static function create(): self {
        return new self();
    }
}