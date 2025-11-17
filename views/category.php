<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="page-title">Category - Mini E-Commerce</title>
    <meta name="description" id="meta-description" content="Browse products in this category">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="<?php echo BASE_URL; ?>/" class="logo">🧩 Mini E-Commerce</a>
                <nav class="nav">
                    <a href="<?php echo BASE_URL; ?>/">Home</a>
                    <a href="<?php echo BASE_URL; ?>/products" class="active">Products</a>
                    <a href="#" id="cart-btn">Cart <span id="cart-count">(0)</span></a>
                </nav>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <nav class="breadcrumb">
                <ol>
                    <li><a href="<?php echo BASE_URL; ?>/">Home</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/products">Products</a></li>
                    <li aria-current="page" id="category-name">Loading...</li>
                </ol>
            </nav>

            <h1 id="category-title">Loading...</h1>
            <p id="category-description"></p>

            <div id="product-loading" class="loading">Loading products...</div>
            <div id="product-grid" class="product-grid"></div>
            <div id="no-products" class="no-products" style="display: none;">
                No products in this category.
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Mini E-Commerce Playground</p>
        </div>
    </footer>

    <script>
        const slug = '<?php echo htmlspecialchars($_GET['slug'] ?? ''); ?>';
        
        async function loadCategory() {
            try {
                const response = await fetch(`<?php echo BASE_URL; ?>/api/categories/${slug}`);
                const result = await response.json();
                
                if (result.success) {
                    const category = result.data;
                    document.getElementById('page-title').textContent = category.name;
                    document.getElementById('category-name').textContent = category.name;
                    document.getElementById('category-title').textContent = category.name;
                    document.getElementById('category-description').textContent = category.description;
                    
                    loadProducts(slug);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
        
        async function loadProducts(category) {
            const loading = document.getElementById('product-loading');
            const grid = document.getElementById('product-grid');
            const noProducts = document.getElementById('no-products');
            
            try {
                const response = await fetch(`<?php echo BASE_URL; ?>/api/products?category=${category}`);
                const result = await response.json();
                
                loading.style.display = 'none';
                
                if (result.success && result.data.length > 0) {
                    result.data.forEach(product => {
                        const card = document.createElement('article');
                        card.className = 'product-card';
                        card.innerHTML = `
                            <a href="<?php echo BASE_URL; ?>/product/${product.slug}">
                                <img src="${product.image_url}" alt="${product.name}" loading="lazy">
                                <div class="product-info">
                                    <h2>${product.name}</h2>
                                    <p class="product-short-desc">${product.short_description}</p>
                                    <p class="product-price">€${parseFloat(product.price).toFixed(2)}</p>
                                </div>
                            </a>
                        `;
                        grid.appendChild(card);
                    });
                } else {
                    noProducts.style.display = 'block';
                }
            } catch (error) {
                loading.style.display = 'none';
                console.error('Error:', error);
            }
        }
        
        loadCategory();
    </script>
</body>
</html>