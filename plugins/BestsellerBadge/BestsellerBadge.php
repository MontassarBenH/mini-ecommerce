<?php

class BestsellerBadge extends BasePlugin {
    
    public function init() {
        // Register hooks
        $this->registerHook('product_card_badge', [$this, 'addBadge'], 10);
        $this->registerHook('head_css', [$this, 'addStyles'], 10);
    }
    
    /**
     * Add bestseller badge to product card
     */
    public function addBadge($product) {
        if (!is_array($product)) {
            return '';
        }
        
        // Check if product is bestseller
        if ($this->isBestseller($product['id'])) {
            return $this->loadView('badge', [
                'type' => 'bestseller',
                'label' => '🏆 Bestseller'
            ]);
        }
        
        // Check if product is new
        if ($this->isNew($product)) {
            return $this->loadView('badge', [
                'type' => 'new',
                'label' => '✨ New'
            ]);
        }
        
        // Check if product is on sale
        if ($this->isOnSale($product)) {
            return $this->loadView('badge', [
                'type' => 'sale',
                'label' => '🔥 Sale'
            ]);
        }
        
        return '';
    }
    
    /**
     * Add CSS styles
     */
    public function addStyles() {
        return '<link rel="stylesheet" href="' . $this->getAssetUrl('badge.css') . '">';
    }
    
    /**
     * Check if product is bestseller 
     */
    private function isBestseller($productId) {
        return in_array($productId, [1, 2, 3]);
    }
    
    /**
     * Check if product is new 
     */
    private function isNew($product) {
        if (!isset($product['created_at'])) {
            return false;
        }
        
        $createdDate = strtotime($product['created_at']);
        $thirtyDaysAgo = strtotime('-30 days');
        
        return $createdDate > $thirtyDaysAgo;
    }
    
    /**
     * Check if product is on sale 
     */
    private function isOnSale($product) {
        return in_array($product['id'], [4, 5]);
    }
}
?>