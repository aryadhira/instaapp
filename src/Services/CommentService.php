<?php

namespace App\Services;

use App\DB\DBConnector;
use Ramsey\Uuid\Uuid;
use PDO;

class CommentService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DBConnector::getConnection();
    }

    public function createComment(array $commentData, string $userId): array
    {
        try {
            // Check if the post exists
            $stmt = $this->pdo->prepare("SELECT id FROM posts WHERE id = ?");
            $stmt->execute([$commentData['post_id']]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$post) {
                return [
                    'success' => false,
                    'message' => 'Post does not exist'
                ];
            }

            // Generate a new UUID for the comment
            $commentId = Uuid::uuid4()->toString();

            $sql = "INSERT INTO comments (id, user_id, post_id, content) VALUES (?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                $commentId,
                $userId,
                $commentData['post_id'],
                $commentData['content']
            ]);

            return [
                'success' => true,
                'comment_id' => $commentId
            ];
        } catch (\PDOException $e) {
            error_log("Comment creation error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error during comment creation: ' . $e->getMessage()
            ];
        }
    }

    public function editComment(array $commentData, string $userId): array
    {
        try {
            // Check if the comment exists and belongs to the user
            $stmt = $this->pdo->prepare("
                SELECT id FROM comments 
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$commentData['comment_id'], $userId]);
            $comment = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$comment) {
                return [
                    'success' => false,
                    'message' => 'Comment does not exist or does not belong to the user'
                ];
            }

            // Update the comment content
            $sql = "UPDATE comments SET content = ? WHERE id = ? AND user_id = ?";
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                $commentData['new_comment'],
                $commentData['comment_id'],
                $userId
            ]);

            if ($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Comment updated successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to update comment'
                ];
            }
        } catch (\PDOException $e) {
            error_log("Comment update error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error during comment update: ' . $e->getMessage()
            ];
        }
    }

    public function deleteComment(array $commentData, string $userId): array
    {
        try {
            // Check if the comment exists and belongs to the user
            $stmt = $this->pdo->prepare("
                SELECT id FROM comments 
                WHERE id = ? AND user_id = ?
            ");
            $stmt->execute([$commentData['comment_id'], $userId]);
            $comment = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$comment) {
                return [
                    'success' => false,
                    'message' => 'Comment does not exist or does not belong to the user'
                ];
            }

            // Delete the comment
            $sql = "DELETE FROM comments WHERE id = ? AND user_id = ?";
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                $commentData['comment_id'],
                $userId
            ]);

            if ($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'message' => 'Comment deleted successfully'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to delete comment'
                ];
            }
        } catch (\PDOException $e) {
            error_log("Comment deletion error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error during comment deletion: ' . $e->getMessage()
            ];
        }
    }
}