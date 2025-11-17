# 🧩 Mini E-Commerce Playground Store

A comprehensive full-stack e-commerce project demonstrating Shopware-like architecture, SEO/SEA optimization, and QA best practices.

## 📋 Project Overview

This project showcases:
- **Full-Stack Development**: PHP backend + MySQL + responsive frontend
- **Marketing Performance**: SEO, tracking, A/B testing, PageSpeed optimization
- **QA Thinking**: Test plans, test cases, bug reports, performance testing

## 🚀 Quick Start

### Prerequisites
- PHP 7.4+ (with PDO and MySQL extensions)
- MySQL 5.7+ or MariaDB
- Apache web server with mod_rewrite enabled
- Composer (optional, for future dependencies)

### Installation Steps

#### 1. Clone or Download the Project
```bash
git clone https://github.com/yourusername/mini-ecommerce.git
cd mini-ecommerce
```

#### 2. Create Project Structure
```
mini-ecommerce/
├── index.php                 # Main router
├── config.php               # Database configuration
├── .htaccess               # URL rewriting
├── controllers/
│   ├── ProductController.php
│   └── CategoryController.php
├── views/
│   ├── home.php
│   ├── products.php
│   ├── product-detail.php
│   ├── category.php
│   └── 404.php
├── assets/
│   ├── css/
│   │   └── styles.css
│   ├── js/
│   └── images/
├── plugins/                 # Module 2
├── tests/                   # Module 6
├── seo/                     # Module 3
├── tracking/                # Module 4
└── performance/             # Module 5
```

#### 3. Setup Database

**Create Database:**
```bash
mysql -u root -p
```

```sql
CREATE DATABASE mini_ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

**Import Schema:**
```bash
mysql -u root -p mini_ecommerce < database/schema.sql
```

Or manually run the SQL from `schema.sql` in your MySQL client.

#### 4. Configure Database Connection

Edit `config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'mini_ecommerce');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('BASE_URL', 'http://localhost/mini-ecommerce');
```

#### 5. Setup Apache Virtual Host (Recommended)

Edit your Apache `httpd.conf` or create a virtual host:

```apache
<VirtualHost *:80>
    ServerName mini-ecommerce.local
    DocumentRoot "/path/to/mini-ecommerce"
    
    <Directory "/path/to/mini-ecommerce">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Add to your hosts file:
```
127.0.0.1 mini-ecommerce.local
```

#### 6. Verify Installation

Visit: `http://localhost/mini-ecommerce` or `http://mini-ecommerce.local`

Test API endpoints:
- `http://localhost/mini-ecommerce/api/products`
- `http://localhost/mini-ecommerce/api/categories`
- `http://localhost/mini-ecommerce/api/products/wooden-adventure-tower`

## 📁 Project Modules

### ✅ MODULE 1 - Core Online Shop (COMPLETED)
- [x] Product listing page with filtering
- [x] Product detail page
- [x] Category navigation
- [x] Search functionality
- [x] Responsive layout
- [x] RESTful API endpoints
- [x] Clean URL structure

**Key Files:**
- `controllers/ProductController.php` - Product business logic
- `controllers/CategoryController.php` - Category business logic
- `views/products.php` - Product listing UI
- `views/product-detail.php` - Product detail UI
- `assets/css/styles.css` - Responsive styling

### 🔌 MODULE 2 - Plugin System (NEXT)
Create a Shopware-like plugin architecture:
- Plugin manifest system
- Hook/event system
- Theme customization
- Example plugins (reviews, badges, SEO data)

### 🔍 MODULE 3 - SEO Performance
- Title tags & meta descriptions
- Canonical URLs
- JSON-LD structured data
- Semantic HTML5
- XML sitemap generation
- robots.txt
- Core Web Vitals optimization

### 📊 MODULE 4 - SEA Performance & Tracking
- Google Tag Manager integration
- Event tracking (view_product, add_to_cart, checkout, purchase)
- Landing page optimization
- Conversion tracking setup

### 🧪 MODULE 5 - CRO / A/B Testing
- A/B test implementation
- Scroll depth tracking
- CTA optimization
- User behavior analytics

### ✅ MODULE 6 - QA Testing
- Comprehensive test plan
- Test case documentation
- Bug report templates
- Performance testing (Lighthouse, GTmetrix)
- Regression testing

## 🛠️ Development Workflow

### Running Locally
```bash
# Start Apache and MySQL
sudo service apache2 start
sudo service mysql start

# Or use XAMPP/MAMP control panel
```

### Testing API Endpoints

**Get all products:**
```bash
curl http://localhost/mini-ecommerce/api/products
```

**Filter by category:**
```bash
curl http://localhost/mini-ecommerce/api/products?category=wooden-playgrounds
```

**Search products:**
```bash
curl http://localhost/mini-ecommerce/api/products?search=swing
```

**Get single product:**
```bash
curl http://localhost/mini-ecommerce/api/products/wooden-adventure-tower
```

## 📊 Database Schema

### Tables
- `products` - Product catalog
- `categories` - Product categories
- `product_categories` - Many-to-many relationship

### Key Features
- Foreign key constraints
- Indexes on frequently queried columns
- Slug-based URLs for SEO
- Metadata fields for SEO optimization

## 🎯 Learning Objectives

### Full-Stack Development
✓ PHP MVC-like architecture
✓ RESTful API design
✓ MySQL database design
✓ Frontend-backend integration
✓ Responsive web design

### Marketing & Performance
- SEO best practices
- Tracking implementation
- Landing page optimization
- A/B testing methodology
- PageSpeed optimization

### QA & Testing
- Test planning
- Test case design
- Bug reporting
- Performance testing
- Regression testing

## 🔄 Next Steps

1. **Complete Module 2**: Build plugin system
2. **Implement Module 3**: SEO optimization
3. **Add Module 4**: Tracking & analytics
4. **Create Module 5**: A/B testing
5. **Document Module 6**: QA testing

## 📝 Notes

- This project uses vanilla JavaScript (no frameworks) for simplicity
- Images use Unsplash placeholders (replace with actual product images)
- Cart functionality is placeholder (extend in later modules)
- Production deployment would require additional security measures

## 🤝 Contributing

This is a portfolio project for learning purposes. Feel free to fork and customize for your own portfolio!

## 📄 License

MIT License - Free to use for educational and portfolio purposes.

---

**Built with ❤️ to showcase E-Commerce, Marketing, and QA skills**