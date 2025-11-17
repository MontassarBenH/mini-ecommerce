<?php

class ProductController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function getProducts() {
        $category = $_GET['category'] ?? null;
        $search = $_GET['search'] ?? null;
        $limit = $_GET['limit'] ?? 20;
        $offset = $_GET['offset'] ?? 0;
        
        $sql = "SELECT DISTINCT p.*, 
                GROUP_CONCAT(c.name SEPARATOR ', ') as category_names,
                GROUP_CONCAT(c.slug SEPARATOR ', ') as category_slugs
                FROM products p
                LEFT JOIN product_categories pc ON p.id = pc.product_id
                LEFT JOIN categories c ON pc.category_id = c.id
                WHERE p.is_active = 1";
        
        $params = [];
        
        // Filter by category
        if ($category) {
            $sql .= " AND c.slug = :category";
            $params[':category'] = $category;
        }
        
        // Search filter
        if ($search) {
            $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            $params[':search'] = "%$search%";
        }
        
        $sql .= " GROUP BY p.id ORDER BY p.created_at DESC LIMIT :limit OFFSET :offset";
        
        try {
            $stmt = $this->db->prepare($sql);
            
            // Bind parameters
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            
            $stmt->execute();
            $products = $stmt->fetchAll();
            
            // Get total count
            $countSql = "SELECT COUNT(DISTINCT p.id) as total FROM products p
                        LEFT JOIN product_categories pc ON p.id = pc.product_id
                        LEFT JOIN categories c ON pc.category_id = c.id
                        WHERE p.is_active = 1";
            
            if ($category) {
                $countSql .= " AND c.slug = :category";
            }
            if ($search) {
                $countSql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
            }
            
            $countStmt = $this->db->prepare($countSql);
            foreach ($params as $key => $value) {
                if ($key !== ':limit' && $key !== ':offset') {
                    $countStmt->bindValue($key, $value);
                }
            }
            $countStmt->execute();
            $total = $countStmt->fetch()['total'];
            
            return json_encode([
                'success' => true,
                'data' => $products,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset
            ]);
        } catch(PDOException $e) {
            http_response_code(500);
            return json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
    
    public function getProduct($identifier) {
        $isId = is_numeric($identifier);
        
        $sql = "SELECT p.*, 
                GROUP_CONCAT(DISTINCT c.id) as category_ids,
                GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') as category_names,
                GROUP_CONCAT(DISTINCT c.slug SEPARATOR ', ') as category_slugs
                FROM products p
                LEFT JOIN product_categories pc ON p.id = pc.product_id
                LEFT JOIN categories c ON pc.category_id = c.id
                WHERE ";
        
        if ($isId) {
            $sql .= "p.id = :identifier";
        } else {
            $sql .= "p.slug = :identifier";
        }
        
        $sql .= " AND p.is_active = 1 GROUP BY p.id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':identifier', $identifier);
            $stmt->execute();
            
            $product = $stmt->fetch();
            
            if ($product) {
                return json_encode([
                    'success' => true,
                    'data' => $product
                ]);
            } else {
                http_response_code(404);
                return json_encode(['error' => 'Product not found']);
            }
        } catch(PDOException $e) {
            http_response_code(500);
            return json_encode(['error' => 'Database error: ' . $e->getMessage()]);
        }
    }
}
?>