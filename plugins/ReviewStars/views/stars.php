<?php
/**
 * View: Sterne-Anzeige für Reviews (kleiner Block)
 *
 * Erwartete Variablen:
 * @var float|null $rating      
 * @var int        $reviewCount 
 * @var string     $size        
 */

$rating      = isset($rating) ? (float) $rating : null;
$reviewCount = isset($reviewCount) ? (int) $reviewCount : 0;
$size        = isset($size) ? $size : 'small';

// Wenn keine Bewertungen → gar nichts anzeigen
if ($rating === null || $reviewCount === 0) {
    return;
}

$fullStars  = (int) floor($rating);
$hasHalf    = ($rating - $fullStars) >= 0.5;
$emptyStars = 5 - $fullStars - ($hasHalf ? 1 : 0);

$containerClass = $size === 'large' ? 'review-stars-large' : 'review-stars-small';
?>
<div class="review-stars <?= htmlspecialchars($containerClass, ENT_QUOTES, 'UTF-8') ?>">
    <div class="review-stars-icons">
        <?php for ($i = 0; $i < $fullStars; $i++): ?>
            <span class="star star-full">★</span>
        <?php endfor; ?>

        <?php if ($hasHalf): ?>
            <span class="star star-half">★</span>
        <?php endif; ?>

        <?php for ($i = 0; $i < $emptyStars; $i++): ?>
            <span class="star star-empty">★</span>
        <?php endfor; ?>
    </div>

    <div class="review-stars-meta">
        <span class="review-stars-rating">
            <?= number_format($rating, 1, ',', '') ?>/5
        </span>
        <span class="review-stars-count">
            (<?= $reviewCount ?> <?= $reviewCount === 1 ? 'Review' : 'Reviews' ?>)
        </span>
    </div>
</div>
