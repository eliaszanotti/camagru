<?php

function forgotPasswordPageBaseUrl(): string
{
    $baseUrl = getenv('CAMAGRU_BASE_URL');
    if ($baseUrl === false || trim($baseUrl) === '') {
        $baseUrl = 'http://localhost:8080';
    }

    return rtrim($baseUrl, '/');
}

function requestForgotPasswordPage(string $method, array $formData = [], ?string $cookie = null): array
{
    $headers = [];
    if ($method === 'POST') {
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    }
    if ($cookie !== null) {
        $headers[] = 'Cookie: ' . $cookie;
    }

    $options = [
        'http' => [
            'method' => $method,
            'header' => implode("\r\n", $headers),
            'ignore_errors' => true,
            'follow_location' => 0,
            'timeout' => 5,
        ],
    ];

    if ($method === 'POST') {
        $options['http']['content'] = http_build_query($formData);
    }

    $context = stream_context_create($options);
    $body = @file_get_contents(forgotPasswordPageBaseUrl() . '/forgot-password.php', false, $context);
    $responseHeaders = $http_response_header ?? [];

    if ($body === false) {
        throw new RuntimeException(
            'Could not reach the password recovery page. Start the project and check CAMAGRU_BASE_URL.'
        );
    }

    $statusCode = 0;
    $responseCookie = null;
    foreach ($responseHeaders as $header) {
        if ($statusCode === 0 && preg_match('/^HTTP\/\S+\s+(\d{3})/', $header, $matches)) {
            $statusCode = (int)$matches[1];
        }

        if ($responseCookie === null && preg_match('/^Set-Cookie:\s*([^;]+)/i', $header, $matches)) {
            $responseCookie = $matches[1];
        }
    }

    return [
        'status' => $statusCode,
        'body' => $body,
        'cookie' => $responseCookie,
    ];
}

function submitForgotPasswordPageForm(array $formData): array
{
    $page = requestForgotPasswordPage('GET');
    if ($page['status'] !== 200) {
        throw new RuntimeException('Expected the password recovery page to return HTTP 200.');
    }

    if (!preg_match('/name="csrf_token"\s+value="([^"]+)"/', $page['body'], $matches)) {
        throw new RuntimeException('The password recovery form did not render a CSRF token.');
    }

    $formData['csrf_token'] = $matches[1];
    return requestForgotPasswordPage('POST', $formData, $page['cookie']);
}

function assertForgotPasswordPageContains(array $response, string $expectedText): void
{
    if (!str_contains($response['body'], $expectedText)) {
        throw new RuntimeException('Expected response to contain: ' . $expectedText);
    }
}

return [
    'renders the email recovery form and a CSRF token' => static function (): void {
        $response = requestForgotPasswordPage('GET');

        if ($response['status'] !== 200) {
            throw new RuntimeException('Expected HTTP 200, got HTTP ' . $response['status'] . '.');
        }

        assertForgotPasswordPageContains($response, 'Forgot Password');
        assertForgotPasswordPageContains($response, 'name="email"');
        assertForgotPasswordPageContains($response, 'name="csrf_token"');
        assertForgotPasswordPageContains($response, 'login.php');
    },

    'shows an error when the email address is missing' => static function (): void {
        $response = submitForgotPasswordPageForm(['email' => '']);

        assertForgotPasswordPageContains($response, 'Email is required');
    },

    'shows an error for an invalid email address' => static function (): void {
        $response = submitForgotPasswordPageForm(['email' => 'not-an-email']);

        assertForgotPasswordPageContains($response, 'Invalid email format');
    },

    'returns a generic response for an unknown email address' => static function (): void {
        $email = 'forgot_unknown_' . bin2hex(random_bytes(6)) . '@example.com';
        $response = submitForgotPasswordPageForm(['email' => $email]);

        if ($response['status'] !== 200) {
            throw new RuntimeException('Expected HTTP 200, got HTTP ' . $response['status'] . '.');
        }

        assertForgotPasswordPageContains($response, 'If the email exists, a reset link has been sent.');
        if (str_contains($response['body'], 'Email is not registered')) {
            throw new RuntimeException('The page must not reveal whether an email address is registered.');
        }
    },

    'rejects a request without a CSRF token' => static function (): void {
        $page = requestForgotPasswordPage('GET');
        $response = requestForgotPasswordPage('POST', ['email' => 'person@example.com'], $page['cookie']);

        if ($response['status'] !== 403) {
            throw new RuntimeException('Expected HTTP 403, got HTTP ' . $response['status'] . '.');
        }

        assertForgotPasswordPageContains($response, 'CSRF token validation failed');
    },

    'rejects a request with an invalid CSRF token' => static function (): void {
        $page = requestForgotPasswordPage('GET');
        $response = requestForgotPasswordPage(
            'POST',
            ['email' => 'person@example.com', 'csrf_token' => 'invalid-token'],
            $page['cookie']
        );

        if ($response['status'] !== 403) {
            throw new RuntimeException('Expected HTTP 403, got HTTP ' . $response['status'] . '.');
        }

        assertForgotPasswordPageContains($response, 'CSRF token validation failed');
    },
];
