<?php
require_once __DIR__ . '/../../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Body-Hooks vom Plugin (falls du sie nicht schon woanders hast)
echo $pluginManager->renderHook('body_noscript_tracking');
echo $pluginManager->renderHook('after_body_open');
?>

<header class="header">
    <div class="container">
        <div class="header-content">
            <!-- Logo -->
            <a href="<?php echo BASE_URL; ?>/" class="logo">
                🧩 Mini E-Commerce
            </a>

            <!-- Hauptnavigation (links) -->
            <nav class="nav nav-main">
                <a href="<?php echo BASE_URL; ?>/" class="nav-link">Home</a>
                <a href="<?php echo BASE_URL; ?>/products" class="nav-link">Products</a>

                <!-- Cart immer als Plugin-Sidebar -->
                <a href="#" id="cart-btn" class="nav-link">
                    Cart <span id="cart-count">(0)</span>
                </a>
            </nav>

            <!-- Benutzerbereich (rechts) -->
            <div class="user-area">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="user-greeting">
                        Hello, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Customer'); ?>
                    </span>
                    <a href="<?php echo BASE_URL; ?>/my-orders" class="nav-link nav-small">
                        My Orders
                    </a>
                    <a href="<?php echo BASE_URL; ?>/logout" class="nav-link nav-small">
                        Logout
                    </a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/login" class="nav-link nav-small">
                        Login
                    </a>
                    <a href="<?php echo BASE_URL; ?>/register" class="nav-link nav-small">
                        Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
