<?php

require_once __DIR__ . '/../config/database.php';

class CapturedImage
{
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function create(array $imageData): int
    {
        try {
            $sql = "INSERT INTO captured_images (user_id, image_path, source, created_at)
                    VALUES (:user_id, :image_path, :source, NOW())";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id' => $imageData['user_id'],
                ':image_path' => $imageData['image_path'],
                ':source' => $imageData['source']
            ]);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating captured image: " . $e->getMessage());
            return 0;
        }
    }

    public function findById(int $id, int $userId): ?array
    {
        try {
            $sql = "SELECT * FROM captured_images WHERE id = :id AND user_id = :user_id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id, ':user_id' => $userId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error finding captured image: " . $e->getMessage());
            return null;
        }
    }

    public function getByUserId(int $userId): array
    {
        try {
            $sql = "SELECT * FROM captured_images WHERE user_id = :user_id ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting user captured images: " . $e->getMessage());
            return [];
        }
    }

    public function delete(int $imageId, int $userId): bool
    {
        try {
            // Get image info first to delete file
            $image = $this->findById($imageId, $userId);
            if ($image) {
                $filePath = __DIR__ . '/../..' . $image['image_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $sql = "DELETE FROM captured_images WHERE id = :id AND user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $imageId,
                ':user_id' => $userId
            ]);
        } catch (PDOException $e) {
            error_log("Error deleting captured image: " . $e->getMessage());
            return false;
        }
    }
}