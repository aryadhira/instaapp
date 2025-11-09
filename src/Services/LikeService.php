<?php

namespace App\Services;

use App\DB\DBConnector;
use Ramsey\Uuid\Uuid;
use PDO;

class LikeService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DBConnector::getConnection();
    }

    public function toggleLike(array $likeData, string $userId): array
    {
        try {
            // Check if the user has already liked the post
            $checkStmt = $this->pdo->prepare("
                SELECT id FROM likes 
                WHERE post_id = ? AND user_id = ?
            ");
            $checkStmt->execute([$likeData['post_id'], $userId]);
            $existingLike = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($existingLike) {
                // User has already liked the post, so unlike it
                return $this->unlikePost($likeData['post_id'], $userId);
            } else {
                // User hasn't liked the post yet, so like it
                return $this->likePost($likeData['post_id'], $userId);
            }
        } catch (\PDOException $e) {
            error_log("Like toggle error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error during like/unlike operation: ' . $e->getMessage()
            ];
        }
    }

    private function likePost(string $postId, string $userId): array
    {
        try {
            // Generate a new UUID for the like
            $likeId = Uuid::uuid4()->toString();

            $sql = "INSERT INTO likes (id, user_id, post_id) VALUES (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                $likeId,
                $userId,
                $postId
            ]);

            return [
                'success' => true,
                'message' => 'Post liked successfully'
            ];
        } catch (\PDOException $e) {
            // Handle unique constraint violation (user tried to like the same post twice)
            if ($e->getCode() == 23505) {
                return [
                    'success' => false,
                    'message' => 'User already liked this post'
                ];
            }
            error_log("Like post error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error during like operation: ' . $e->getMessage()
            ];
        }
    }

    public function unlikePost(string $postId, string $userId): array
    {
        try {
            // Delete the like record
            $sql = "DELETE FROM likes WHERE post_id = ? AND user_id = ?";
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                $postId,
                $userId
            ]);

            if ($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Post unliked successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Like not found or user does not own the like'
                ];
            }
        } catch (\PDOException $e) {
            error_log("Unlike post error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error during unlike operation: ' . $e->getMessage()
            ];
        }
    }
}