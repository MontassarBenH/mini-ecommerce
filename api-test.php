<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';
require_once 'controllers/ProductController.php';
require_once 'controllers/CategoryController.php';

echo "<h1>API Test</h1>";

// Test Categories API
echo "<h2>Test 1: Categories API</h2>";
try {
    $controller = new CategoryController();
    $result = $controller->getCategories();
    echo "<pre>" . htmlspecialchars($result) . "</pre>";
    
    $json = json_decode($result, true);
    if ($json && $json['success']) {
        echo "✅ Categories API works! Found " . count($json['data']) . " categories<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

// Test Products API
echo "<hr><h2>Test 2: Products API</h2>";
try {
    $controller = new ProductController();
    $result = $controller->getProducts();
    echo "<pre>" . htmlspecialchars($result) . "</pre>";
    
    $json = json_decode($result, true);
    if ($json && $json['success']) {
        echo "✅ Products API works! Found " . count($json['data']) . " products<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

// Test Single Product API
echo "<hr><h2>Test 3: Single Product API</h2>";
try {
    $controller = new ProductController();
    $result = $controller->getProduct('wooden-adventure-tower');
    echo "<pre>" . htmlspecialchars($result) . "</pre>";
    
    $json = json_decode($result, true);
    if ($json && $json['success']) {
        echo "✅ Single Product API works!<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

echo "<hr>";
echo "<p><strong>If all tests pass ✅, then the API code is fine and the issue is with routing.</strong></p>";
?>