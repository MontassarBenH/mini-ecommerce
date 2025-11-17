<?php

use PHPUnit\Framework\TestCase;

final class ApiProductsTest extends TestCase
{
    private ProductController $controller;

    protected function setUp(): void
    {
        $this->controller = new ProductController();
    }

    public function testGetProductsReturnsList(): void
    {
        // Simuliert: GET /api/products
        $json = $this->controller->getProducts();
        $data = json_decode($json, true);

        $this->assertIsArray($data, 'Response should be JSON-decoded array');
        $this->assertArrayHasKey('success', $data);
        $this->assertTrue($data['success'], 'success should be true');

        $this->assertArrayHasKey('data', $data);
        $this->assertIsArray($data['data'], 'data should be an array of products');
        $this->assertNotEmpty($data['data'], 'products list should not be empty');

        $first = $data['data'][0];

        $this->assertArrayHasKey('id', $first);
        $this->assertArrayHasKey('name', $first);
        $this->assertArrayHasKey('slug', $first);
        $this->assertArrayHasKey('price', $first);
        $this->assertArrayHasKey('stock', $first);
    }

    public function testGetSingleProductBySlug(): void
    {
        // Erst alle Produkte holen, um einen gültigen slug zu haben
        $jsonList = $this->controller->getProducts();
        $list = json_decode($jsonList, true);

        $this->assertTrue($list['success']);
        $this->assertNotEmpty($list['data']);

        $firstProduct = $list['data'][0];
        $slug = $firstProduct['slug'];

        // Simuliert: GET /api/products/{slug}
        $jsonProduct = $this->controller->getProduct($slug);
        $data = json_decode($jsonProduct, true);

        $this->assertTrue($data['success']);
        $this->assertIsArray($data['data']);

        $product = $data['data'];

        $this->assertSame($slug, $product['slug']);
        $this->assertArrayHasKey('name', $product);
        $this->assertArrayHasKey('price', $product);
        $this->assertArrayHasKey('stock', $product);
    }
}
