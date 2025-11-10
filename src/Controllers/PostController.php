<?php

namespace App\Controllers;

use App\Services\PostService;

class PostController
{
    private PostService $postService;

    public function __construct()
    {
        $this->postService = new PostService();
    }

    public function createPost($userId)
    {
        header('Content-Type: application/json');

        // Check if the request is a multipart form
        if (!isset($_POST) || empty($_POST)) {
            http_response_code(400);
            echo json_encode(['error' => 'No form data provided']);
            return;
        }

        // Get content from the form
        $content = $_POST['content'] ?? null;

        if (empty($content)) {
            http_response_code(400);
            echo json_encode(['error' => 'Content is required']);
            return;
        }

        // We need the post ID for image naming, so we generate it before file upload
        $postId = \Ramsey\Uuid\Uuid::uuid4()->toString();

        // Handle file upload if provided
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = $this->uploadImage($_FILES['image'], $userId, $postId);
            
            if (!$imagePath) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to upload image']);
                return;
            }
        }

        // Create the post
        $result = $this->postService->createPost($postId,[
            'content' => $content
        ], $userId, $imagePath);

        if ($result['success']) {
            http_response_code(201);
            echo json_encode([
                'status' => 'success',
                'message' => 'Post created successfully',
                'post_id' => $result['post_id']
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => $result['message']]);
        }
    }

    public function getAllPosts()
    {
        header('Content-Type: application/json');

        $result = $this->postService->getAllPosts();

        if ($result['success']) {
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'posts' => $result['posts']
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => $result['message']]);
        }
    }

    private function uploadImage($file, string $userId, string $postId): ?string
    {
        $imagePath = $_ENV['IMAGE_PATH'] ?? 'uploads';
        $imageDir = __DIR__ . '/../../' . $imagePath;
        $imageUrl = $_ENV['IMAGE_URL'];

        // Create directory if it doesn't exist
        if (!is_dir($imageDir)) {
            if (!mkdir($imageDir, 0755, true)) {
                error_log("Failed to create image directory: {$imageDir}");
                return null;
            }
        }

        // Get the file extension
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileExtension = strtolower($fileExtension);

        // Validate file extension (basic validation)
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($fileExtension, $allowedExtensions)) {
            error_log("Invalid file extension: {$fileExtension}");
            return null;
        }

        // Generate filename with the format: <user_id>_<post_id>_<timestamp>.extension
        $timestamp = time();
        
        $newFilename = $userId . '_' . $postId . '_' . $timestamp . '.' . $fileExtension;
        $fullImagePath = $imageDir . '/' . $newFilename;

        // Move the uploaded file to the destination
        if (move_uploaded_file($file['tmp_name'], $fullImagePath)) {
            return $imageUrl . '/' . $newFilename;
        } else {
            error_log("Failed to move uploaded file to: {$fullImagePath}");
            return null;
        }
    }
}