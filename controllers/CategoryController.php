<?php

require_once __DIR__ . '/../config.php';

class CategoryController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getCategories() {
        $sql = "SELECT c.*, 
                COUNT(DISTINCT pc.product_id) as product_count
                FROM categories c
                LEFT JOIN product_categories pc ON c.id = pc.category_id
                LEFT JOIN products p ON pc.product_id = p.id AND p.is_active = 1
                GROUP BY c.id
                ORDER BY c.name";
        
        try {
            $stmt = $this->db->query($sql);
            $categories = $stmt->fetchAll();
            
            return json_encode([
                'success' => true,
                'data' => $categories
            ]);
        } catch(PDOException $e) {
            http_response_code(500);
            return json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    
    public function getCategory($identifier) {
        $isId = is_numeric($identifier);
        
        $sql = "SELECT c.*, 
                COUNT(DISTINCT pc.product_id) as product_count
                FROM categories c
                LEFT JOIN product_categories pc ON c.id = pc.category_id
                LEFT JOIN products p ON pc.product_id = p.id AND p.is_active = 1
                WHERE ";
        
        if ($isId) {
            $sql .= "c.id = :identifier";
        } else {
            $sql .= "c.slug = :identifier";
        }
        
        $sql .= " GROUP BY c.id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':identifier', $identifier);
            $stmt->execute();
            
            $category = $stmt->fetch();
            
            if ($category) {
                return json_encode([
                    'success' => true,
                    'data' => $category
                ]);
            } else {
                http_response_code(404);
                return json_encode(['error' => 'Category not found']);
            }
        } catch(PDOException $e) {
            http_response_code(500);
            return json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
}
?>