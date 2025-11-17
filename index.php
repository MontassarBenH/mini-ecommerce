<?php
// index.php - Main entry point with routing

require_once 'config.php';
require_once 'controllers/ProductController.php';
require_once 'controllers/CategoryController.php';
require_once 'controllers/CartController.php';


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
            
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
    }
    exit;
}

// Frontend routes
switch($segments[0] ?? 'home') {
    case 'home':
    case '':
        include 'views/home.php';
        break;
        
    case 'products':
        include 'views/products.php';
        break;
        
    case 'product':
        if (isset($segments[1])) {
            $_GET['slug'] = $segments[1];
            include 'views/product-detail.php';
        } else {
            header('Location: /products');
        }
        break;
        
    case 'category':
        if (isset($segments[1])) {
            $_GET['slug'] = $segments[1];
            include 'views/category.php';
        } else {
            header('Location: /products');
        }
        break;
        
    default:
        http_response_code(404);
        include 'views/404.php';
}
?>