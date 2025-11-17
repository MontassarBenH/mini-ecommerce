<?php
require_once __DIR__ . '/../config.php';

require_once __DIR__ . '/../seo/ImageOptimizer.php';

$slug = $_GET['slug'] ?? '';
$product = null;

$reviewSubmitted = false;
$reviewError     = null;

if (isset($_GET['review_submitted']) && $_GET['review_submitted'] === '1') {
    $reviewSubmitted = true;
}

// Produkt über API laden
if ($slug) {
    $apiUrl = BASE_URL . '/api/products/' . urlencode($slug);
    $json   = @file_get_contents($apiUrl);

    if ($json !== false) {
        $result = json_decode($json, true);
        if (!empty($result['success']) && !empty($result['data'])) {
            $product = $result['data'];
        }
    }
}

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

                // Redirect nach erfolgreichem Submit
                header('Location: ' . BASE_URL . '/product/' . urlencode($product['slug']) . '?review_submitted=1');
                exit;
            } catch (PDOException $e) {
                $reviewError = 'Sorry, your review could not be saved.';
            }
        }
    }
}

// Hilfswerte für SEO / Meta
if ($product) {
    $pageTitle = $product['meta_title'] ?? ($product['name'] . ' - Mini E-Commerce Playground');
    $metaDescription = $product['meta_description'] ?? ($product['short_description'] ?? '');
    $canonicalUrl = BASE_URL . '/product/' . $product['slug'];
} else {
    $pageTitle = 'Product Not Found - Mini E-Commerce Playground';
    $metaDescription = 'Product not found';
    $canonicalUrl = BASE_URL . '/products';
}

// Breadcrumbs für SEOOptimizer
$breadcrumbs = [
    ['name' => 'Home',     'url' => BASE_URL . '/'],
    ['name' => 'Products', 'url' => BASE_URL . '/products'],
];

if ($product) {
    $breadcrumbs[] = [
        'name' => $product['name'],
        'url'  => $canonicalUrl,
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>

    <!-- Basis Meta Description (für Fallback) -->
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8'); ?>">

    <!-- Standard Canonical (SEOOptimizer setzt zusätzlich OG/Twitter/Robots etc.) -->
    <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">

    <?php
    // Google Tag Manager (HEAD)
    echo $pluginManager->renderHook('head_tracking');

    // Performance / Preload
    echo $pluginManager->renderHook('head_preload');

    // Meta Tags (OG, Twitter, Canonical)
    if ($product) {
        echo $pluginManager->renderHook('head_meta', [
            'title'       => $pageTitle,
            'description' => $metaDescription,
            'image'       => $product['image_url'],
            'type'        => 'product',
        ]);
    } else {
        echo $pluginManager->renderHook('head_meta', [
            'title'       => $pageTitle,
            'description' => $metaDescription,
            'type'        => 'website',
        ]);
    }

    // JSON-LD Structured Data
    echo $pluginManager->renderHook('head_structured_data', [
        'breadcrumbs' => $breadcrumbs,
        'product'     => $product ?: null,
    ]);

    // Plugin CSS
    echo $pluginManager->renderHook('head_css');
?>

</head>
<body>
    <?php
    echo $pluginManager->renderHook('body_noscript_tracking');
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
                        <?php
                        // Bild optimiert ausgeben (Unsplash + lazy loading)
                        echo ImageOptimizer::getWebPImageTag(
                            $product['image_url'],
                            $product['name'],
                            'product-image-main',
                            800
                        );
                        ?>
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

                            <!-- ReviewStars-Plugin -->
                            <?php echo $pluginManager->renderHook('product_detail_after_price', $product); ?>
                        </div>

                        <div class="product-stock">
                            <?php if ($product['stock'] > 0): ?>
                                <span class="in-stock">
                                    ✓ In Stock (<?php echo (int)$product['stock']; ?> available)
                                </span>
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
                            >
                                <?php echo $product['stock'] > 0 ? '🛒 Add to Cart' : 'Out of Stock'; ?>
                            </button>
                        </div>
                    </div>
                </article>

                <!-- Review-Erfolgs-/Fehlermeldungen -->
                <?php if ($reviewSubmitted): ?>
                    <p class="alert alert-success">Thank you for your review!</p>
                <?php elseif ($reviewError): ?>
                    <p class="alert alert-error">
                        <?php echo htmlspecialchars($reviewError, ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                <?php endif; ?>

                <!-- Review-Bereich -->
                <div class="product-reviews product-reviews--fullwidth">
                    <?php echo $pluginManager->renderHook('product_detail_reviews', $product); ?>
                </div>

                <script>
                    // Kleines Tracking 
                    const addToCartBtn = document.getElementById('add-to-cart');
                    if (addToCartBtn && <?php echo (int)$product['stock']; ?> > 0) {
                        addToCartBtn.addEventListener('click', () => {
                            console.log('Event: click_add_to_cart', {
                                product_id: <?php echo (int)$product['id']; ?>,
                                product_name: '<?php echo addslashes($product['name']); ?>',
                                price: '<?php echo $product['price']; ?>',
                                currency: 'EUR'
                            });
                        });
                    }
                </script>
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
    echo $pluginManager->renderHook('tracking_js');
    echo $pluginManager->renderHook('before_body_close');
    ?>
</body>
</html>
