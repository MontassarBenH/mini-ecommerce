<?php

use PHPUnit\Framework\TestCase;

final class RegressionReviewsTest extends TestCase
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

        // Bestehende Reviews bereinigen, damit der Test deterministisch ist
        $stmt = $this->db->prepare("DELETE FROM product_reviews WHERE product_id = :pid");
        $stmt->execute([':pid' => $this->testProductId]);

        // Ein Review anlegen, damit auch wirklich etwas gerendert wird
        $stmt = $this->db->prepare("
            INSERT INTO product_reviews (product_id, rating, title, comment, author_name, is_approved)
            VALUES (:pid, 5, 'Regression Test', 'Initial review for regression test', 'QA Bot', 1)
        ");
        $stmt->execute([':pid' => $this->testProductId]);
    }

    public function testRenderingReviewsDoesNotCreateNewDbEntries(): void
    {
        // 1) Review-Anzahl vor dem Rendern merken
        $beforeCount = $this->getReviewCount();

        // 2) Produkt-Dummy wie in deinen anderen Tests
        $product = [
            'id'    => $this->testProductId,
            'name'  => 'Dummy Product',
            'slug'  => 'dummy-product',
            'price' => 123.45,
            'stock' => 5,
        ];

        // 3) Review-Sektion rendern 
        $html = $this->pluginManager->renderHook('product_detail_reviews', $product);

        // Sicherstellen, dass HTML überhaupt da ist
        $this->assertStringContainsString('Customer Reviews', $html);

        // 4) Review-Anzahl nach dem Rendern prüfen
        $afterCount = $this->getReviewCount();

        $this->assertSame(
            $beforeCount,
            $afterCount,
            'Rendering the reviews section must not create new product_reviews rows'
        );
    }

    private function getReviewCount(): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS cnt FROM product_reviews WHERE product_id = :pid");
        $stmt->execute([':pid' => $this->testProductId]);
        $row = $stmt->fetch();
        return (int)($row['cnt'] ?? 0);
    }
}
