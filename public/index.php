<?php
require __DIR__ . '/../vendor/autoload.php';

// Load env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// Define Request Variable
$uri = trim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
$method = $_SERVER['REQUEST_METHOD'];

// Router
if ($uri === 'register' && $method === 'POST') {
    $controller = new App\Controllers\AuthController();
    $controller->register();
} elseif ($uri === 'login' && $method === 'POST') {
    $controller = new App\Controllers\AuthController();
    $controller->login();
} else {
    // Handle 404 Not Found
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Endpoint Not Found']);
}

exit();
