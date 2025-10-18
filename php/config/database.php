<?php

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            try {
                // Load environment variables from .env file
                $envFile = __DIR__ . '/../.env';
                $env = [];

                if (file_exists($envFile)) {
                    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                    foreach ($lines as $line) {
                        if (strpos($line, '=') !== false && !str_starts_with($line, '#')) {
                            list($key, $value) = explode('=', $line, 2);
                            $env[trim($key)] = trim($value);
                        }
                    }
                }

                // Parse DATABASE_URI if it exists, otherwise use individual vars
                if (isset($env['DATABASE_URI'])) {
                    $uri = $env['DATABASE_URI'];
                    // postgresql://username:password@host:port/database
                    $pattern = '/postgresql:\/\/([^:]+):([^@]+)@([^:]+):(\d+)\/(.+)/';
                    if (preg_match($pattern, $uri, $matches)) {
                        $dbname = $matches[5];
                        $host = $matches[3];
                        $port = $matches[4];
                        $user = $matches[1];
                        $password = $matches[2];
                    } else {
                        throw new Exception("Invalid DATABASE_URI format");
                    }
                } else {
                    $dbname = $env['DB_NAME'] ?? 'camagru';
                    $host = $env['DB_HOST'] ?? 'localhost';
                    $port = $env['DB_PORT'] ?? '5432';
                    $user = $env['DB_USER'] ?? 'postgres';
                    $password = $env['DB_PASSWORD'] ?? '';
                }

                // Create PDO connection
                $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
                self::$instance = new PDO($dsn, $user, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]);

            } catch (PDOException $e) {
                die("❌ Database connection failed: " . $e->getMessage());
            }
        }

        return self::$instance;
    }

    public static function closeConnection(): void {
        self::$instance = null;
    }
}