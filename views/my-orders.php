<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Mini E-Commerce</title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <style>
        .account-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }
        .account-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .user-info h1 {
            margin: 0;
        }
        .user-welcome {
            color: #666;
            font-size: 14px;
        }
        .btn-logout {
            padding: 10px 20px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-logout:hover {
            background: #c82333;
        }
        .orders-list {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .order-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: box-shadow 0.2s;
        }
        .order-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .order-number {
            font-weight: bold;
            color: #007bff;
            font-size: 16px;
        }
        .order-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #cce5ff; color: #004085; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .order-details {
            display: flex;
            justify-content: space-between;
            color: #666;
            font-size: 14px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .order-total {
            font-weight: bold;
            color: #333;
            font-size: 16px;
        }
        .no-orders {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        .no-orders h3 {
            margin-bottom: 10px;
            color: #666;
        }
        .loading {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .account-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .order-header {
                flex-direction: column;
            }
            .order-details {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/partials/header.php'; ?>
    
    <div class="account-container">
        <div class="account-header">
            <div class="user-info">
                <div>
                    <h1>My Orders</h1>
                    <span class="user-welcome">Welcome, <?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'User' ?></span>
                </div>
            </div>
            <button class="btn-logout" onclick="logout()">Logout</button>
        </div>
        
        <div class="orders-list">
            <div id="loading" class="loading">Loading orders...</div>
            <div id="orders-container"></div>
        </div>
    </div>
    
    <?php include __DIR__ . '/partials/footer.php'; ?>
    
    <script>
        async function loadOrders() {
            const loading = document.getElementById('loading');
            const container = document.getElementById('orders-container');
            
            try {
                const response = await fetch('<?= BASE_URL ?>/api/orders');
                const result = await response.json();
                
                loading.style.display = 'none';
                
                if (!result.success) {
                    if (result.error === 'NOT_AUTHENTICATED') {
                        window.location.href = '<?= BASE_URL ?>/login';
                        return;
                    }
                    container.innerHTML = '<p class="no-orders">Error loading orders</p>';
                    return;
                }
                
                if (!result.data || result.data.length === 0) {
                    container.innerHTML = `
                        <div class="no-orders">
                            <h3>No orders yet</h3>
                            <p>Start shopping to see your orders here!</p>
                            <a href="<?= BASE_URL ?>/" class="btn-primary" style="display: inline-block; margin-top: 20px; padding: 12px 30px; text-decoration: none;">Start Shopping</a>
                        </div>
                    `;
                    return;
                }
                
                const ordersHTML = result.data.map(order => {
                    const date = new Date(order.date).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                    
                    return `
                        <div class="order-card" onclick="viewOrder(${order.id})">
                            <div class="order-header">
                                <span class="order-number">${order.order_number}</span>
                                <span class="order-status status-${order.status}">${order.status}</span>
                            </div>
                            <div class="order-details">
                                <span>📅 ${date}</span>
                                <span>💳 ${order.payment_method || 'N/A'}</span>
                                <span class="order-total">Total: €${order.total.toFixed(2)}</span>
                            </div>
                        </div>
                    `;
                }).join('');
                
                container.innerHTML = ordersHTML;
                
            } catch (error) {
                loading.style.display = 'none';
                container.innerHTML = '<p class="no-orders">Error loading orders. Please try again later.</p>';
                console.error('Error:', error);
            }
        }
        
        function viewOrder(orderId) {
            alert('Order details view - Coming soon!\nOrder ID: ' + orderId);
        }
        
        async function logout() {
            if (!confirm('Are you sure you want to logout?')) {
                return;
            }
            
            try {
                const response = await fetch('<?= BASE_URL ?>/api/auth', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ action: 'logout' })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    window.location.href = '<?= BASE_URL ?>/';
                } else {
                    alert('Logout failed. Please try again.');
                }
            } catch (error) {
                console.error('Logout error:', error);
                alert('Logout failed. Please try again.');
            }
        }
        
        // Load orders on page load
        loadOrders();
    </script>
</body>
</html>