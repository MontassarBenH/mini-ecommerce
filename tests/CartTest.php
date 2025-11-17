<?php

use PHPUnit\Framework\TestCase;

final class CartTest extends TestCase
{
    private PluginManager $pluginManager;
    private ?ShoppingCart $cart = null;

    private int $testProductId = 1;

    protected function setUp(): void
    {
        $this->pluginManager = PluginManager::getInstance();

        $pluginData = $this->pluginManager->getPlugin('ShoppingCart');
        $this->assertNotNull($pluginData, 'ShoppingCart plugin not loaded');
        $this->assertArrayHasKey('instance', $pluginData);
        $this->assertInstanceOf(ShoppingCart::class, $pluginData['instance']);

        $this->cart = $pluginData['instance'];

        $result = $this->cart->clearCart();
        $this->assertTrue($result['success'], 'Could not clear cart before test');
    }

    public function testAddToCartAndGetItems(): void
    {
        // 1) Produkt in den Warenkorb legen
        $result = $this->cart->addToCart($this->testProductId, 2);
        $this->assertTrue($result['success'], 'addToCart did not return success');

        // 2) Cart-Count prüfen
        $count = $this->cart->getCartCount();
        $this->assertSame(2, $count, 'Cart count should be 2 after adding quantity=2');

        // 3) Items holen
        $items = $this->cart->getCartItems();
        $this->assertCount(1, $items, 'There should be exactly 1 cart item');

        $item = $items[0];
        $this->assertSame($this->testProductId, (int)$item['product_id']);
        $this->assertSame(2, (int)$item['quantity']);

        // 4) Total prüfen
        $total = $this->cart->getCartTotal();
        $expected = (float)$item['price'] * 2;
        $this->assertEquals($expected, $total);
    }

    public function testUpdateQuantity(): void
    {
        // Erst 1 Stück in den Warenkorb
        $this->cart->addToCart($this->testProductId, 1);

        // Dann Menge auf 3 aktualisieren
        $result = $this->cart->updateQuantity($this->testProductId, 3);
        $this->assertTrue($result['success'], 'updateQuantity should return success');

        // Prüfen, ob Count = 3 ist
        $count = $this->cart->getCartCount();
        $this->assertSame(3, $count);

        $items = $this->cart->getCartItems();
        $this->assertCount(1, $items);
        $this->assertSame(3, (int)$items[0]['quantity']);
    }

    public function testRemoveFromCart(): void
    {
        // Ein Produkt rein
        $this->cart->addToCart($this->testProductId, 1);

        // Entfernen
        $result = $this->cart->removeFromCart($this->testProductId);
        $this->assertTrue($result['success'], 'removeFromCart should return success');

        // Cart sollte leer sein
        $count = $this->cart->getCartCount();
        $this->assertSame(0, $count);

        $items = $this->cart->getCartItems();
        $this->assertCount(0, $items);
    }
}
