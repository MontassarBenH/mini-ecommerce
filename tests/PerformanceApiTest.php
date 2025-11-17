<?php

use PHPUnit\Framework\TestCase;

/**
 * @group performance
 */
final class PerformanceApiTest extends TestCase
{
    private ProductController $productController;
    private CategoryController $categoryController;

    protected function setUp(): void
    {
        $this->productController = new ProductController();
        $this->categoryController = new CategoryController();
    }

    public function testProductsApiIsFastEnough(): void
    {
        $start = microtime(true);

        $json = $this->productController->getProducts();
        $data = json_decode($json, true);

        $durationMs = (microtime(true) - $start) * 1000;

        // Sanity Checks
        $this->assertTrue($data['success'] ?? false);
        $this->assertIsArray($data['data'] ?? null);

        // Performance Check (unter 200ms)
        $this->assertLessThan(
            200,
            $durationMs,
            sprintf('getProducts() took %.2f ms, expected < 200 ms', $durationMs)
        );
    }

    public function testSingleProductApiIsFastEnough(): void
    {
        // Erst ein Produkt holen, um einen gültigen Slug zu haben
        $jsonList = $this->productController->getProducts();
        $list = json_decode($jsonList, true);
        $this->assertTrue($list['success']);
        $this->assertNotEmpty($list['data']);

        $slug = $list['data'][0]['slug'];

        $start = microtime(true);

        $json = $this->productController->getProduct($slug);
        $data = json_decode($json, true);

        $durationMs = (microtime(true) - $start) * 1000;

        $this->assertTrue($data['success'] ?? false);

        $this->assertLessThan(
            150,
            $durationMs,
            sprintf('getProduct(%s) took %.2f ms, expected < 150 ms', $slug, $durationMs)
        );
    }

    public function testCategoriesApiIsFastEnough(): void
    {
        $start = microtime(true);

        $json = $this->categoryController->getCategories();
        $data = json_decode($json, true);

        $durationMs = (microtime(true) - $start) * 1000;

        $this->assertTrue($data['success'] ?? false);
        $this->assertIsArray($data['data'] ?? null);

        $this->assertLessThan(
            150,
            $durationMs,
            sprintf('getCategories() took %.2f ms, expected < 150 ms', $durationMs)
        );
    }
}
