<?php

namespace App\Controllers;

use App\Services\AuthService;

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function register()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON input.']);
            return;
        }

        $result = $this->authService->registerUser($input);

        if ($result['success']) {
            http_response_code(201); // 201 Created
            echo json_encode([
                'status' => 'success',
                'user_id' => $result['user_id'],
                'token' => $result['token']
            ]);
        } else {
            http_response_code(409); // 409 Conflict (for duplicate user) or 400
            if ($result['message'] === 'Email or username already in use.') {
                http_response_code(409);
            } else {
                http_response_code(400);
            }
            echo json_encode(['error' => $result['message']]);
        }
    }

    public function login()
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || empty($input['identifier']) || empty($input['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Identifier and password are required.']);
            return;
        }

        $identifier = $input['identifier'];
        $password = $input['password'];

        $result = $this->authService->loginUser($identifier, $password);

        if ($result['success']) {
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'user_id' => $result['user_id'],
                'username' => $result['username'],
                'token' => $result['token']
            ]);
        } else {
            http_response_code(401); // 401 Unauthorized
            echo json_encode(['error' => $result['message']]);
        }
    }
}
