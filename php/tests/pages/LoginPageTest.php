<?php

function loginPageBaseUrl(): string
{
    $baseUrl = getenv('CAMAGRU_BASE_URL');
    if ($baseUrl === false || trim($baseUrl) === '') {
        $baseUrl = 'http://localhost:8080';
    }

    return rtrim($baseUrl, '/');
}

function requestLoginPage(string $path, string $method = 'GET', array $formData = [], ?string $cookie = null): array
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
    $body = @file_get_contents(loginPageBaseUrl() . '/' . ltrim($path, '/'), false, $context);
    $responseHeaders = $http_response_header ?? [];

    if ($body === false) {
        throw new RuntimeException(
            'Could not reach the login page. Start the project and check CAMAGRU_BASE_URL.'
        );
    }

    $statusCode = 0;
    $responseCookie = null;
    $location = null;
    foreach ($responseHeaders as $header) {
        if ($statusCode === 0 && preg_match('/^HTTP\/\S+\s+(\d{3})/', $header, $matches)) {
            $statusCode = (int)$matches[1];
        }

        if ($responseCookie === null && preg_match('/^Set-Cookie:\s*([^;]+)/i', $header, $matches)) {
            $responseCookie = $matches[1];
        }

        if ($location === null && preg_match('/^Location:\s*(.+)$/i', $header, $matches)) {
            $location = trim($matches[1]);
        }
    }

    return [
        'status' => $statusCode,
        'body' => $body,
        'cookie' => $responseCookie,
        'location' => $location,
    ];
}

function submitLoginPageForm(array $formData): array
{
    $page = requestLoginPage('login.php');
    if ($page['status'] !== 200) {
        throw new RuntimeException('Expected the login page to return HTTP 200.');
    }

    if (!preg_match('/name="csrf_token"\s+value="([^"]+)"/', $page['body'], $matches)) {
        throw new RuntimeException('The login form did not render a CSRF token.');
    }

    $formData['csrf_token'] = $matches[1];
    return requestLoginPage('login.php', 'POST', $formData, $page['cookie']);
}

function runLoginDatabaseSql(string $sql): string
{
    $process = proc_open(
        [
            'docker', 'compose', 'exec', '-T', 'db',
            'psql', '-U', 'camagru', '-d', 'camagru', '-v', 'ON_ERROR_STOP=1', '-tA',
        ],
        [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ],
        $pipes,
        dirname(__DIR__, 2)
    );

    if (!is_resource($process)) {
        throw new RuntimeException('Could not start psql through Docker Compose.');
    }

    fwrite($pipes[0], $sql);
    fclose($pipes[0]);
    $output = stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $error = stream_get_contents($pipes[2]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);

    if ($exitCode !== 0) {
        throw new RuntimeException('Database fixture command failed: ' . trim($error));
    }

    return trim($output);
}

function createLoginPageFixture(bool $verified): array
{
    $username = 'login_test_' . bin2hex(random_bytes(6));
    $email = $username . '@example.com';
    $password = 'LoginTestPass1!';
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    if ($passwordHash === false) {
        throw new RuntimeException('Could not hash the login test password.');
    }

    $quotedUsername = str_replace("'", "''", $username);
    $quotedEmail = str_replace("'", "''", $email);
    $quotedPasswordHash = str_replace("'", "''", $passwordHash);
    $verifiedValue = $verified ? 'TRUE' : 'FALSE';

    $insertOutput = runLoginDatabaseSql(
        "INSERT INTO users (username, email, password_hash, is_verified) " .
        "VALUES ('{$quotedUsername}', '{$quotedEmail}', '{$quotedPasswordHash}', {$verifiedValue}) " .
        "RETURNING id;"
    );
    $userId = strtok($insertOutput, "\n");

    if (!ctype_digit($userId)) {
        throw new RuntimeException('Could not create a login test user.');
    }

    return [
        'id' => (int)$userId,
        'username' => $username,
        'password' => $password,
    ];
}

function deleteLoginPageFixture(int $userId): void
{
    runLoginDatabaseSql('DELETE FROM users WHERE id = ' . $userId . ';');
}

function assertLoginPageContains(array $response, string $expectedText): void
{
    if (!str_contains($response['body'], $expectedText)) {
        throw new RuntimeException('Expected response to contain: ' . $expectedText);
    }
}

return [
    'renders login fields and password recovery link' => static function (): void {
        $response = requestLoginPage('login.php');

        if ($response['status'] !== 200) {
            throw new RuntimeException('Expected HTTP 200, got HTTP ' . $response['status'] . '.');
        }

        assertLoginPageContains($response, 'name="login_identifier"');
        assertLoginPageContains($response, 'name="password"');
        assertLoginPageContains($response, 'name="csrf_token"');
        assertLoginPageContains($response, 'forgot-password.php');
    },

    'shows errors when username or email and password are missing' => static function (): void {
        $response = submitLoginPageForm([
            'login_identifier' => '',
            'password' => '',
        ]);

        assertLoginPageContains($response, 'Email or username is required');
        assertLoginPageContains($response, 'Password is required');
    },

    'rejects unknown login credentials' => static function (): void {
        $response = submitLoginPageForm([
            'login_identifier' => 'login_unknown_' . bin2hex(random_bytes(6)),
            'password' => 'IncorrectPass1!',
        ]);

        if ($response['status'] !== 200) {
            throw new RuntimeException('Expected HTTP 200, got HTTP ' . $response['status'] . '.');
        }

        assertLoginPageContains($response, 'Invalid username');
    },

    'rejects login POSTs without a CSRF token' => static function (): void {
        $page = requestLoginPage('login.php');
        $response = requestLoginPage(
            'login.php',
            'POST',
            ['login_identifier' => 'some_user', 'password' => 'SomePass1!'],
            $page['cookie']
        );

        if ($response['status'] !== 403) {
            throw new RuntimeException('Expected HTTP 403, got HTTP ' . $response['status'] . '.');
        }
    },

    'rejects login POSTs with an invalid CSRF token' => static function (): void {
        $page = requestLoginPage('login.php');
        $response = requestLoginPage(
            'login.php',
            'POST',
            [
                'login_identifier' => 'some_user',
                'password' => 'SomePass1!',
                'csrf_token' => 'invalid-token',
            ],
            $page['cookie']
        );

        if ($response['status'] !== 403) {
            throw new RuntimeException('Expected HTTP 403, got HTTP ' . $response['status'] . '.');
        }
    },

    'rejects an incorrect password for an existing username' => static function (): void {
        $user = createLoginPageFixture(true);

        try {
            $response = submitLoginPageForm([
                'login_identifier' => $user['username'],
                'password' => 'IncorrectPass1!',
            ]);

            if ($response['status'] !== 200) {
                throw new RuntimeException('Expected invalid credentials to remain on the login page.');
            }

            assertLoginPageContains($response, 'Invalid username');
        } finally {
            deleteLoginPageFixture($user['id']);
        }
    },

    'allows a verified user to sign in with their username and password' => static function (): void {
        $user = createLoginPageFixture(true);

        try {
            $response = submitLoginPageForm([
                'login_identifier' => $user['username'],
                'password' => $user['password'],
            ]);

            if ($response['status'] !== 302 || $response['location'] !== 'index.php') {
                throw new RuntimeException('Expected successful login to redirect to index.php.');
            }

            if ($response['cookie'] === null) {
                throw new RuntimeException('Successful login did not return an authenticated session cookie.');
            }

            $protectedPage = requestLoginPage('gallery.php', 'GET', [], $response['cookie']);
            if ($protectedPage['status'] !== 200) {
                throw new RuntimeException('Expected the authenticated session to access gallery.php.');
            }

            $loginPage = requestLoginPage('login.php', 'GET', [], $response['cookie']);
            if ($loginPage['status'] !== 302 || $loginPage['location'] !== 'index.php') {
                throw new RuntimeException('Expected an authenticated user to be redirected away from login.php.');
            }
        } finally {
            deleteLoginPageFixture($user['id']);
        }
    },

    'does not allow an unverified user to sign in' => static function (): void {
        $user = createLoginPageFixture(false);

        try {
            $response = submitLoginPageForm([
                'login_identifier' => $user['username'],
                'password' => $user['password'],
            ]);

            if ($response['status'] !== 200) {
                throw new RuntimeException('Expected unverified login to remain on the login page.');
            }

            assertLoginPageContains($response, 'Please verify your email before logging in');
        } finally {
            deleteLoginPageFixture($user['id']);
        }
    },
];
