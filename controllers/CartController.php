<?php
// controllers/CartController.php

require_once __DIR__ . '/../config.php';

class CartController {
    private $cart;
    
    public function __construct() {
        $pluginManager = PluginManager::getInstance();
        $cartPlugin = $pluginManager->getPlugin('ShoppingCart');
        
        if ($cartPlugin) {
            $this->cart = $cartPlugin['instance'];
        } else {
            throw new Exception('Shopping Cart plugin not loaded');
        }
    }
    
    /**
     * Get cart items
     */
    public function getCart() {
        $items = $this->cart->getCartItems();
        $total = $this->cart->getCartTotal();
        $count = $this->cart->getCartCount();
        
        return json_encode([
            'success' => true,
            'items' => $items,
            'total' => $total,
            'count' => $count
        ]);
    }
    
    /**
     * Add item to cart
     */
    public function addToCart() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['product_id'])) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Product ID required']);
        }
        
        $quantity = $data['quantity'] ?? 1;
        $result = $this->cart->addToCart($data['product_id'], $quantity);
        
        if (!$result['success']) {
            http_response_code(400);
        }
        
        return json_encode($result);
    }
    
    /**
     * Update cart item quantity
     */
    public function updateCart() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['product_id']) || !isset($data['quantity'])) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Product ID and quantity required']);
        }
        
        $result = $this->cart->updateQuantity($data['product_id'], $data['quantity']);
        
        if (!$result['success']) {
            http_response_code(400);
        }
        
        return json_encode($result);
    }
    
    /**
     * Remove item from cart
     */
    public function removeFromCart($productId) {
        $result = $this->cart->removeFromCart($productId);
        
        if (!$result['success']) {
            http_response_code(400);
        }
        
        return json_encode($result);
    }
    
    /**
     * Clear cart
     */
    public function clearCart() {
        $result = $this->cart->clearCart();
        
        if (!$result['success']) {
            http_response_code(400);
        }
        
        return json_encode($result);
    }
    
    /**
     * Create order (checkout)
     */
    public function checkout() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $required = ['name', 'email', 'address'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'message' => ucfirst($field) . ' is required'
                ]);
            }
        }
        
        $result = $this->cart->createOrder($data);
        
        if (!$result['success']) {
            http_response_code(400);
        }
        
        return json_encode($result);
    }
}
?>