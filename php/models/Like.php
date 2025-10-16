<?php

require_once __DIR__ . '/../config/database.php';

class Like {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create(array $likeData): bool {
        try {
            $sql = "INSERT INTO likes (post_id, user_id)
                    VALUES (:post_id, :user_id)";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':post_id' => $likeData['post_id'],
                ':user_id' => $likeData['user_id']
            ]);
        } catch (PDOException $e) {
            error_log("Error creating like: " . $e->getMessage());
            return false;
        }
    }

    public function delete(int $postId, int $userId): bool {
        try {
            $sql = "DELETE FROM likes WHERE post_id = :post_id AND user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':post_id' => $postId,
                ':user_id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Error deleting like: " . $e->getMessage());
            return false;
        }
    }

    public function isLiked(int $postId, int $userId): bool {
        try {
            $sql = "SELECT id FROM likes WHERE post_id = :post_id AND user_id = :user_id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':post_id' => $postId,
                ':user_id' => $userId
            ]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("Error checking like: " . $e->getMessage());
            return false;
        }
    }

    public function getCount(int $postId): int {
        try {
            $sql = "SELECT COUNT(*) as total FROM likes WHERE post_id = :post_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':post_id' => $postId]);
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error counting likes: " . $e->getMessage());
            return 0;
        }
    }
}