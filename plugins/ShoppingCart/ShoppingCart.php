<?php

require_once __DIR__ . '/../../config.php';

class ShoppingCart extends BasePlugin {
    private $db;
    private $sessionId;
    
    public function init() {
        $this->db = Database::getInstance()->getConnection();
        $this->startSession();
        
        // Register hooks
        $this->registerHook('head_css', [$this, 'addStyles'], 10);
        $this->registerHook('before_body_close', [$this, 'addCartModal'], 10);
        $this->registerHook('after_body_open', [$this, 'addCartScript'], 10);
    }
    
    /**
     * Start session and get session ID
     */
    private function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['cart_id'])) {
            $_SESSION['cart_id'] = uniqid('cart_', true);
        }
        
        $this->sessionId = $_SESSION['cart_id'];
    }
    
    /**
     * Add CSS styles
     */
    public function addStyles() {
        return '<link rel="stylesheet" href="' . $this->getAssetUrl('cart.css') . '">';
    }
    
    /**
     * Add cart modal HTML
     */
    public function addCartModal() {
        return $this->loadView('cart-modal');
    }
    
    /**
     * Add cart JavaScript
     */
    public function addCartScript() {
        return '<script src="' . $this->getAssetUrl('cart.js') . '"></script>';
    }
    
    /**
     * Get cart items
     */
    public function getCartItems() {
        $sql = "SELECT ci.*, p.name, p.slug, p.price, p.image_url, p.stock,
                (ci.quantity * p.price) as subtotal
                FROM cart_items ci
                JOIN products p ON ci.product_id = p.id
                WHERE ci.session_id = :session_id
                ORDER BY ci.created_at DESC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':session_id' => $this->sessionId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Cart Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Add item to cart
     */
public function addToCart($productId, $quantity = 1) {
    $product = $this->getProduct($productId);
    if (!$product || $product['stock'] < $quantity) {
        return ['success' => false, 'message' => 'Product not available'];
    }

    $sql = "INSERT INTO cart_items (session_id, product_id, quantity)
            VALUES (:session_id, :product_id, :quantity)
            ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";

    try {
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':session_id' => $this->sessionId,
            ':product_id' => $productId,
            ':quantity'   => $quantity
        ]);

        return [
            'success'    => true,
            'message'    => 'Product added to cart',
            'cart_count' => $this->getCartCount()
        ];
    } catch (PDOException $e) {
        error_log("Cart Error addToCart: " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Failed to add to cart: ' . $e->getMessage()
        ];
    }
}


    
    /**
     * Update cart item quantity
     */
    public function updateQuantity($productId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeFromCart($productId);
        }
        
        // Check stock
        $product = $this->getProduct($productId);
        if (!$product || $product['stock'] < $quantity) {
            return ['success' => false, 'message' => 'Insufficient stock'];
        }
        
        $sql = "UPDATE cart_items 
                SET quantity = :quantity
                WHERE session_id = :session_id AND product_id = :product_id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':quantity' => $quantity,
                ':session_id' => $this->sessionId,
                ':product_id' => $productId
            ]);
            
            return ['success' => true, 'cart_count' => $this->getCartCount()];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to update cart'];
        }
    }
    
    /**
     * Remove item from cart
     */
    public function removeFromCart($productId) {
        $sql = "DELETE FROM cart_items 
                WHERE session_id = :session_id AND product_id = :product_id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':session_id' => $this->sessionId,
                ':product_id' => $productId
            ]);
            
            return ['success' => true, 'cart_count' => $this->getCartCount()];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to remove item'];
        }
    }
    
    /**
     * Clear cart
     */
    public function clearCart() {
        $sql = "DELETE FROM cart_items WHERE session_id = :session_id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':session_id' => $this->sessionId]);
            return ['success' => true];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Failed to clear cart'];
        }
    }
    
    /**
     * Get cart count
     */
    public function getCartCount() {
        $sql = "SELECT SUM(quantity) as total FROM cart_items WHERE session_id = :session_id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':session_id' => $this->sessionId]);
            $result = $stmt->fetch();
            return (int)($result['total'] ?? 0);
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Get cart total
     */
    public function getCartTotal() {
        $items = $this->getCartItems();
        $total = 0;
        
        foreach ($items as $item) {
            $total += $item['subtotal'];
        }
        
        return $total;
    }
    
    /**
     * Get product by ID
     */
    private function getProduct($productId) {
        $sql = "SELECT * FROM products WHERE id = :id AND is_active = 1";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $productId]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return null;
        }
    }
    
    /**
     * Create order from cart
     */
    function createOrder($customerData) {
    $items = $this->getCartItems();
    
    if (empty($items)) {
        return ['success' => false, 'message' => 'Cart is empty'];
    }

    $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    
    try {
        $this->db->beginTransaction();
        
        $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

        // user_id ins Insert aufnehmen
        $sql = "INSERT INTO orders (
                    user_id,
                    order_number,
                    customer_name,
                    customer_email,
                    customer_phone,
                    shipping_address,
                    total_amount,
                    payment_method
                ) VALUES (
                    :user_id,
                    :order_number,
                    :name,
                    :email,
                    :phone,
                    :address,
                    :total,
                    :payment
                )";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id'      => $userId, // kann NULL sein, wenn Gast
            ':order_number' => $orderNumber,
            ':name'         => $customerData['name'],
            ':email'        => $customerData['email'],
            ':phone'        => $customerData['phone'] ?? '',
            ':address'      => $customerData['address'],
            ':total'        => $this->getCartTotal(),
            ':payment'      => $customerData['payment_method'] ?? 'credit_card'
        ]);

        $orderId = $this->db->lastInsertId();
            
            // Create order items
            $sql = "INSERT INTO order_items (order_id, product_id, product_name, 
                    product_price, quantity, subtotal)
                    VALUES (:order_id, :product_id, :name, :price, :quantity, :subtotal)";
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($items as $item) {
                $stmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['product_id'],
                    ':name' => $item['name'],
                    ':price' => $item['price'],
                    ':quantity' => $item['quantity'],
                    ':subtotal' => $item['subtotal']
                ]);
            }
            
            // Clear cart
            $this->clearCart();
            
            $this->db->commit();
            
            return [
                'success' => true,
                'order_number' => $orderNumber,
                'order_id' => $orderId
            ];
            
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Order Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to create order'];
        }
    }
}
?>