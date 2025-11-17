<?php

use PHPUnit\Framework\TestCase;

/**
 * @group performance
 */
final class PerformanceCartTest extends TestCase
{
    private ?ShoppingCart $cart = null;
    private int $testProductId = 1;

    protected function setUp(): void
    {
        $pm = PluginManager::getInstance();
        $pluginData = $pm->getPlugin('ShoppingCart');

        $this->assertNotNull($pluginData, 'ShoppingCart plugin not loaded');
        $this->assertArrayHasKey('instance', $pluginData);
        $this->assertInstanceOf(ShoppingCart::class, $pluginData['instance']);

        $this->cart = $pluginData['instance'];

        // Vor jedem Test: Cart leeren, damit die Zeiten vergleichbar sind
        $this->cart->clearCart();
    }

    public function testAddToCartIsFastEnough(): void
    {
        $start = microtime(true);

        $result = $this->cart->addToCart($this->testProductId, 1);

        $durationMs = (microtime(true) - $start) * 1000;

        $this->assertTrue($result['success'] ?? false);

        $this->assertLessThan(
            150,
            $durationMs,
            sprintf('addToCart() took %.2f ms, expected < 150 ms', $durationMs)
        );
    }
}
