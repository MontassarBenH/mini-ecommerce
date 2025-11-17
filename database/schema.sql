
DROP TABLE IF EXISTS product_categories;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    parent_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    short_description VARCHAR(500),
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    sku VARCHAR(100) UNIQUE,
    image_url VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    meta_title VARCHAR(255),
    meta_description VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Product-Category relationship (many-to-many)
CREATE TABLE product_categories (
    product_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (product_id, category_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample categories
INSERT INTO categories (name, slug, description) VALUES
('Playground Equipment', 'playground-equipment', 'High-quality playground equipment for children'),
('Wooden Playgrounds', 'wooden-playgrounds', 'Eco-friendly wooden playground structures'),
('Steel Playgrounds', 'steel-playgrounds', 'Durable steel playground equipment'),
('Swing Sets', 'swing-sets', 'Various swing set options'),
('Climbing Frames', 'climbing-frames', 'Safe climbing structures for kids');

-- Insert sample products
INSERT INTO products (name, slug, description, short_description, price, stock, sku, image_url, meta_title, meta_description) VALUES
('Wooden Adventure Tower', 'wooden-adventure-tower', 'A magnificent 3-level wooden play tower with slide, climbing wall, and sandbox. Made from FSC-certified wood with weather-resistant treatment. Perfect for ages 3-12.', 'Premium wooden play tower with slide and climbing wall', 1299.99, 15, 'WPG-001', 'https://images.unsplash.com/photo-1587818541473-862fbb8fd3e8?w=800', 'Wooden Adventure Tower - Premium Playground Equipment', 'Buy the best wooden adventure tower for your backyard. FSC-certified wood, weather-resistant, perfect for kids aged 3-12.'),

('Steel Fortress Climber', 'steel-fortress-climber', 'Heavy-duty steel climbing frame with multiple challenge levels. Powder-coated finish for rust protection. Meets EN1176 safety standards. Suitable for ages 5-14.', 'Durable steel climbing frame with safety certification', 899.99, 22, 'SPG-001', 'https://images.unsplash.com/photo-1595814433015-e0e925afe0d8?w=800', 'Steel Fortress Climber - Professional Grade Playground', 'Professional steel climbing frame meeting EN1176 standards. Rust-proof and built to last.'),

('Double Swing Set Classic', 'double-swing-set-classic', 'Classic double swing set with adjustable chains. Hot-dip galvanized steel frame with rubber-coated seats. Weather-resistant and maintenance-free.', 'Classic double swing with galvanized steel frame', 399.99, 35, 'SWG-001', 'https://images.unsplash.com/photo-1576610616656-d3aa5d1f4534?w=800', 'Double Swing Set - Classic Design for Backyards', 'Galvanized steel double swing set with rubber seats. Weather-resistant and safe.'),

('Rainbow Climbing Arch', 'rainbow-climbing-arch', 'Colorful climbing arch made from sustainable wood. Montessori-inspired design that promotes gross motor skills. Indoor/outdoor use. Ages 1-6.', 'Montessori-inspired wooden climbing arch', 249.99, 48, 'WPG-002', 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?w=800', 'Rainbow Climbing Arch - Montessori Playground Equipment', 'Sustainable wooden climbing arch for toddlers. Montessori-inspired design for motor skill development.'),

('Steel Slide Tower Pro', 'steel-slide-tower-pro', 'Professional-grade steel slide tower with wide stainless steel slide. Platform height 2.5m. Anti-slip steps with handrails. Commercial quality for home use.', 'Professional steel tower with stainless steel slide', 1499.99, 8, 'SPG-002', 'https://images.unsplash.com/photo-1578450671530-5bae6d5457c4?w=800', 'Steel Slide Tower Pro - Commercial Quality Playground', 'Commercial-grade steel slide tower with 2.5m platform. Stainless steel slide and safety features.'),

('Wooden Monkey Bars', 'wooden-monkey-bars', 'Traditional monkey bars made from pressure-treated pine. Adjustable height settings. Encourages upper body strength. Easy assembly with included hardware.', 'Classic wooden monkey bars for outdoor fun', 329.99, 28, 'WPG-003', 'https://images.unsplash.com/photo-1516627145497-ae6968895b74?w=800', 'Wooden Monkey Bars - Build Strength Through Play', 'Pressure-treated pine monkey bars with adjustable height. Develops upper body strength.');

-- Link products to categories
INSERT INTO product_categories (product_id, category_id) VALUES
(1, 2), (1, 1), -- Wooden Adventure Tower
(2, 3), (2, 1), (2, 5), -- Steel Fortress Climber
(3, 4), (3, 1), -- Double Swing Set
(4, 2), (4, 5), -- Rainbow Climbing Arch
(5, 3), (5, 1), -- Steel Slide Tower
(6, 2), (6, 5); -- Wooden Monkey Bars