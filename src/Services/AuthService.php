<?php

namespace App\Services;

use App\DB\DBConnector;
use Firebase\JWT\JWT;
use PDO;

class AuthService
{
    private PDO $pdo;
    private string $jwtSecret;

    public function __construct()
    {
        $this->pdo = DBConnector::getConnection();

        $this->jwtSecret = $_ENV['JWT_SECRET'] ?? 'default_secret_fallback';
    }


    private function generateJwt(string $userId): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; // Token valid for 1 hour

        $payload = [
            'iat'  => $issuedAt,           // Issued at
            'exp'  => $expirationTime,     // Expiration time
            'iss'  => 'instaapp',           // Issuer
            'data' => [
                'user_id' => $userId
            ]
        ];

        // Algorithm used: HS256 (requires the secret key)
        return JWT::encode($payload, $this->jwtSecret, 'HS256');
    }

    // --- Core Logic: Register User ---

    public function registerUser(array $data): array
    {
        if (empty($data['email']) || empty($data['username']) || empty($data['password'])) {
            return ['success' => false, 'message' => 'Missing required fields.'];
        }

        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO users (email, username, password_hash) 
                VALUES (:email, :username, :password_hash) 
                RETURNING id; -- Returns the newly created UUID
            ");

            $stmt->execute([
                'email' => $data['email'],
                'username' => $data['username'],
                'password_hash' => $passwordHash
            ]);

            $userId = $stmt->fetchColumn();

            $token = $this->generateJwt($userId);

            return ['success' => true, 'user_id' => $userId, 'token' => $token];
        } catch (\PDOException $e) {
            if ($e->getCode() == '23505') {
                return ['success' => false, 'message' => 'Email or username already in use.'];
            }
            error_log("Registration error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error during registration.'];
        }
    }

    // --- Core Logic: Login User ---

    public function loginUser(string $identifier, string $password): array
    {
        $stmt = $this->pdo->prepare("
            SELECT id, password_hash FROM users 
            WHERE email = :identifier OR username = :identifier
        ");
        $stmt->execute(['identifier' => $identifier]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid credentials.'];
        }

        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Invalid credentials.'];
        }

        $token = $this->generateJwt($user['id']);

        return ['success' => true, 'user_id' => $user['id'], 'token' => $token];
    }
}
