<?php

require_once __DIR__ . '/../config/database.php';

class Post {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create(array $postData): bool {
        try {
            $sql = "INSERT INTO posts (user_id, image_path, caption, is_published)
                    VALUES (:user_id, :image_path, :caption, :is_published)";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':user_id' => $postData['user_id'],
                ':image_path' => $postData['image_path'],
                ':caption' => $postData['caption'] ?? '',
                ':is_published' => $postData['is_published'] ?? true
            ]);
        } catch (PDOException $e) {
            error_log("Error creating post: " . $e->getMessage());
            return false;
        }
    }

    public function getAll(int $page = 1, int $limit = 9): array {
        try {
            $offset = ($page - 1) * $limit;
            $sql = "SELECT p.*, u.username
                    FROM posts p
                    JOIN users u ON p.user_id = u.id
                    WHERE p.is_published = true
                    ORDER BY p.created_at DESC
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting posts: " . $e->getMessage());
            return [];
        }
    }

    public function getByUserId(int $userId): array {
        try {
            $sql = "SELECT * FROM posts WHERE user_id = :user_id ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting user posts: " . $e->getMessage());
            return [];
        }
    }

    public function findById(int $id): ?array {
        try {
            $sql = "SELECT p.*, u.username
                    FROM posts p
                    JOIN users u ON p.user_id = u.id
                    WHERE p.id = :id LIMIT 1";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error finding post: " . $e->getMessage());
            return null;
        }
    }

    public function delete(int $postId, int $userId): bool {
        try {
            $sql = "DELETE FROM posts WHERE id = :id AND user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $postId,
                ':user_id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Error deleting post: " . $e->getMessage());
            return false;
        }
    }

    public function getTotalCount(): int {
        try {
            $sql = "SELECT COUNT(*) as total FROM posts WHERE is_published = true";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error counting posts: " . $e->getMessage());
            return 0;
        }
    }
}