<?php

function registerPageBaseUrl(): string
{
    $baseUrl = getenv('CAMAGRU_BASE_URL');
    if ($baseUrl === false || trim($baseUrl) === '') {
        $baseUrl = 'http://localhost:8080';
    }

    return rtrim($baseUrl, '/');
}

function requestRegisterPage(string $method, array $formData = [], ?string $cookie = null): array
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
    $body = @file_get_contents(registerPageBaseUrl() . '/register.php', false, $context);
    $responseHeaders = $http_response_header ?? [];

    if ($body === false) {
        throw new RuntimeException(
            'Could not reach the registration page. Start the project and check CAMAGRU_BASE_URL.'
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

function submitRegisterPageForm(array $formData): array
{
    $page = requestRegisterPage('GET');
    if ($page['status'] !== 200) {
        throw new RuntimeException('Expected the registration page to return HTTP 200.');
    }

    if (!preg_match('/name="csrf_token"\s+value="([^"]+)"/', $page['body'], $matches)) {
        throw new RuntimeException('The registration form did not render a CSRF token.');
    }

    $formData['csrf_token'] = $matches[1];
    return requestRegisterPage('POST', $formData, $page['cookie']);
}

function assertRegisterPageResponseContains(array $response, string $expectedText): void
{
    if (!str_contains($response['body'], $expectedText)) {
        throw new RuntimeException('Expected response to contain: ' . $expectedText);
    }
}

function validRegisterPageFormData(): array
{
    return [
        'username' => 'camagru_test_user',
        'email' => 'camagru-test@example.com',
        'password' => 'StrongPass1!',
        'confirm_password' => 'StrongPass1!',
    ];
}

return [
    'renders the registration form and CSRF token' => static function (): void {
        $response = requestRegisterPage('GET');

        if ($response['status'] !== 200) {
            throw new RuntimeException('Expected HTTP 200, got HTTP ' . $response['status'] . '.');
        }

        assertRegisterPageResponseContains($response, 'Register');
        assertRegisterPageResponseContains($response, 'name="username"');
        assertRegisterPageResponseContains($response, 'name="email"');
        assertRegisterPageResponseContains($response, 'name="password"');
        assertRegisterPageResponseContains($response, 'name="confirm_password"');
        assertRegisterPageResponseContains($response, 'name="csrf_token"');
    },

    'shows an error for an invalid email address' => static function (): void {
        $formData = validRegisterPageFormData();
        $formData['email'] = 'not-an-email';
        $response = submitRegisterPageForm($formData);

        if ($response['status'] !== 200) {
            throw new RuntimeException('Expected HTTP 200, got HTTP ' . $response['status'] . '.');
        }

        assertRegisterPageResponseContains($response, 'Invalid email format');
    },

    'shows an error when the email address is missing' => static function (): void {
        $formData = validRegisterPageFormData();
        $formData['email'] = '';
        $response = submitRegisterPageForm($formData);

        assertRegisterPageResponseContains($response, 'Email is required');
    },

    'shows an error when the username is missing' => static function (): void {
        $formData = validRegisterPageFormData();
        $formData['username'] = '';
        $response = submitRegisterPageForm($formData);

        assertRegisterPageResponseContains($response, 'Username is required');
    },

    'shows an error when the username is too short' => static function (): void {
        $formData = validRegisterPageFormData();
        $formData['username'] = 'ab';
        $response = submitRegisterPageForm($formData);

        assertRegisterPageResponseContains($response, 'Username must be at least 3 characters long');
    },

    'shows an error for a username with unsupported characters' => static function (): void {
        $formData = validRegisterPageFormData();
        $formData['username'] = 'invalid username';
        $response = submitRegisterPageForm($formData);

        assertRegisterPageResponseContains($response, 'Username can only contain letters, numbers, and underscores');
    },

    'shows an error when the password is missing' => static function (): void {
        $formData = validRegisterPageFormData();
        $formData['password'] = '';
        $formData['confirm_password'] = '';
        $response = submitRegisterPageForm($formData);

        assertRegisterPageResponseContains($response, 'Password is required');
    },

    'shows an error when the password is shorter than eight characters' => static function (): void {
        $formData = validRegisterPageFormData();
        $formData['password'] = 'Pass1!';
        $formData['confirm_password'] = 'Pass1!';
        $response = submitRegisterPageForm($formData);

        assertRegisterPageResponseContains($response, 'Password must be at least 8 characters long');
    },

    'shows an error for a weak password' => static function (): void {
        $formData = validRegisterPageFormData();
        $formData['password'] = 'password';
        $formData['confirm_password'] = 'password';
        $response = submitRegisterPageForm($formData);

        assertRegisterPageResponseContains($response, 'Password must contain at least one uppercase letter');
    },

    'shows an error when the password has no lowercase letter' => static function (): void {
        $formData = validRegisterPageFormData();
        $formData['password'] = 'PASSWORD1!';
        $formData['confirm_password'] = 'PASSWORD1!';
        $response = submitRegisterPageForm($formData);

        assertRegisterPageResponseContains($response, 'Password must contain at least one lowercase letter');
    },

    'shows an error when the password has no number' => static function (): void {
        $formData = validRegisterPageFormData();
        $formData['password'] = 'StrongPass!';
        $formData['confirm_password'] = 'StrongPass!';
        $response = submitRegisterPageForm($formData);

        assertRegisterPageResponseContains($response, 'Password must contain at least one number');
    },

    'shows an error when the password has no special character' => static function (): void {
        $formData = validRegisterPageFormData();
        $formData['password'] = 'StrongPass1';
        $formData['confirm_password'] = 'StrongPass1';
        $response = submitRegisterPageForm($formData);

        assertRegisterPageResponseContains($response, 'Password must contain at least one special character');
    },

    'shows an error when password confirmation does not match' => static function (): void {
        $formData = validRegisterPageFormData();
        $formData['confirm_password'] = 'DifferentPass2!';
        $response = submitRegisterPageForm($formData);

        assertRegisterPageResponseContains($response, 'Passwords do not match');
    },

    'rejects a registration POST without a CSRF token' => static function (): void {
        $page = requestRegisterPage('GET');
        $response = requestRegisterPage('POST', validRegisterPageFormData(), $page['cookie']);

        if ($response['status'] !== 403) {
            throw new RuntimeException('Expected HTTP 403, got HTTP ' . $response['status'] . '.');
        }

        assertRegisterPageResponseContains($response, 'CSRF token validation failed');
    },

    'rejects a registration POST with an invalid CSRF token' => static function (): void {
        $page = requestRegisterPage('GET');
        $formData = validRegisterPageFormData();
        $formData['csrf_token'] = 'invalid-token';
        $response = requestRegisterPage('POST', $formData, $page['cookie']);

        if ($response['status'] !== 403) {
            throw new RuntimeException('Expected HTTP 403, got HTTP ' . $response['status'] . '.');
        }

        assertRegisterPageResponseContains($response, 'CSRF token validation failed');
    },
];
