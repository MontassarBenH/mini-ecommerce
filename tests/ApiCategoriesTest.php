<?php

use PHPUnit\Framework\TestCase;

final class ApiCategoriesTest extends TestCase
{
    private CategoryController $controller;

    protected function setUp(): void
    {
        $this->controller = new CategoryController();
    }

    public function testGetCategoriesReturnsList(): void
    {
        // Simuliert: GET /api/categories
        $json = $this->controller->getCategories();
        $data = json_decode($json, true);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('success', $data);
        $this->assertTrue($data['success']);

        $this->assertArrayHasKey('data', $data);
        $this->assertIsArray($data['data']);
        $this->assertNotEmpty($data['data']);

        $first = $data['data'][0];

        $this->assertArrayHasKey('id', $first);
        $this->assertArrayHasKey('name', $first);
        $this->assertArrayHasKey('slug', $first);
    }
}
