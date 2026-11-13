<?php

namespace Camagru\Tests\Support;

use RuntimeException;

final class PageTestSupport
{
    public static function request(
        string $path,
        string $method = 'GET',
        array $formData = [],
        ?string $cookie = null
    ): array {
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

        $baseUrl = getenv('CAMAGRU_BASE_URL');
        if ($baseUrl === false || trim($baseUrl) === '') {
            $baseUrl = 'http://localhost:8080';
        }

        $context = stream_context_create($options);
        $body = @file_get_contents(rtrim($baseUrl, '/') . '/' . ltrim($path, '/'), false, $context);
        $responseHeaders = $http_response_header ?? [];

        if ($body === false) {
            throw new RuntimeException(
                'Could not reach the application. Start the project and check CAMAGRU_BASE_URL.'
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

    public static function postWithCsrf(string $path, array $formData, ?string $cookie = null): array
    {
        $page = self::request($path, 'GET', [], $cookie);
        if ($page['status'] !== 200) {
            throw new RuntimeException('Expected a page returning HTTP 200 before posting the form.');
        }

        $formData['csrf_token'] = self::csrfToken($page['body']);
        return self::request($path, 'POST', $formData, $page['cookie'] ?? $cookie);
    }

    public static function csrfToken(string $body): string
    {
        if (!preg_match('/name="csrf_token"\s+value="([^"]+)"/', $body, $matches)) {
            throw new RuntimeException('The page did not render a CSRF token.');
        }

        return $matches[1];
    }

    public static function loginAs(string $username, string $password): string
    {
        $page = self::request('login.php');
        $response = self::request(
            'login.php',
            'POST',
            [
                'login_identifier' => $username,
                'password' => $password,
                'csrf_token' => self::csrfToken($page['body']),
            ],
            $page['cookie']
        );

        if ($response['status'] !== 302 || $response['location'] !== 'index.php' || $response['cookie'] === null) {
            throw new RuntimeException('Could not log in with the page test account.');
        }

        return $response['cookie'];
    }

    public static function createUser(
        bool $verified = true,
        bool $emailNotifications = true,
        ?string $verificationToken = null,
        ?string $resetToken = null
    ): array {
        $username = 'page_test_' . bin2hex(random_bytes(6));
        $email = $username . '@example.invalid';
        $password = 'PageTestPass1!';
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        if ($passwordHash === false) {
            throw new RuntimeException('Could not hash a page test password.');
        }

        $verifiedSql = $verified ? 'TRUE' : 'FALSE';
        $notificationsSql = $emailNotifications ? 'TRUE' : 'FALSE';
        $verificationTokenSql = $verificationToken === null ? 'NULL' : self::quote($verificationToken);
        $resetTokenSql = $resetToken === null ? 'NULL' : self::quote($resetToken);
        $sql = 'INSERT INTO users (username, email, password_hash, is_verified, email_notifications, ' .
            'email_verification_token, password_reset_token, password_reset_expires) VALUES (' .
            self::quote($username) . ', ' . self::quote($email) . ', ' . self::quote($passwordHash) . ', ' .
            $verifiedSql . ', ' . $notificationsSql . ', ' . $verificationTokenSql . ', ' . $resetTokenSql . ', ' .
            ($resetToken === null ? 'NULL' : "CURRENT_TIMESTAMP + INTERVAL '1 hour'") . ') RETURNING id;';

        $output = self::databaseSql($sql);
        $userId = strtok($output, "\n");
        if (!is_string($userId) || !ctype_digit($userId)) {
            throw new RuntimeException('Could not create a page test account.');
        }

        return [
            'id' => (int)$userId,
            'username' => $username,
            'email' => $email,
            'password' => $password,
        ];
    }

    public static function createPost(int $userId, string $caption, bool $published = true, int $sortOrder = 0): int
    {
        $publishedSql = $published ? 'TRUE' : 'FALSE';
        $sql = 'INSERT INTO posts (user_id, image_path, caption, is_published, created_at) VALUES (' .
            $userId . ', ' . self::quote('assets/stickers/neutral-face.png') . ', ' . self::quote($caption) . ', ' .
            $publishedSql . ", CURRENT_TIMESTAMP + INTERVAL '1 day' + INTERVAL '{$sortOrder} seconds') RETURNING id;";
        $output = self::databaseSql($sql);
        $postId = strtok($output, "\n");

        if (!is_string($postId) || !ctype_digit($postId)) {
            throw new RuntimeException('Could not create a page test photo.');
        }

        return (int)$postId;
    }

    public static function createComment(int $userId, int $postId, string $content): int
    {
        $output = self::databaseSql(
            'INSERT INTO comments (user_id, post_id, content) VALUES (' .
            $userId . ', ' . $postId . ', ' . self::quote($content) . ') RETURNING id;'
        );
        $commentId = strtok($output, "\n");

        if (!is_string($commentId) || !ctype_digit($commentId)) {
            throw new RuntimeException('Could not create a page test comment.');
        }

        return (int)$commentId;
    }

    public static function deleteUser(int $userId): void
    {
        self::databaseSql('DELETE FROM users WHERE id = ' . $userId . ';');
    }

    public static function databaseSql(string $sql): string
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
            throw new RuntimeException('Database test fixture failed: ' . trim($error));
        }

        return trim($output);
    }

    public static function assertContains(string $expectedText, string $body): void
    {
        if (!str_contains($body, $expectedText)) {
            throw new RuntimeException('Expected response to contain: ' . $expectedText);
        }
    }

    public static function assertNotContains(string $unexpectedText, string $body): void
    {
        if (str_contains($body, $unexpectedText)) {
            throw new RuntimeException('Expected response not to contain: ' . $unexpectedText);
        }
    }

    private static function quote(string $value): string
    {
        return "'" . str_replace("'", "''", $value) . "'";
    }
}
