<?php

namespace App\Services;

use App\DB\DBConnector;
use Ramsey\Uuid\Uuid;
use PDO;

class PostService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DBConnector::getConnection();
    }

    public function createPost(string $postId,array $postData, string $userId, ?string $imagePath = null): array
    {
        try {
            $sql = "INSERT INTO posts (id, user_id, content, image_url) VALUES (?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                $postId,
                $userId,
                $postData['content'] ?? '',
                $imagePath
            ]);

            return [
                'success' => true,
                'post_id' => $postId
            ];
        } catch (\PDOException $e) {
            error_log("Post creation error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error during post creation: ' . $e->getMessage()
            ];
        }
    }

    public function getAllPosts(): array
    {
        try {
            // Fetch all posts with post owner's username
            $postsSql = "
                SELECT
                    p.id as post_id,
                    p.user_id,
                    u.username as post_username,
                    p.content,
                    p.image_url
                FROM posts p
                LEFT JOIN users u ON p.user_id = u.id
                ORDER BY p.created_at DESC
            ";

            $postsStmt = $this->pdo->prepare($postsSql);
            $postsStmt->execute();
            $posts = $postsStmt->fetchAll(PDO::FETCH_ASSOC);

            $result = [];

            foreach ($posts as $post) {
                $postId = $post['post_id'];

                // Get likes for this post with usernames
                $likesSql = "
                    SELECT
                        l.user_id,
                        u.username
                    FROM likes l
                    LEFT JOIN users u ON l.user_id = u.id
                    WHERE l.post_id = ?
                ";
                $likesStmt = $this->pdo->prepare($likesSql);
                $likesStmt->execute([$postId]);
                $likes = $likesStmt->fetchAll(PDO::FETCH_ASSOC);

                // Get comments for this post with usernames, ordered by newest first
                $commentsSql = "
                    SELECT
                        c.user_id,
                        u.username,
                        c.content
                    FROM comments c
                    LEFT JOIN users u ON c.user_id = u.id
                    WHERE c.post_id = ?
                    ORDER BY c.created_at DESC
                ";
                $commentsStmt = $this->pdo->prepare($commentsSql);
                $commentsStmt->execute([$postId]);
                $comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

                $result[] = [
                    'post_id' => $post['post_id'],
                    'user_id' => $post['user_id'],
                    'username' => $post['post_username'],
                    'content' => $post['content'],
                    'image_url' => $post['image_url'],
                    'likes' => $likes,
                    'comments' => $comments
                ];
            }

            return [
                'success' => true,
                'posts' => $result
            ];
        } catch (\PDOException $e) {
            error_log("Fetch all posts error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error during fetching posts: ' . $e->getMessage()
            ];
        }
    }
}