<?php

require_once __DIR__ . '/../../config.php';

class SEOOptimizer extends BasePlugin {
    private $db;
    
    public function init() {
        $this->db = Database::getInstance()->getConnection();
        
        // Register hooks
        $this->registerHook('head_meta', [$this, 'addMetaTags'], 10);
        $this->registerHook('head_structured_data', [$this, 'addStructuredData'], 10);
        $this->registerHook('head_preload', [$this, 'addPreloadTags'], 10);
    }
    
    /**
     * Add comprehensive meta tags
     */
    public function addMetaTags($data = []) {
        $currentUrl = $this->getCurrentUrl();
        $title = $data['title'] ?? 'Mini E-Commerce Playground - Premium Playground Equipment';
        $description = $data['description'] ?? 'Shop high-quality playground equipment for children. Wooden and steel structures, swing sets, climbing frames with free delivery.';
        $image = $data['image'] ?? BASE_URL . '/assets/images/og-default.jpg';
        $type = $data['type'] ?? 'website';
        
        $html = '
    <!-- Basic SEO Meta Tags -->
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="googlebot" content="index, follow">
    <link rel="canonical" href="' . htmlspecialchars($currentUrl) . '">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:locale" content="en_US">
    <meta property="og:type" content="' . htmlspecialchars($type) . '">
    <meta property="og:title" content="' . htmlspecialchars($title) . '">
    <meta property="og:description" content="' . htmlspecialchars($description) . '">
    <meta property="og:url" content="' . htmlspecialchars($currentUrl) . '">
    <meta property="og:site_name" content="Mini E-Commerce Playground">
    <meta property="og:image" content="' . htmlspecialchars($image) . '">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="' . htmlspecialchars($title) . '">
    <meta name="twitter:description" content="' . htmlspecialchars($description) . '">
    <meta name="twitter:image" content="' . htmlspecialchars($image) . '">
    
    <!-- Additional SEO Meta Tags -->
    <meta name="author" content="Mini E-Commerce Playground">
    <meta name="revisit-after" content="7 days">
    <meta name="theme-color" content="#2563eb">
    ';
        
        return $html;
    }
    
    /**
     * Add structured data (JSON-LD)
     */
    public function addStructuredData($data = []) {
        $schemas = [];
        
        // Organization Schema
        $schemas[] = $this->getOrganizationSchema();
        
        // Website Schema
        $schemas[] = $this->getWebsiteSchema();
        
        // Breadcrumb Schema
        if (isset($data['breadcrumbs'])) {
            $schemas[] = $this->getBreadcrumbSchema($data['breadcrumbs']);
        }
        
        // Product Schema 
        if (isset($data['product'])) {
            $schemas[] = $this->getProductSchema($data['product']);
        }
        
        // Product List Schema (if products provided)
        if (isset($data['products'])) {
            $schemas[] = $this->getProductListSchema($data['products']);
        }
        
        $html = '';
        foreach ($schemas as $schema) {
            $html .= '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
        }
        
        return $html;
    }
    
    /**
     * Add preload tags for performance
     */
    public function addPreloadTags() {
        return '
    <!-- Preload Critical Resources -->
    <link rel="preload" href="' . BASE_URL . '/assets/css/styles.css" as="style">
    <link rel="dns-prefetch" href="//images.unsplash.com">
    <link rel="preconnect" href="//images.unsplash.com" crossorigin>
    ';
    }
    
    /**
     * Get Organization Schema
     */
    private function getOrganizationSchema() {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Mini E-Commerce Playground',
            'url' => BASE_URL,
            'logo' => BASE_URL . '/assets/images/logo.png',
            'description' => 'Premium playground equipment supplier',
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'DE'
            ],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'Customer Service',
                'email' => 'info@mini-ecommerce.local'
            ]
        ];
    }
    
    /**
     * Get Website Schema
     */
    private function getWebsiteSchema() {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'Mini E-Commerce Playground',
            'url' => BASE_URL,
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => BASE_URL . '/products?search={search_term_string}',
                'query-input' => 'required name=search_term_string'
            ]
        ];
    }
    
    /**
     * Get Breadcrumb Schema
     */
    private function getBreadcrumbSchema($breadcrumbs) {
        $itemListElement = [];
        
        foreach ($breadcrumbs as $index => $crumb) {
            $itemListElement[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $crumb['name'],
                'item' => $crumb['url']
            ];
        }
        
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement
        ];
    }
    
    /**
     * Get Product Schema
     */
    private function getProductSchema($product) {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product['name'],
            'description' => $product['description'],
            'image' => $product['image_url'],
            'sku' => $product['sku'],
            'brand' => [
                '@type' => 'Brand',
                'name' => 'Mini E-Commerce Playground'
            ],
            'offers' => [
                '@type' => 'Offer',
                'url' => BASE_URL . '/product/' . $product['slug'],
                'priceCurrency' => 'EUR',
                'price' => $product['price'],
                'availability' => $product['stock'] > 0 ? 
                    'https://schema.org/InStock' : 
                    'https://schema.org/OutOfStock',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => 'Mini E-Commerce Playground'
                ]
            ]
        ];
        
        // Add aggregateRating if reviews exist
        if (isset($product['rating']) && isset($product['review_count'])) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $product['rating'],
                'reviewCount' => $product['review_count']
            ];
        }
        
        return $schema;
    }
    
    /**
     * Get Product List Schema
     */
    private function getProductListSchema($products) {
        $itemListElement = [];
        
        foreach ($products as $index => $product) {
            $itemListElement[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'url' => BASE_URL . '/product/' . $product['slug']
            ];
        }
        
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'itemListElement' => $itemListElement
        ];
    }
    
    /**
     * Generate XML Sitemap
     */
    public function generateSitemap() {
        $urls = [];
        
        // Homepage
        $urls[] = [
            'loc' => BASE_URL . '/',
            'lastmod' => date('Y-m-d'),
            'changefreq' => 'daily',
            'priority' => '1.0'
        ];
        
        // Products page
        $urls[] = [
            'loc' => BASE_URL . '/products',
            'lastmod' => date('Y-m-d'),
            'changefreq' => 'daily',
            'priority' => '0.9'
        ];
        
        // Get all products
        $stmt = $this->db->query("SELECT slug, updated_at FROM products WHERE is_active = 1");
        $products = $stmt->fetchAll();
        
        foreach ($products as $product) {
            $urls[] = [
                'loc' => BASE_URL . '/product/' . $product['slug'],
                'lastmod' => date('Y-m-d', strtotime($product['updated_at'])),
                'changefreq' => 'weekly',
                'priority' => '0.8'
            ];
        }
        
        // Get all categories
        $stmt = $this->db->query("SELECT slug, updated_at FROM categories");
        $categories = $stmt->fetchAll();
        
        foreach ($categories as $category) {
            $urls[] = [
                'loc' => BASE_URL . '/category/' . $category['slug'],
                'lastmod' => date('Y-m-d', strtotime($category['updated_at'])),
                'changefreq' => 'weekly',
                'priority' => '0.7'
            ];
        }
        
        return $this->buildSitemapXML($urls);
    }
    
    /**
     * Build sitemap XML
     */
    private function buildSitemapXML($urls) {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        foreach ($urls as $url) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($url['loc']) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . $url['lastmod'] . '</lastmod>' . "\n";
            $xml .= '    <changefreq>' . $url['changefreq'] . '</changefreq>' . "\n";
            $xml .= '    <priority>' . $url['priority'] . '</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }
    
    /**
     * Get current URL
     */
    private function getCurrentUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}
?>