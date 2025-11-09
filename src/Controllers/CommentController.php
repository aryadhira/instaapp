<?php

namespace App\Controllers;

use App\Services\CommentService;

class CommentController
{
    private CommentService $commentService;

    public function __construct()
    {
        $this->commentService = new CommentService();
    }

    public function createComment($userId)
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON input.']);
            return;
        }

        if (!isset($input['post_id']) || !isset($input['content']) || empty($input['content'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Post ID and content are required.']);
            return;
        }

        $result = $this->commentService->createComment($input, $userId);

        if ($result['success']) {
            http_response_code(201);
            echo json_encode([
                'status' => 'success',
                'message' => 'Comment created successfully',
                'comment_id' => $result['comment_id']
            ]);
        } else {
            if (strpos($result['message'], 'Post does not exist') !== false) {
                http_response_code(404);
            } else {
                http_response_code(500);
            }
            echo json_encode(['error' => $result['message']]);
        }
    }

    public function editComment($userId)
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON input.']);
            return;
        }

        if (!isset($input['comment_id']) || !isset($input['new_comment']) || empty($input['new_comment'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Comment ID and new comment content are required.']);
            return;
        }

        $result = $this->commentService->editComment($input, $userId);

        if ($result['success']) {
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => $result['message']
            ]);
        } else {
            if (strpos($result['message'], 'does not exist') !== false) {
                http_response_code(404);
            } else {
                http_response_code(400);
            }
            echo json_encode(['error' => $result['message']]);
        }
    }

    public function deleteComment($userId)
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON input.']);
            return;
        }

        if (!isset($input['comment_id']) || empty($input['comment_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Comment ID is required.']);
            return;
        }

        $result = $this->commentService->deleteComment($input, $userId);

        if ($result['success']) {
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => $result['message']
            ]);
        } else {
            if (strpos($result['message'], 'does not exist') !== false) {
                http_response_code(404);
            } else {
                http_response_code(400);
            }
            echo json_encode(['error' => $result['message']]);
        }
    }
}