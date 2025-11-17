<?php
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Mini E-Commerce Playground</title>
    <meta name="description" content="Browse our collection of high-quality playground equipment for children">
    <link rel="canonical" href="<?php echo BASE_URL; ?>/products">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
     <?php
    // Plugin-CSS einbinden
    echo $pluginManager->renderHook('head_css');
    ?>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="<?php echo BASE_URL; ?>/" class="logo">
                    🧩 Mini E-Commerce
                </a>
                <nav class="nav">
                    <a href="<?php echo BASE_URL; ?>/">Home</a>
                    <a href="<?php echo BASE_URL; ?>/products" class="active">Products</a>
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
                    <li aria-current="page">Products</li>
                </ol>
            </nav>

            <h1>Our Products</h1>

            <!-- Filters -->
            <div class="filters">
                <div class="search-box">
                    <input type="text" id="search-input" placeholder="Search products...">
                    <button id="search-btn">🔍 Search</button>
                </div>
                
                <div class="category-filter">
                    <label for="category-select">Category:</label>
                    <select id="category-select">
                        <option value="">All Categories</option>
                    </select>
                </div>
            </div>

            <!-- Product Grid -->
            <div id="product-loading" class="loading">Loading products...</div>
            <div id="product-grid" class="product-grid"></div>
            <div id="no-products" class="no-products" style="display: none;">
                No products found. Try adjusting your filters.
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Mini E-Commerce Playground. Built for portfolio demonstration.</p>
        </div>
    </footer>

    <script>
        // Load categories
        async function loadCategories() {
            try {
                const response = await fetch('<?php echo BASE_URL; ?>/api/categories');
                const result = await response.json();
                
                if (result.success) {
                    const select = document.getElementById('category-select');
                    result.data.forEach(cat => {
                        const option = document.createElement('option');
                        option.value = cat.slug;
                        option.textContent = `${cat.name} (${cat.product_count})`;
                        select.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }

        // Load products
        async function loadProducts() {
            const category = document.getElementById('category-select').value;
            const search = document.getElementById('search-input').value;
            
            const params = new URLSearchParams();
            if (category) params.append('category', category);
            if (search) params.append('search', search);
            
            const loading = document.getElementById('product-loading');
            const grid = document.getElementById('product-grid');
            const noProducts = document.getElementById('no-products');
            
            loading.style.display = 'block';
            grid.innerHTML = '';
            noProducts.style.display = 'none';
            
            try {
                const response = await fetch(`<?php echo BASE_URL; ?>/api/products?${params}`);
                const result = await response.json();
                
                loading.style.display = 'none';
                
                if (result.success && result.data.length > 0) {
                    result.data.forEach(product => {
                        const card = createProductCard(product);
                        grid.appendChild(card);
                    });
                } else {
                    noProducts.style.display = 'block';
                }
            } catch (error) {
                loading.style.display = 'none';
                console.error('Error loading products:', error);
                grid.innerHTML = '<p class="error">Error loading products. Please try again.</p>';
            }
        }

        // Create product card
        function createProductCard(product) {
            const card = document.createElement('article');
            card.className = 'product-card';
            card.innerHTML = `
                <a href="<?php echo BASE_URL; ?>/product/${product.slug}" class="product-link">
                    <img src="${product.image_url}" alt="${product.name}" loading="lazy">
                    <div class="product-info">
                        <h2>${product.name}</h2>
                        <p class="product-short-desc">${product.short_description}</p>
                        <p class="product-price">€${parseFloat(product.price).toFixed(2)}</p>
                        <span class="product-stock">${product.stock > 0 ? 'In Stock' : 'Out of Stock'}</span>
                    </div>
                </a>
            `;
            
            // Track product view event 
            card.addEventListener('click', () => {
                trackEvent('view_product', {
                    product_id: product.id,
                    product_name: product.name,
                    price: product.price
                });
            });
            
            return card;
        }

        // Placeholder for event tracking
        function trackEvent(eventName, data) {
            console.log('Event:', eventName, data);
        }

        // Event listeners
        document.getElementById('category-select').addEventListener('change', loadProducts);
        document.getElementById('search-btn').addEventListener('click', loadProducts);
        document.getElementById('search-input').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') loadProducts();
        });

        // Initialize
        loadCategories();
        loadProducts();
    </script>
</body>
</html>