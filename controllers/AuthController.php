<?php
/**
 * AuthController.php
 * Simple authentication controller
 */

class AuthController {
    private $pdo;
    
    public function __construct() {
        require_once __DIR__ . '/../config.php';
        $db = Database::getInstance();
        $this->pdo = $db->getConnection();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function handle() {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            
            if ($method === 'POST') {
                $input = file_get_contents('php://input');
                $data = json_decode($input, true);
                $action = $data['action'] ?? null;
                
                switch ($action) {
                    case 'register':
                        return $this->register($data);
                    case 'login':
                        return $this->login($data);
                    case 'logout':
                        return $this->logout();
                    case 'check':
                        return $this->checkAuth();
                    default:
                        return json_encode([
                            'success' => false,
                            'error' => 'INVALID_ACTION'
                        ]);
                }
            }
            
            if ($method === 'GET') {
                return $this->checkAuth();
            }
            
            return json_encode(['success' => false, 'error' => 'METHOD_NOT_ALLOWED']);
            
        } catch (Exception $e) {
            error_log("AuthController Error: " . $e->getMessage());
            return json_encode([
                'success' => false,
                'error' => 'SERVER_ERROR',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    private function register($data) {
        try {
            // Validate input
            if (empty($data['email']) || empty($data['password']) || empty($data['full_name'])) {
                return json_encode([
                    'success' => false,
                    'error' => 'MISSING_FIELDS',
                    'message' => 'Email, password, and full name are required'
                ]);
            }
            
            // Validate email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return json_encode([
                    'success' => false,
                    'error' => 'INVALID_EMAIL',
                    'message' => 'Invalid email address'
                ]);
            }
            
            // Check if user already exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            
            if ($stmt->fetch()) {
                return json_encode([
                    'success' => false,
                    'error' => 'EMAIL_EXISTS',
                    'message' => 'Email already registered'
                ]);
            }
            
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $stmt = $this->pdo->prepare("
                INSERT INTO users (email, password, full_name, phone, address)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $data['email'],
                $hashedPassword,
                $data['full_name'],
                $data['phone'] ?? null,
                $data['address'] ?? null
            ]);
            
            $userId = $this->pdo->lastInsertId();
            
            // Auto login after registration
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_email'] = $data['email'];
            $_SESSION['user_name'] = $data['full_name'];
            
            return json_encode([
                'success' => true,
                'message' => 'Registration successful',
                'user' => [
                    'id' => $userId,
                    'email' => $data['email'],
                    'name' => $data['full_name']
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Register Error: " . $e->getMessage());
            return json_encode([
                'success' => false,
                'error' => 'REGISTRATION_FAILED',
                'message' => 'Registration failed. Please try again.'
            ]);
        }
    }
    
    private function login($data) {
        try {
            // Validate input
            if (empty($data['email']) || empty($data['password'])) {
                return json_encode([
                    'success' => false,
                    'error' => 'MISSING_CREDENTIALS',
                    'message' => 'Email and password are required'
                ]);
            }
            
            // Get user
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verify password
            if (!$user || !password_verify($data['password'], $user['password'])) {
                return json_encode([
                    'success' => false,
                    'error' => 'INVALID_CREDENTIALS',
                    'message' => 'Invalid email or password'
                ]);
            }
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['full_name'];
            
            return json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'name' => $user['full_name']
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Login Error: " . $e->getMessage());
            return json_encode([
                'success' => false,
                'error' => 'LOGIN_FAILED',
                'message' => 'Login failed. Please try again.'
            ]);
        }
    }
    
    private function logout() {
        // Clear user session but keep cart
        $cart = $_SESSION['cart'] ?? [];
        
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        
        $_SESSION['cart'] = $cart; 
        
        return json_encode([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }
    
    private function checkAuth() {
        if (isset($_SESSION['user_id'])) {
            return json_encode([
                'success' => true,
                'authenticated' => true,
                'user' => [
                    'id' => $_SESSION['user_id'],
                    'email' => $_SESSION['user_email'],
                    'name' => $_SESSION['user_name']
                ]
            ]);
        }
        
        return json_encode([
            'success' => true,
            'authenticated' => false
        ]);
    }
    
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public static function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    public static function getUserName() {
        return $_SESSION['user_name'] ?? null;
    }
}