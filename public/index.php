<?php
require __DIR__ . '/../vendor/autoload.php';

// Load env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// Define Request Variable
$uri = trim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
$method = $_SERVER['REQUEST_METHOD'];

// Router
if ($uri === 'test' && $method === 'GET') {
    header('Content-Type: application/json');
    echo json_encode(['message' => 'API is running!', 'env' => $_ENV['APP_ENV']]);
} elseif ($uri === 'getuser' && $method === 'GET') {
    

} else {
    // Handle 404 Not Found
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Endpoint Not Found']);
}

exit();
