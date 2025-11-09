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
} elseif ($uri === 'posts' && $method === 'POST') {
    // Authenticate using JWT
    $middleware = new App\Middleware\JwtMiddleware();
    $authResult = $middleware->authenticate();
    
    if (!$authResult) {
        exit();
    }
    
    $userId = $authResult['user_id'];
    $controller = new App\Controllers\PostController();
    $controller->createPost($userId);
} elseif ($uri === 'comments' && $method === 'POST') {
    // Authenticate using JWT
    $middleware = new App\Middleware\JwtMiddleware();
    $authResult = $middleware->authenticate();
    
    if (!$authResult) {
        exit();
    }
    
    $userId = $authResult['user_id'];
    $controller = new App\Controllers\CommentController();
    $controller->createComment($userId);
} elseif ($uri === 'comments' && $method === 'PUT') {
    // Authenticate using JWT
    $middleware = new App\Middleware\JwtMiddleware();
    $authResult = $middleware->authenticate();
    
    if (!$authResult) {
        exit();
    }
    
    $userId = $authResult['user_id'];
    $controller = new App\Controllers\CommentController();
    $controller->editComment($userId);
} elseif ($uri === 'comments' && $method === 'DELETE') {
    // Authenticate using JWT
    $middleware = new App\Middleware\JwtMiddleware();
    $authResult = $middleware->authenticate();
    
    if (!$authResult) {
        exit();
    }
    
    $userId = $authResult['user_id'];
    $controller = new App\Controllers\CommentController();
    $controller->deleteComment($userId);
} elseif ($uri === 'likes' && $method === 'POST') {
    // Authenticate using JWT
    $middleware = new App\Middleware\JwtMiddleware();
    $authResult = $middleware->authenticate();
    
    if (!$authResult) {
        exit();
    }
    
    $userId = $authResult['user_id'];
    $controller = new App\Controllers\LikeController();
    $controller->toggleLike($userId);
} elseif ($uri === 'likes' && $method === 'DELETE') {
    // Authenticate using JWT
    $middleware = new App\Middleware\JwtMiddleware();
    $authResult = $middleware->authenticate();
    
    if (!$authResult) {
        exit();
    }
    
    $userId = $authResult['user_id'];
    $controller = new App\Controllers\LikeController();
    $controller->unlikePost($userId);
} elseif ($uri === 'posts' && $method === 'GET') {
    $controller = new App\Controllers\PostController();
    $controller->getAllPosts();
} else {
    // Handle 404 Not Found
    http_response_code(404);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Endpoint Not Found']);
}

exit();
