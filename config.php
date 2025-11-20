<?php
// config.php - Database configuration

define('DB_HOST', 'localhost');
define('DB_NAME', 'mini_ecommerce');
define('DB_NAME_TEST', 'mini_ecommerce_test');
define('DB_USER', 'root');
define('DB_PASS', '');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base URL configuration
define('BASE_URL', 'http://localhost/mini-ecommerce');

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load Plugin Manager
require_once __DIR__ . '/plugins/PluginManager.php';

// Initialize and load plugins
$pluginManager = PluginManager::getInstance();
$pluginManager->loadPlugins();

/**
 * Optional Helferfunktion, falls du lieber eine Funktion aufrufst:
 */
if (!function_exists('pm')) {
    function pm() {
        return PluginManager::getInstance();
    }
}

// Database connection class
class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
    try {
        $appEnv = getenv('APP_ENV') ?: 'prod';

        $dbName = ($appEnv === 'test')
            ? DB_NAME_TEST  
            : DB_NAME;       

        $this->conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . $dbName . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
    } catch(PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
}
?>
