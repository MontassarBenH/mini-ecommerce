<?php

class ReviewStars extends BasePlugin {

    public function init() {
        // Hooks für Sterne
        $this->registerHook('product_card_after_price', [$this, 'addStarsToCard'], 10);
        $this->registerHook('product_detail_after_price', [$this, 'addStarsToDetail'], 10);

        // CSS
        $this->registerHook('head_css', [$this, 'addStyles'], 10);

        // Neuer Hook: kompletter Review-Bereich auf Detailseite
        $this->registerHook('product_detail_reviews', [$this, 'renderReviewsSection'], 10);
    }

    /**
     * Kleine Hilfsfunktion: DB holen
     */
    private function getDb() {
        // Database-Klasse kommt aus config.php
        return Database::getInstance()->getConnection();
    }

    /**
     * Add stars to product card
     */
    public function addStarsToCard($product) {
        if (!is_array($product)) {
            return $product;
        }

        $rating      = $this->calculateRating($product['id']);
        $reviewCount = $this->getReviewCount($product['id']);

        // Wenn noch keine Bewertungen: auf Karten gar nichts anzeigen (oder Placeholder, wie du magst)
        if ($rating === null || $reviewCount === 0) {
            return '';
        }

        return $this->loadView('stars', [
            'rating'      => $rating,
            'reviewCount' => $reviewCount,
            'size'        => 'small'
        ]);
    }

    /**
     * Add stars to product detail page
     */
    public function addStarsToDetail($product) {
        if (!is_array($product)) {
            return $product;
        }

        $rating      = $this->calculateRating($product['id']);
        $reviewCount = $this->getReviewCount($product['id']);

        if ($rating === null || $reviewCount === 0) {
            // Detailseite: Du könntest hier auch "No reviews yet" anzeigen, ich lasse es clean
            return '';
        }

        return $this->loadView('stars', [
            'rating'      => $rating,
            'reviewCount' => $reviewCount,
            'size'        => 'large'
        ]);
    }

    /**
     * Add CSS styles
     */
    public function addStyles() {
        return '<link rel="stylesheet" href="' . $this->getAssetUrl('stars.css') . '">';
    }

    /**
     * Calculate product rating (echte DB-Daten)
     */
    private function calculateRating($productId) {
        $db = $this->getDb();

        $stmt = $db->prepare("
            SELECT AVG(rating) AS avg_rating
            FROM product_reviews
            WHERE product_id = :id AND is_approved = 1
        ");
        $stmt->execute([':id' => $productId]);
        $row = $stmt->fetch();

        if (!$row || $row['avg_rating'] === null) {
            return null; // keine Bewertungen
        }

        $rating = (float) $row['avg_rating'];

        // Auf 0.5-Schritte runden
        return round($rating * 2) / 2;
    }

    /**
     * Get review count (echte DB-Daten)
     */
    private function getReviewCount($productId) {
        $db = $this->getDb();

        $stmt = $db->prepare("
            SELECT COUNT(*) AS cnt
            FROM product_reviews
            WHERE product_id = :id AND is_approved = 1
        ");
        $stmt->execute([':id' => $productId]);
        $row = $stmt->fetch();

        return $row ? (int) $row['cnt'] : 0;
    }

    /**
     * Alle Reviews für Produkt holen
     */
    private function getReviews($productId) {
        $db = $this->getDb();

        $stmt = $db->prepare("
            SELECT author_name, rating, title, comment, created_at
            FROM product_reviews
            WHERE product_id = :id AND is_approved = 1
            ORDER BY created_at DESC
            LIMIT 10
        ");
        $stmt->execute([':id' => $productId]);
        return $stmt->fetchAll();
    }

    /**
     * Review-Sektion für Produktdetailseite rendern
     */
    public function renderReviewsSection($product) {
        if (!is_array($product)) {
            return '';
        }

        $productId   = $product['id'];
        $rating      = $this->calculateRating($productId);
        $reviewCount = $this->getReviewCount($productId);
        $reviews     = $this->getReviews($productId);

        return $this->loadView('reviews', [
            'rating'      => $rating,
            'reviewCount' => $reviewCount,
            'reviews'     => $reviews,
            'product'     => $product
        ]);
    }
}
?>
