<?php
require_once __DIR__ . '/../config.php';

$slug = $_GET['slug'] ?? '';
$product = null;

$reviewSubmitted = false;
$reviewError = null;

// Wurde nach erfolgreichem Review-Submit redirectet?
if (isset($_GET['review_submitted']) && $_GET['review_submitted'] === '1') {
    $reviewSubmitted = true;
}

// Produkt über API laden
if ($slug) {
    $apiUrl = BASE_URL . '/api/products/' . urlencode($slug);
    $json = @file_get_contents($apiUrl);
    if ($json !== false) {
        $result = json_decode($json, true);
        if (!empty($result['success']) && !empty($result['data'])) {
            $product = $result['data'];
        }
    }
}

// ⭐ Review-Formular verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $product) {
    if (isset($_POST['review_form'])) {
        $rating  = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
        $author  = trim($_POST['author_name'] ?? '');
        $title   = trim($_POST['title'] ?? '');
        $comment = trim($_POST['comment'] ?? '');

        if ($rating < 1 || $rating > 5) {
            $reviewError = 'Please select a rating between 1 and 5.';
        } elseif ($comment === '') {
            $reviewError = 'Please write a short comment.';
        } else {
            try {
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("
                    INSERT INTO product_reviews (product_id, rating, title, comment, author_name)
                    VALUES (:pid, :rating, :title, :comment, :author)
                ");
                $stmt->execute([
                    ':pid'     => $product['id'],
                    ':rating'  => $rating,
                    ':title'   => $title ?: null,
                    ':comment' => $comment,
                    ':author'  => $author ?: null,
                ]);

                // 🎯 Redirect nach erfolgreichem Submit (PRG-Pattern)
                header('Location: ' . BASE_URL . '/product/' . urlencode($product['slug']) . '?review_submitted=1');
                exit;
            } catch (PDOException $e) {
                // In echt: loggen
                $reviewError = 'Sorry, your review could not be saved.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php
        if ($product) {
            echo htmlspecialchars($product['meta_title'] ?? ($product['name'] . ' - Mini E-Commerce Playground'));
        } else {
            echo 'Product Not Found - Mini E-Commerce Playground';
        }
        ?>
    </title>
    <meta name="description" content="<?php
        if ($product) {
            echo htmlspecialchars($product['meta_description'] ?? ($product['short_description'] ?? ''));
        } else {
            echo 'Product not found';
        }
    ?>">
    <link rel="canonical" href="<?php
        if ($product) {
            echo BASE_URL . '/product/' . htmlspecialchars($product['slug']);
        } else {
            echo BASE_URL . '/products';
        }
    ?>">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">

    <?php
    echo $pluginManager->renderHook('head_css');
    ?>
</head>
<body>
    <?php
    // ✨ Scripts direkt nach <body> (z.B. cart.js vom ShoppingCart-Plugin)
    echo $pluginManager->renderHook('after_body_open');
    ?>

    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="<?php echo BASE_URL; ?>/" class="logo">
                    🧩 Mini E-Commerce
                </a>
                <nav class="nav">
                    <a href="<?php echo BASE_URL; ?>/">Home</a>
                    <a href="<?php echo BASE_URL; ?>/products">Products</a>
                    <a href="#" id="cart-btn">Cart <span id="cart-count">(0)</span></a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Breadcrumb -->
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <ol>
                    <li><a href="<?php echo BASE_URL; ?>/">Home</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/products">Products</a></li>
                    <?php if ($product): ?>
                        <li aria-current="page"><?php echo htmlspecialchars($product['name']); ?></li>
                    <?php else: ?>
                        <li aria-current="page">Product Not Found</li>
                    <?php endif; ?>
                </ol>
            </nav>

            <?php if (!$product): ?>
                <div class="error">
                    <h1>Product Not Found</h1>
                    <p>The product you're looking for doesn't exist.</p>
                    <a href="<?php echo BASE_URL; ?>/products" class="btn-primary">Back to Products</a>
                </div>
            <?php else: ?>
                <!-- Produkt-Content -->
                <article class="product-detail">
                    <div class="product-image">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>"
                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>

                    <div class="product-details">
                        <h1><?php echo htmlspecialchars($product['name']); ?></h1>

                        <div class="product-meta">
                            <span>SKU: <?php echo htmlspecialchars($product['sku']); ?></span>
                            <?php
                            $categories = !empty($product['category_names'])
                                ? explode(', ', $product['category_names'])
                                : [];
                            if (!empty($categories)): ?>
                                <span>Category: <?php echo htmlspecialchars($categories[0]); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="product-price">
                            €<?php echo number_format((float)$product['price'], 2); ?>

                            <!-- ⭐ ReviewStars-Plugin (neben dem Preis) -->
                            <?php echo $pluginManager->renderHook('product_detail_after_price', $product); ?>
                        </div>

                        <div class="product-stock">
                            <?php if ($product['stock'] > 0): ?>
                                <span class="in-stock">✓ In Stock (<?php echo (int)$product['stock']; ?> available)</span>
                            <?php else: ?>
                                <span class="out-of-stock">Out of Stock</span>
                            <?php endif; ?>
                        </div>

                        <div class="product-description">
                            <h2>Description</h2>
                            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                        </div>

                        <div class="product-actions">
                            <button
                                class="btn-primary add-to-cart-btn"
                                id="add-to-cart"
                                <?php echo $product['stock'] == 0 ? 'disabled' : ''; ?>
                                data-product-id="<?php echo (int)$product['id']; ?>"
                                data-product-name="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>"
                                data-product-price="<?php echo number_format((float)$product['price'], 2, '.', ''); ?>"
                                data-product-image="<?php echo htmlspecialchars($product['image_url'], ENT_QUOTES, 'UTF-8'); ?>"
                                data-product-slug="<?php echo htmlspecialchars($product['slug'], ENT_QUOTES, 'UTF-8'); ?>"
                            >
                                <?php echo $product['stock'] > 0 ? '🛒 Add to Cart' : 'Out of Stock'; ?>
                            </button>
                        </div>
                    </div>
                </article>

                <!-- ⭐ Review-Bereich jetzt unter dem ganzen Produkt, volle Breite -->
                <?php if ($reviewSubmitted): ?>
                    <p class="alert alert-success">Thank you for your review!</p>
                <?php elseif ($reviewError): ?>
                    <p class="alert alert-error">
                        <?= htmlspecialchars($reviewError, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                <?php endif; ?>

                <div class="product-reviews product-reviews--fullwidth">
                    <?php echo $pluginManager->renderHook('product_detail_reviews', $product); ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Mini E-Commerce Playground. Built for portfolio demonstration.</p>
        </div>
    </footer>

    <?php
    echo $pluginManager->renderHook('before_body_close');
    ?>
</body>
</html>
