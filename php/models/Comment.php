<?php

require_once __DIR__ . '/../config/database.php';

class Comment {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create(array $commentData): bool {
        try {
            $sql = "INSERT INTO comments (post_id, user_id, content)
                    VALUES (:post_id, :user_id, :content)";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':post_id' => $commentData['post_id'],
                ':user_id' => $commentData['user_id'],
                ':content' => $commentData['content']
            ]);
        } catch (PDOException $e) {
            error_log("Error creating comment: " . $e->getMessage());
            return false;
        }
    }

    public function getByPostId(int $postId): array {
        try {
            $sql = "SELECT c.*, u.username
                    FROM comments c
                    JOIN users u ON c.user_id = u.id
                    WHERE c.post_id = :post_id
                    ORDER BY c.created_at DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':post_id' => $postId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting comments: " . $e->getMessage());
            return [];
        }
    }

    public function delete(int $commentId, int $userId): bool {
        try {
            $sql = "DELETE FROM comments WHERE id = :id AND user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $commentId,
                ':user_id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Error deleting comment: " . $e->getMessage());
            return false;
        }
    }
}