# 🧩 Mini E-Commerce Playground

A complete full-stack e-commerce system with plugin architecture, SEO optimization, tracking integration, reviews, shopping cart, and comprehensive automated testing.

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D7.4-blue)](https://php.net)
[![PHPUnit](https://img.shields.io/badge/PHPUnit-9.x-green)](https://phpunit.de)
[![License](https://img.shields.io/badge/license-MIT-orange)](LICENSE)

---

## 📋 Overview

A fully functional e-commerce system developed as a portfolio project showcasing:

- **Software Architecture**: Modular plugin system
- **E-Commerce Features**: Shopping cart, reviews, order management
- **SEO Optimization**: JSON-LD, sitemaps, robots.txt, Open Graph, Twitter Cards
- **SEA & Tracking**: Google Tag Manager with e-commerce events
- **QA & Testing**: Smoke tests, regression tests, automated PHPUnit tests, performance testing

---

## 🚀 Key Features

### 🛒 Shopping Cart Plugin
- **Sidebar Cart**: Smooth slide-in interface
- **Cart Operations**: Add, update, and remove items
- **Data Persistence**: Session-based storage with `cart_items` table
- **Checkout Flow**: Modal-based checkout with order creation
- **Dynamic Frontend**: JavaScript-powered interactions
- **Fully Tested**: Complete PHPUnit test coverage

### ⭐ ReviewStars Plugin
- **Custom Review System**: Integrated into all product pages
- **Star Ratings**: 1-5 star rating system with modern UI
- **User Features**:
  - Write detailed reviews
  - Submit star ratings
  - Optional name and title fields
- **Display Features**:
  - Average rating calculation
  - Total review count
  - Individual review listings

### 🔍 SEO Optimizer Plugin
Automated search engine optimization including:

- **Meta Tags**:
  - Dynamic title and description
  - Canonical URLs
  - Robots directives
- **Social Media**:
  - Open Graph tags
  - Twitter Cards
- **Structured Data (JSON-LD)**:
  - Organization
  - Website
  - Search functionality
  - Breadcrumbs
  - Product information
  - AggregateRating
- **Performance**:
  - Sitemap generator (`sitemap.php`)
  - `robots.txt` configuration
  - Critical CSS loading
  - Resource preloading and preconnecting

### 📦 Products & Categories (JSON API)
- `GET /api/products` - List all products
- `GET /api/products/{slug}` - Get product by slug
- `GET /api/categories` - List all categories
- **Features**: Filtering, search, slugs, pricing, stock management, images

### 🎯 SEA / Tracking Module
- **Google Tag Manager** integration
- **E-Commerce Events**:
  - `view_item`
  - `add_to_cart`
  - `purchase`
- **Campaign Templates**: Ready-to-use landing pages
- **Product Tracking**: Tracking-enabled product detail pages

### 🧪 Testing & QA Module

#### Manual Testing Documentation
- Comprehensive test plan
- Smoke test suite
- Regression test cases
- Review-specific tests (`tc-reviews.md`)
- Bug report templates

#### Automated Testing (PHPUnit)
- Unit tests
- API integration tests
- Plugin functionality tests
- Regression tests for known bugs
- Performance tests with time assertions
- Smoke test suite
- Code coverage reports (with Xdebug)
- HTML testdox reports

#### Performance Testing
- API response time validation (<150-200ms)
- Server-side rendering performance
- Lighthouse frontend audits (Google Chrome)

---

## 🏗️ Project Structure

```
mini-ecommerce/
│
├── controllers/
│   ├── ProductController.php
│   ├── CategoryController.php
│   └── CartController.php
│
├── plugins/
│   ├── ShoppingCart/
│   │   ├── ShoppingCart.php
│   │   ├── plugin.json
│   │   ├── assets/
│   │   │   ├── cart.css
│   │   │   └── cart.js
│   │   └── views/cart-modal.php
│   │
│   ├── ReviewStars/
│   │   ├── ReviewStars.php
│   │   ├── plugin.json
│   │   ├── assets/stars.css
│   │   └── views/stars.php
│   │
│   └── SEOOptimizer/
│       ├── SEOOptimizer.php
│       ├── plugin.json
│       └── assets/css/critical.css
│
├── seo/
│   ├── ImageOptimizer.php
│   └── seo-audit.php
│
├── tests/
│   ├── ApiProductsTest.php
│   ├── ApiCategoriesTest.php
│   ├── CartTest.php
│   ├── ReviewStarsTest.php
│   ├── PerformanceApiTest.php
│   ├── PerformanceCartTest.php
│   ├── SmokeTest.php
│   └── RegressionReviewBugTest.php
│
├── views/
│   ├── home.php
│   ├── product-detail.php
│   ├── products.php
│   └── category.php
│
├── phpunit.xml
├── sitemap.php
├── robots.txt
├── composer.json
├── index.php
└── README.md
```

---

## 🔧 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/mini-ecommerce.git
cd mini-ecommerce
```

### 2. Install Composer
If you don't have Composer installed:
- Visit [https://getcomposer.org/download/](https://getcomposer.org/download/)

### 3. Install Dependencies
```bash
# For XAMPP on Windows
"C:\xampp\php\php.exe" composer.phar install

# For standard PHP installations
composer install
```

### 4. Run Initial Tests
```bash
# For XAMPP on Windows
"C:\xampp\php\php.exe" vendor\bin\phpunit

# For standard PHP installations
./vendor/bin/phpunit
```

---

## 🤖 Automated Testing

### Run All Tests
```bash
php vendor/bin/phpunit
```

### Run Specific Test Suites

**Smoke Tests Only**:
```bash
php vendor/bin/phpunit --group smoke
```

**Regression Tests**:
```bash
php vendor/bin/phpunit --group regression
```

**Performance Tests**:
```bash
php vendor/bin/phpunit --group performance
```

### Generate Test Reports

**HTML Test Report**:
```bash
php vendor/bin/phpunit --testdox-html build/test-report.html
```

**Code Coverage Report** (requires Xdebug):
```bash
php vendor/bin/phpunit --coverage-html build/coverage
```

---

## 📝 Manual Testing

### 🔥 Smoke Test Suite

| Test | Status | Description |
|------|--------|-------------|
| Homepage loads | ✅ | CSS/JS loading correctly |
| Product page loads | ✅ | Slug routing functional |
| Add to cart | ✅ | Cart modal appears |
| Checkout opens | ✅ | Modal visible |
| Reviews display | ✅ | Plugin loads correctly |

### 🐞 Regression Tests

**Example: Double Review Bug**
- Issue was reproduced, fixed, and automated
- Test file: `tests/RegressionReviewBugTest.php`

### 📋 Manual Test Cases
Documented in:
- `/docs/tests/tc-reviews.md`
- `/docs/tests/test-plan.md`
- `/docs/tests/testcases/`

**Sample Test Cases**:
- `TC-REV-001` – Submit 5-star review
- `TC-REV-004` – Verify average rating calculation
- `TC-CART-003` – Update item quantity
- `TC-PROD-006` – Validate price formatting

---

## 📊 Performance Testing

### PHPUnit Performance Tests
Response time validation for API controllers:
- `getProducts()` < 200ms
- `getProduct(slug)` < 150ms
- `Cart->addToCart()` < 150ms

### Lighthouse Audits
Frontend performance measured via Chrome DevTools.
Reports saved in: `/docs/performance/lighthouse/`

---

## 🔍 SEO Testing

- **JSON-LD Validation**: Via Google Rich Results Tool
- **Meta Tag Tests**: Automated validation
- **Canonical Check**: Link integrity verification
- **Sitemap/Robots Test**: Crawlability validation
- **SEO Audit Report**: Available at `/seo/seo-audit.php`

---

## 📈 SEA / Tracking

### Google Tag Manager Integration
Injected in `<head>` section with DataLayer events:

**Tracked Events**:
- `view_item` - Product page views
- `add_to_cart` - Cart additions
- `begin_checkout` - Checkout initiation
- `purchase` - Order completion

**Configuration Files**:
- `assets/js/gtm.js`
- `views/product-detail.php`
- `views/home.php`

---

## 🎯 Use Cases

This project demonstrates professional skills for roles such as:

- **Full-Stack Developer**
- **Web Developer**
- **QA/Testing Engineer**
- **Automation Engineer**
- **E-Commerce Developer**
- **DevOps Engineer**

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 📧 Contact

Your Name - [@yourtwitter](https://twitter.com/yourtwitter)

Project Link: [https://github.com/yourusername/mini-ecommerce](https://github.com/yourusername/mini-ecommerce)

---

## 🙏 Acknowledgments

- Plugin architecture inspired by WordPress
- Testing methodology following industry best practices
- SEO implementation based on Google's guidelines
- E-commerce tracking using Google Analytics 4 standards

---

**⭐ If you find this project useful, please consider giving it a star!**