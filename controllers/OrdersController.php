<?php
/**
 * OrdersController.php
 * Handle user orders
 */

class OrdersController {
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
            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                return json_encode([
                    'success' => false,
                    'error' => 'NOT_AUTHENTICATED',
                    'message' => 'Please login to view orders'
                ]);
            }
            
            $method = $_SERVER['REQUEST_METHOD'];
            
            if ($method === 'GET') {
                // Get order ID from URL if present
                $segments = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
                $orderIndex = array_search('orders', $segments);
                
                if ($orderIndex !== false && isset($segments[$orderIndex + 1])) {
                    // Get single order
                    return $this->getOrder($segments[$orderIndex + 1]);
                } else {
                    // Get all user orders
                    return $this->getOrders();
                }
            }
            
            return json_encode(['success' => false, 'error' => 'METHOD_NOT_ALLOWED']);
            
        } catch (Exception $e) {
            error_log("OrdersController Error: " . $e->getMessage());
            return json_encode([
                'success' => false,
                'error' => 'SERVER_ERROR',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    private function getOrders() {
        try {
            $userId = $_SESSION['user_id'];
            
            $stmt = $this->pdo->prepare("
                SELECT id, order_number, total_amount, status, 
                       payment_method, created_at
                FROM orders
                WHERE user_id = ?
                ORDER BY created_at DESC
            ");
            
            $stmt->execute([$userId]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format orders
            $formattedOrders = [];
            foreach ($orders as $order) {
                $formattedOrders[] = [
                    'id' => intval($order['id']),
                    'order_number' => $order['order_number'],
                    'total' => floatval($order['total_amount']),
                    'status' => $order['status'],
                    'payment_method' => $order['payment_method'],
                    'date' => $order['created_at']
                ];
            }
            
            return json_encode([
                'success' => true,
                'data' => $formattedOrders
            ]);
            
        } catch (Exception $e) {
            error_log("GetOrders Error: " . $e->getMessage());
            return json_encode([
                'success' => false,
                'error' => 'GET_ORDERS_ERROR',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    private function getOrder($orderId) {
        try {
            $userId = $_SESSION['user_id'];
            
            // Get order
            $stmt = $this->pdo->prepare("
                SELECT * FROM orders
                WHERE id = ? AND user_id = ?
            ");
            
            $stmt->execute([$orderId, $userId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$order) {
                return json_encode([
                    'success' => false,
                    'error' => 'ORDER_NOT_FOUND',
                    'message' => 'Order not found'
                ]);
            }
            
            // Get order items
            $stmt = $this->pdo->prepare("
                SELECT * FROM order_items WHERE order_id = ?
            ");
            
            $stmt->execute([$orderId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return json_encode([
                'success' => true,
                'data' => [
                    'order' => [
                        'id' => intval($order['id']),
                        'order_number' => $order['order_number'],
                        'customer_name' => $order['customer_name'],
                        'customer_email' => $order['customer_email'],
                        'customer_phone' => $order['customer_phone'],
                        'shipping_address' => $order['shipping_address'],
                        'total' => floatval($order['total_amount']),
                        'status' => $order['status'],
                        'payment_method' => $order['payment_method'],
                        'created_at' => $order['created_at']
                    ],
                    'items' => array_map(function($item) {
                        return [
                            'product_name' => $item['product_name'],
                            'price' => floatval($item['product_price']),
                            'quantity' => intval($item['quantity']),
                            'subtotal' => floatval($item['subtotal'])
                        ];
                    }, $items)
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("GetOrder Error: " . $e->getMessage());
            return json_encode([
                'success' => false,
                'error' => 'GET_ORDER_ERROR',
                'message' => $e->getMessage()
            ]);
        }
    }
}