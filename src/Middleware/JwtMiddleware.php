<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class JwtMiddleware
{
    private string $jwtSecret;

    public function __construct()
    {
        $this->jwtSecret = $_ENV['JWT_SECRET'] ?? 'default_secret_fallback';
    }

    public function authenticate(): ?array
    {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Missing or invalid Authorization header']);
            return null;
        }

        $jwt = $matches[1];

        try {
            $decoded = JWT::decode($jwt, new Key($this->jwtSecret, 'HS256'));
            
            // Return the user ID from the token
            return [
                'user_id' => $decoded->data->user_id,
                'exp' => $decoded->exp
            ];
        } catch (ExpiredException $e) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Token has expired']);
            return null;
        } catch (SignatureInvalidException $e) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid token signature']);
            return null;
        } catch (\Exception $e) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid token']);
            return null;
        }
    }
}