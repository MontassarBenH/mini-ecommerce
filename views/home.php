<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini E-Commerce Playground - Premium Playground Equipment</title>
    <meta name="description" content="Shop high-quality playground equipment for children. Wooden and steel playground structures, swing sets, and climbing frames.">
    <link rel="canonical" href="<?php echo BASE_URL; ?>/">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <style>
        .hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .hero p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .hero .btn-primary {
            background: white;
            color: #667eea;
            padding: 1rem 2rem;
            font-size: 1.125rem;
        }
        
        .hero .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }
        
        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 0.5rem;
            border: 1px solid var(--border-color);
            text-align: center;
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .featured-products h2 {
            font-size: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }
    </style>
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
                    <a href="<?php echo BASE_URL; ?>/" class="active">Home</a>
                    <a href="<?php echo BASE_URL; ?>/products">Products</a>
                    <a href="#" id="cart-btn">Cart <span id="cart-count">(0)</span></a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>🎪 Premium Playground Equipment</h1>
            <p>Create unforgettable childhood memories with our high-quality playground structures</p>
            <a href="<?php echo BASE_URL; ?>/products" class="btn-primary">Shop Now</a>
        </div>
    </section>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Features -->
            <section class="features">
                <article class="feature-card">
                    <div class="feature-icon">🌳</div>
                    <h3>Eco-Friendly Materials</h3>
                    <p>FSC-certified wood and recyclable steel for sustainable play</p>
                </article>
                
                <article class="feature-card">
                    <div class="feature-icon">🛡️</div>
                    <h3>Safety Certified</h3>
                    <p>All products meet EN1176 safety standards</p>
                </article>
                
                <article class="feature-card">
                    <div class="feature-icon">🚚</div>
                    <h3>Free Delivery</h3>
                    <p>Free shipping on orders over €500</p>
                </article>
                
                <article class="feature-card">
                    <div class="feature-icon">⚙️</div>
                    <h3>Easy Assembly</h3>
                    <p>Clear instructions and all hardware included</p>
                </article>
            </section>

            <!-- Category Links -->
            <section style="margin: 4rem 0;">
                <h2 style="text-align: center; font-size: 2rem; margin-bottom: 2rem;">
                    Shop by Category
                </h2>
                <div id="category-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                    <!-- Categories loaded dynamically -->
                </div>
            </section>

            <!-- Featured Products -->
            <section class="featured-products">
                <h2>Featured Products</h2>
                <div id="featured-products" class="product-grid">
                    <div class="loading">Loading products...</div>
                </div>
            </section>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Mini E-Commerce Playground. Built for portfolio demonstration.</p>
            <p style="margin-top: 0.5rem; font-size: 0.875rem;">
                <a href="#about">About</a> | 
                <a href="#contact">Contact</a> | 
                <a href="#privacy">Privacy Policy</a>
            </p>
        </div>
    </footer>

    <script>
        // Load categories
        async function loadCategories() {
            try {
                const response = await fetch('<?php echo BASE_URL; ?>/api/categories');
                const result = await response.json();
                
                if (result.success) {
                    const grid = document.getElementById('category-grid');
                    grid.innerHTML = '';
                    
                    result.data.forEach(cat => {
                        if (cat.product_count > 0) {
                            const card = document.createElement('a');
                            card.href = `<?php echo BASE_URL; ?>/category/${cat.slug}`;
                            card.className = 'feature-card';
                            card.style.textDecoration = 'none';
                            card.innerHTML = `
                                <h3>${cat.name}</h3>
                                <p>${cat.product_count} products</p>
                            `;
                            grid.appendChild(card);
                        }
                    });
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }

        // Load featured products
        async function loadFeaturedProducts() {
            try {
                const response = await fetch('<?php echo BASE_URL; ?>/api/products?limit=6');
                const result = await response.json();
                
                const container = document.getElementById('featured-products');
                container.innerHTML = '';
                
                if (result.success && result.data.length > 0) {
                    result.data.forEach(product => {
                        const card = document.createElement('article');
                        card.className = 'product-card';
                        card.innerHTML = `
                            <a href="<?php echo BASE_URL; ?>/product/${product.slug}" class="product-link">
                                <img src="${product.image_url}" alt="${product.name}" loading="lazy">
                                <div class="product-info">
                                    <h3>${product.name}</h3>
                                    <p class="product-short-desc">${product.short_description}</p>
                                    <p class="product-price">€${parseFloat(product.price).toFixed(2)}</p>
                                    <span class="product-stock">${product.stock > 0 ? 'In Stock' : 'Out of Stock'}</span>
                                </div>
                            </a>
                        `;
                        container.appendChild(card);
                    });
                } else {
                    container.innerHTML = '<p class="error">No products available.</p>';
                }
            } catch (error) {
                console.error('Error loading products:', error);
                document.getElementById('featured-products').innerHTML = 
                    '<p class="error">Error loading products.</p>';
            }
        }

        // Initialize
        loadCategories();
        loadFeaturedProducts();
    </script>
</body>
</html>