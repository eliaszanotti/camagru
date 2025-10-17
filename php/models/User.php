<?php

require_once __DIR__ . '/../config/database.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create(array $userData): bool {
        try {
            $sql = "INSERT INTO users (username, email, password_hash, email_verification_token)
                    VALUES (:username, :email, :password_hash, :verification_token)";

            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                ':username' => $userData['username'],
                ':email' => $userData['email'],
                ':password_hash' => $userData['password_hash'],
                ':verification_token' => $userData['verification_token'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("Error creating user: " . $e->getMessage());
            return false;
        }
    }

    public function findByEmail(string $email): ?array {
        try {
            $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':email' => $email]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error finding user by email: " . $e->getMessage());
            return null;
        }
    }

    public function findByUsername(string $username): ?array {
        try {
            $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':username' => $username]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error finding user by username: " . $e->getMessage());
            return null;
        }
    }

    public function findById(int $id): ?array {
        try {
            $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error finding user by ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify user email
     */
    public function verifyEmail(string $token): bool {
        try {
            $sql = "UPDATE users
                    SET is_verified = TRUE, email_verification_token = NULL, updated_at = CURRENT_TIMESTAMP
                    WHERE email_verification_token = :token";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':token' => $token]);
        } catch (PDOException $e) {
            error_log("Error verifying email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update password
     */
    public function updatePassword(int $userId, string $passwordHash): bool {
        try {
            $sql = "UPDATE users SET password_hash = :password_hash,
                    password_reset_token = NULL, password_reset_expires = NULL
                    WHERE id = :user_id";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':password_hash' => $passwordHash,
                ':user_id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Error updating password: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create password reset token
     */
    public function createPasswordResetToken(string $email, string $token, string $expires): bool {
        try {
            $sql = "UPDATE users
                    SET password_reset_token = :token, password_reset_expires = :expires
                    WHERE email = :email";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':token' => $token,
                ':expires' => $expires,
                ':email' => $email
            ]);
        } catch (PDOException $e) {
            error_log("Error creating password reset token: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find by password reset token
     */
    public function findByPasswordResetToken(string $token): ?array {
        try {
            $sql = "SELECT * FROM users
                    WHERE password_reset_token = :token
                    AND password_reset_expires > CURRENT_TIMESTAMP
                    LIMIT 1";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':token' => $token]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error finding user by reset token: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(int $userId, array $data): bool {
        try {
            $allowedFields = ['username', 'email', 'email_notifications'];
            $setClauses = [];
            $params = [':user_id' => $userId];

            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $setClauses[] = "$field = :$field";
                    $params[":$field"] = $data[$field];
                }
            }

            if (empty($setClauses)) {
                return false;
            }

            $sql = "UPDATE users SET " . implode(', ', $setClauses) . " WHERE id = :user_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error updating profile: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if email exists
     */
    public function emailExists(string $email): bool {
        return $this->findByEmail($email) !== null;
    }

    /**
     * Check if username exists
     */
    public function usernameExists(string $username): bool {
        return $this->findByUsername($username) !== null;
    }

    /**
     * Get all users (for admin)
     */
    public function getAll(int $limit = 50, int $offset = 0): array {
        try {
            $sql = "SELECT id, username, email, is_verified, created_at
                    FROM users
                    ORDER BY created_at DESC
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting all users: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Delete user (cascade delete will handle related records)
     */
    public function delete(int $userId): bool {
        try {
            $sql = "DELETE FROM users WHERE id = :user_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':user_id' => $userId]);
        } catch (PDOException $e) {
            error_log("Error deleting user: " . $e->getMessage());
            return false;
        }
    }
}