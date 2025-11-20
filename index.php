<?php
// index.php - Main entry point with routing

require_once 'config.php';
require_once 'controllers/ProductController.php';
require_once 'controllers/CategoryController.php';
require_once 'controllers/CartController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/OrdersController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simple router
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);

// Clean the path
$path = str_replace($base_path, '', parse_url($request_uri, PHP_URL_PATH));
$path = trim($path, '/');

// Parse route - handle empty path
if (empty($path)) {
    $segments = ['home'];
} else {
    $segments = explode('/', $path);
}

// API routes
if ($segments[0] === 'api') {
    header('Content-Type: application/json');
    
    switch($segments[1] ?? '') {
        case 'products':
            $controller = new ProductController();
            if (isset($segments[2])) {
                // GET /api/products/:id or /api/products/:slug
                echo $controller->getProduct($segments[2]);
            } else {
                // GET /api/products?category=slug&search=term
                echo $controller->getProducts();
            }
            break;
            
        case 'categories':
            $controller = new CategoryController();
            if (isset($segments[2])) {
                // GET /api/categories/:id
                echo $controller->getCategory($segments[2]);
            } else {
                // GET /api/categories
                echo $controller->getCategories();
            }
            break;

        case 'cart':
            $controller = new CartController();
            echo $controller->handle();  
            break;

        case 'auth':
            $controller = new AuthController();
            echo $controller->handle();
            break;
                
        case 'orders':
            $controller = new OrdersController();
            echo $controller->handle();
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
    }
    exit;
}

// Frontend routes
switch ($segments[0] ?? 'home') {
    case 'home':
    case '':
        include __DIR__ . '/views/home.php';
        break;
        
    case 'products':
        include __DIR__ . '/views/products.php';
        break;
        
    case 'product':
        if (isset($segments[1])) {
            $_GET['slug'] = $segments[1];
            include __DIR__ . '/views/product-detail.php';
        } else {
            header('Location: ' . BASE_URL . '/products');
        }
        break;
        
    case 'category':
        if (isset($segments[1])) {
            $_GET['slug'] = $segments[1];
            include __DIR__ . '/views/category.php';
        } else {
            header('Location: ' . BASE_URL . '/products');
        }
        break;
        
        
    case 'login':
        if (file_exists(__DIR__ . '/views/login.php')) {
            include __DIR__ . '/views/login.php';
        } else {
            echo "Error: login.php not found";
        }
        break;
        
    case 'register':
        if (file_exists(__DIR__ . '/views/register.php')) {
            include __DIR__ . '/views/register.php';
        } else {
            echo "Error: register.php not found";
        }
        break;
        
    case 'account':
    case 'my-orders':
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        if (file_exists(__DIR__ . '/views/my-orders.php')) {
            include __DIR__ . '/views/my-orders.php';
        } else {
            echo "Error: my-orders.php not found";
        }
        break;
        
    case 'checkout':
        if (file_exists(__DIR__ . '/views/checkout.php')) {
            include __DIR__ . '/views/checkout.php';
        } else {
            echo "Error: checkout.php not found";
        }
        break;

    case 'logout':
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '/');
        exit;

    default:
        http_response_code(404);
        include __DIR__ . '/views/404.php';
}
