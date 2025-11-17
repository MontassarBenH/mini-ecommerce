<?php
// controllers/CartController.php

class CartController
{
    /** @var ShoppingCart */
    private $cart;

    public function __construct()
    {
        // PluginManager ist bereits in config.php geladen
        $pluginManager = PluginManager::getInstance();
        $pluginData    = $pluginManager->getPlugin('ShoppingCart');

        if (!$pluginData || !isset($pluginData['instance']) || !$pluginData['instance'] instanceof ShoppingCart) {
            // Kein Plugin geladen => später 500 zurückgeben
            $this->cart = null;
        } else {
            $this->cart = $pluginData['instance'];
        }
    }

    public function handle()
    {
        if ($this->cart === null) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'message' => 'ShoppingCart plugin not available'
            ]);
        }

        $method = $_SERVER['REQUEST_METHOD'];

        // Body-Daten holen (JSON oder Form-POST)
        $rawBody = file_get_contents('php://input');
        $data    = json_decode($rawBody, true);
        if (!is_array($data)) {
            $data = $_POST;
        }

        $action = $data['action'] ?? null;

        try {
            switch ($method) {
                case 'GET':
                    // 🔹 GET /api/cart  → gesamten Warenkorb laden
                    $items = $this->cart->getCartItems();
                    $total = $this->cart->getCartTotal();
                    $count = $this->cart->getCartCount();

                    return json_encode([
                        'success' => true,
                        'items'   => $items,
                        'total'   => $total,
                        'count'   => $count,
                    ]);

                case 'POST':
                    // Standard: add to cart
                    if ($action === null || $action === 'add') {
                        $productId = isset($data['product_id'])
                            ? (int)$data['product_id']
                            : (isset($data['productId']) ? (int)$data['productId'] : 0);

                        $quantity  = isset($data['quantity']) ? (int)$data['quantity'] : 1;

                        if ($productId <= 0) {
                            http_response_code(400);
                            return json_encode(['success' => false, 'message' => 'Invalid product_id']);
                        }

                        $result = $this->cart->addToCart($productId, $quantity);
                        return json_encode($result);
                    }



                    if ($action === 'update') {
                        $productId = isset($data['product_id']) ? (int)$data['product_id'] : 0;
                        $quantity  = isset($data['quantity']) ? (int)$data['quantity'] : 1;

                        if ($productId <= 0) {
                            http_response_code(400);
                            return json_encode(['success' => false, 'message' => 'Invalid product_id']);
                        }

                        $result = $this->cart->updateQuantity($productId, $quantity);
                        return json_encode($result);
                    }

                    if ($action === 'remove') {
                        $productId = isset($data['product_id']) ? (int)$data['product_id'] : 0;

                        if ($productId <= 0) {
                            http_response_code(400);
                            return json_encode(['success' => false, 'message' => 'Invalid product_id']);
                        }

                        $result = $this->cart->removeFromCart($productId);
                        return json_encode($result);
                    }

                    if ($action === 'clear') {
                        $result = $this->cart->clearCart();
                        return json_encode($result);
                    }

                    if ($action === 'checkout') {
                        // 🧾 Optional: Checkout verarbeiten (Cart->createOrder)
                        $customerData = [
                            'name'           => $data['name']          ?? '',
                            'email'          => $data['email']         ?? '',
                            'phone'          => $data['phone']         ?? '',
                            'address'        => $data['address']       ?? '',
                            'payment_method' => $data['payment_method'] ?? 'credit_card',
                        ];
                        $result = $this->cart->createOrder($customerData);
                        return json_encode($result);
                    }

                    http_response_code(400);
                    return json_encode(['success' => false, 'message' => 'Unknown action']);

                default:
                    http_response_code(405);
                    return json_encode(['success' => false, 'message' => 'Method not allowed']);
            }
        } catch (Throwable $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'message' => 'Cart error',
                'error'   => $e->getMessage()
            ]);
        }
    }
}
