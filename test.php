<?php

// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>PHP Test Page</h1>";
echo "<p>PHP is working! ✓</p>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Test 1: Check if config.php exists
echo "<hr><h2>Test 1: config.php</h2>";
if (file_exists('config.php')) {
    echo "✓ config.php exists<br>";
    require_once 'config.php';
    echo "✓ config.php loaded successfully<br>";
    echo "BASE_URL: " . BASE_URL . "<br>";
    echo "DB_NAME: " . DB_NAME . "<br>";
} else {
    echo "❌ config.php NOT FOUND!<br>";
}

// Test 2: Database connection
echo "<hr><h2>Test 2: Database Connection</h2>";
try {
    $db = Database::getInstance()->getConnection();
    echo "✓ Database connected successfully!<br>";
    
    // Test query
    $stmt = $db->query("SELECT COUNT(*) as count FROM products");
    $result = $stmt->fetch();
    echo "✓ Products in database: " . $result['count'] . "<br>";
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM categories");
    $result = $stmt->fetch();
    echo "✓ Categories in database: " . $result['count'] . "<br>";
    
} catch(Exception $e) {
    echo "❌ Database Error: " . $e->getMessage() . "<br>";
}

// Test 3: Check controllers
echo "<hr><h2>Test 3: Controllers</h2>";
if (file_exists('controllers/ProductController.php')) {
    echo "✓ ProductController.php exists<br>";
    require_once 'controllers/ProductController.php';
    echo "✓ ProductController loaded<br>";
} else {
    echo "❌ ProductController.php NOT FOUND!<br>";
}

if (file_exists('controllers/CategoryController.php')) {
    echo "✓ CategoryController.php exists<br>";
    require_once 'controllers/CategoryController.php';
    echo "✓ CategoryController loaded<br>";
} else {
    echo "❌ CategoryController.php NOT FOUND!<br>";
}

// Test 4: Check views
echo "<hr><h2>Test 4: Views</h2>";
$views = ['home.php', 'products.php', 'product-detail.php', 'category.php', '404.php'];
foreach ($views as $view) {
    if (file_exists('views/' . $view)) {
        echo "✓ views/$view exists<br>";
    } else {
        echo "❌ views/$view NOT FOUND!<br>";
    }
}

// Test 5: Check .htaccess
echo "<hr><h2>Test 5: .htaccess</h2>";
if (file_exists('.htaccess')) {
    echo "✓ .htaccess exists<br>";
    echo "<pre>" . htmlspecialchars(file_get_contents('.htaccess')) . "</pre>";
} else {
    echo "❌ .htaccess NOT FOUND!<br>";
}

echo "<hr><h2>All Tests Complete!</h2>";
echo "<p>If all tests pass, try accessing the main page again.</p>";
?>