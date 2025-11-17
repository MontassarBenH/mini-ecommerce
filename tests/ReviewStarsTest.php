<?php

use PHPUnit\Framework\TestCase;

final class ReviewStarsTest extends TestCase
{
    private PDO $db;
    private PluginManager $pluginManager;
    private ReviewStars $reviewPlugin;
    private int $testProductId = 1; 

    protected function setUp(): void
    {
        $this->db = db();
        $this->pluginManager = PluginManager::getInstance();

        $pluginData = $this->pluginManager->getPlugin('ReviewStars');
        $this->assertNotNull($pluginData, 'ReviewStars plugin not loaded');
        $this->assertArrayHasKey('instance', $pluginData);
        $this->assertInstanceOf(ReviewStars::class, $pluginData['instance']);

        $this->reviewPlugin = $pluginData['instance'];

        // Test-Reviews vor jedem Test für dieses Produkt löschen
        $stmt = $this->db->prepare("DELETE FROM product_reviews WHERE product_id = :pid");
        $stmt->execute([':pid' => $this->testProductId]);
    }

    public function testReviewRenderingAfterInsert(): void
    {
        // 1) Review in DB einfügen
        $stmt = $this->db->prepare("
            INSERT INTO product_reviews (product_id, rating, title, comment, author_name, is_approved)
            VALUES (:pid, :rating, :title, :comment, :author, 1)
        ");
        $stmt->execute([
            ':pid'     => $this->testProductId,
            ':rating'  => 5,
            ':title'   => 'Amazing Playground!',
            ':comment' => 'Our kids absolutely love it.',
            ':author'  => 'Test User',
        ]);

        // 2) Dummy-Produkt wie im echten Template
        $product = [
            'id'    => $this->testProductId,
            'name'  => 'Dummy Product',
            'slug'  => 'dummy-product',
            'price' => 999.99,
            'stock' => 10,
        ];

        // 3) Review-Bereich über Hook rendern
        $html = $this->pluginManager->renderHook('product_detail_reviews', $product);

        // 4) Assertions
        $this->assertStringContainsString('Customer Reviews', $html);
        $this->assertStringContainsString('Amazing Playground!', $html);
        $this->assertStringContainsString('Our kids absolutely love it.', $html);
        $this->assertStringContainsString('Test User', $html);
    }

    public function testEmptyReviewListShowsMessage(): void
    {
        // sicherstellen, dass keine Reviews vorhanden sind
        $stmt = $this->db->prepare("DELETE FROM product_reviews WHERE product_id = :pid");
        $stmt->execute([':pid' => $this->testProductId]);

        $product = [
            'id'    => $this->testProductId,
            'name'  => 'Dummy Product',
            'slug'  => 'dummy-product',
            'price' => 999.99,
            'stock' => 10,
        ];

        $html = $this->pluginManager->renderHook('product_detail_reviews', $product);

        $this->assertStringContainsString('No reviews yet', $html);
    }
}
