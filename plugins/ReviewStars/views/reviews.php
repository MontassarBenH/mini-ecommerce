<?php
/**
 * @var float|null $rating
 * @var int        $reviewCount
 * @var array      $reviews
 * @var array      $product
 */

$hasReviews = $reviewCount > 0 && $rating !== null;
?>
<section class="reviews-section">
    <div class="reviews-header-row">
        <h2 class="reviews-heading">Customer Reviews</h2>

        <?php if ($hasReviews): ?>
            <div class="reviews-summary-card">
                <?php
                $fullStars  = (int) floor($rating);
                $hasHalf    = ($rating - $fullStars) >= 0.5;
                $emptyStars = 5 - $fullStars - ($hasHalf ? 1 : 0);
                ?>
                <div class="review-stars review-stars-large">
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
                            • <?= $reviewCount ?> <?= $reviewCount === 1 ? 'Review' : 'Reviews' ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p class="reviews-empty-text">
                No reviews yet. Be the first to share your experience with this product.
            </p>
        <?php endif; ?>
    </div>

    <div class="reviews-content-grid">
        <!-- Linke Seite: Liste der Reviews -->
        <div class="reviews-list-column">
            <?php if ($hasReviews): ?>
                <div class="reviews-list">
                    <?php foreach ($reviews as $review): ?>
                        <article class="review-item-card">
                            <header class="review-item-header">
                                <div class="review-item-author-block">
                                    <div class="review-avatar-placeholder">
                                        <?= strtoupper(substr($review['author_name'] ?: 'A', 0, 1)) ?>
                                    </div>

                                    <div>
                                        <strong class="review-author">
                                            <?= htmlspecialchars($review['author_name'] ?: 'Anonymous', ENT_QUOTES, 'UTF-8') ?>
                                        </strong>
                                        <div class="review-date">
                                            <?= date('d.m.Y', strtotime($review['created_at'])) ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="review-rating">
                                    <?php
                                    $rFull  = (int) $review['rating'];
                                    $rEmpty = 5 - $rFull;
                                    for ($i = 0; $i < $rFull; $i++): ?>
                                        <span class="star star-full">★</span>
                                    <?php endfor;
                                    for ($i = 0; $i < $rEmpty; $i++): ?>
                                        <span class="star star-empty">★</span>
                                    <?php endfor; ?>
                                </div>
                            </header>

                            <?php if (!empty($review['title'])): ?>
                                <h3 class="review-title">
                                    <?= htmlspecialchars($review['title'], ENT_QUOTES, 'UTF-8') ?>
                                </h3>
                            <?php endif; ?>

                            <?php if (!empty($review['comment'])): ?>
                                <p class="review-comment">
                                    <?= nl2br(htmlspecialchars($review['comment'], ENT_QUOTES, 'UTF-8')) ?>
                                </p>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="reviews-list reviews-list--empty">
                    <p>No reviews yet. Your feedback could be the first one!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Rechte Seite: Formular -->
        <div class="review-form-column">
            <div class="review-form-card">
                <h3 class="review-form-heading">Write a review</h3>
                <p class="review-form-subtext">
                    Share your experience with other customers. Your feedback helps others make better decisions.
                </p>
                <form method="post" class="review-form">
                    <input type="hidden" name="review_form" value="1">
                    <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">

                    <div class="form-row">
                        <label for="review-author">Your name (optional)</label>
                        <input
                            type="text"
                            id="review-author"
                            name="author_name"
                            maxlength="100"
                            value="<?= isset($_POST['author_name']) ? htmlspecialchars($_POST['author_name'], ENT_QUOTES, 'UTF-8') : '' ?>"
                        >
                    </div>

                   <?php
                        $selectedRating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
                        ?>
                        <div class="form-row">
                            <label for="review-rating">Your rating *</label>
                            <div class="rating-stars-wrapper">
                                <div class="rating-stars" data-current="<?= $selectedRating ?>">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <button
                                            type="button"
                                            class="rating-star<?= $i <= $selectedRating ? ' is-filled' : '' ?>"
                                            data-value="<?= $i ?>"
                                            aria-label="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>"
                                        >
                                            ★
                                        </button>
                                    <?php endfor; ?>
                                </div>
                                <input
                                    type="hidden"
                                    name="rating"
                                    id="review-rating"
                                    value="<?= $selectedRating ?: '' ?>"
                                    required
                                >
                            </div>
                        </div>


                    <div class="form-row">
                        <label for="review-title">Title (optional)</label>
                        <input
                            type="text"
                            id="review-title"
                            name="title"
                            maxlength="255"
                            placeholder="Short summary of your experience"
                            value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8') : '' ?>"
                        >
                    </div>

                    <div class="form-row">
                        <label for="review-comment">Your review *</label>
                        <textarea
                            id="review-comment"
                            name="comment"
                            rows="4"
                            placeholder="What did you like or dislike? How is the quality, size, installation, etc.?"
                            required
                        ><?= isset($_POST['comment']) ? htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8') : '' ?></textarea>
                    </div>

                    <button type="submit" class="btn-primary review-submit-btn">
                        Submit review
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const containers = document.querySelectorAll('.rating-stars');

    containers.forEach(function (container) {
        const wrapper = container.closest('.rating-stars-wrapper');
        if (!wrapper) return;

        const hiddenInput = wrapper.querySelector('input[name="rating"]');
        const stars = container.querySelectorAll('.rating-star');

        function setRating(value) {
            const rating = parseInt(value) || 0;
            hiddenInput.value = rating || '';
            container.dataset.current = rating;

            stars.forEach(function (star) {
                const starValue = parseInt(star.dataset.value);
                star.classList.toggle('is-filled', starValue <= rating);
                star.classList.remove('is-hovered');
            });
        }

        function previewRating(value) {
            const rating = parseInt(value) || 0;
            stars.forEach(function (star) {
                const starValue = parseInt(star.dataset.value);
                star.classList.toggle('is-hovered', starValue <= rating);
            });
        }

        // Initialer Zustand 
        setRating(hiddenInput.value);

        stars.forEach(function (star) {
            const value = star.dataset.value;

            star.addEventListener('mouseenter', function () {
                previewRating(value);
            });

            star.addEventListener('mouseleave', function () {
                previewRating(container.dataset.current || 0);
            });

            star.addEventListener('click', function () {
                setRating(value);
            });

            // Tastatur-Zugänglichkeit
            star.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    setRating(value);
                }
            });
        });
    });
});
</script>

</section>
