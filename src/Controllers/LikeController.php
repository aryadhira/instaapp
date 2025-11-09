<?php

namespace App\Controllers;

use App\Services\LikeService;

class LikeController
{
    private LikeService $likeService;

    public function __construct()
    {
        $this->likeService = new LikeService();
    }

    public function toggleLike($userId)
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON input.']);
            return;
        }

        if (!isset($input['post_id']) || empty($input['post_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Post ID is required.']);
            return;
        }

        $result = $this->likeService->toggleLike($input, $userId);

        if ($result['success']) {
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => $result['message']
            ]);
        } else {
            if (strpos($result['message'], 'not found') !== false) {
                http_response_code(404);
            } else {
                http_response_code(400);
            }
            echo json_encode(['error' => $result['message']]);
        }
    }

    public function unlikePost($userId)
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON input.']);
            return;
        }

        if (!isset($input['post_id']) || empty($input['post_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Post ID is required.']);
            return;
        }

        $result = $this->likeService->unlikePost($input['post_id'], $userId);

        if ($result['success']) {
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => $result['message']
            ]);
        } else {
            if (strpos($result['message'], 'not found') !== false) {
                http_response_code(404);
            } else {
                http_response_code(400);
            }
            echo json_encode(['error' => $result['message']]);
        }
    }
}