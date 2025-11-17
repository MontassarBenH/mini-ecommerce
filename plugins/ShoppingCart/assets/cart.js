// plugins/ShoppingCart/assets/cart.js

(function() {
    'use strict';
    
    const API_BASE = window.location.origin + '/mini-ecommerce/api/cart';
    
    // Cart state
    let cartData = {
        items: [],
        total: 0,
        count: 0
    };
    
    // Initialize cart
    function initCart() {
        loadCart();
        setupEventListeners();
    }
    
    // Setup event listeners
    function setupEventListeners() {
        // Cart button
        const cartBtn = document.getElementById('cart-btn');
        if (cartBtn) {
            cartBtn.addEventListener('click', (e) => {
                e.preventDefault();
                openCart();
            });
        }
        
        // Close cart
        document.getElementById('close-cart')?.addEventListener('click', closeCart);
        document.getElementById('cart-overlay')?.addEventListener('click', closeCart);
        
        // Clear cart
        document.getElementById('clear-cart-btn')?.addEventListener('click', clearCart);
        
        // Checkout
        document.getElementById('checkout-btn')?.addEventListener('click', openCheckout);
        document.getElementById('close-checkout')?.addEventListener('click', closeCheckout);
        
        // Checkout form
        document.getElementById('checkout-form')?.addEventListener('submit', handleCheckout);
        
        // Success modal
        document.getElementById('close-success')?.addEventListener('click', closeSuccess);
        
        // Add to cart buttons (delegate event)
        document.addEventListener('click', (e) => {
            if (e.target.id === 'add-to-cart' || e.target.closest('#add-to-cart')) {
                const btn = e.target.id === 'add-to-cart' ? e.target : e.target.closest('#add-to-cart');
                const productId = btn.dataset.productId;
                if (productId) {
                    addToCart(productId);
                }
            }
        });
    }
    
    // Load cart from API
    async function loadCart() {
        try {
            const response = await fetch(API_BASE);
            const data = await response.json();
            
            if (data.success) {
                cartData = data;
                updateCartUI();
            }
        } catch (error) {
            console.error('Error loading cart:', error);
        }
    }
    
    // Add to cart
    async function addToCart(productId, quantity = 1) {
        try {
            const response = await fetch(API_BASE, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                showNotification('✓ Product added to cart!', 'success');
                loadCart();
                openCart();
            } else {
                showNotification('❌ ' + data.message, 'error');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            showNotification('❌ Failed to add to cart', 'error');
        }
    }
    
    // Update quantity
    async function updateQuantity(productId, quantity) {
        try {
            const response = await fetch(API_BASE, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                loadCart();
            } else {
                showNotification('❌ ' + data.message, 'error');
            }
        } catch (error) {
            console.error('Error updating cart:', error);
        }
    }
    
    // Remove from cart
    async function removeFromCart(productId) {
        try {
            const response = await fetch(`${API_BASE}/${productId}`, {
                method: 'DELETE'
            });
            
            const data = await response.json();
            
            if (data.success) {
                showNotification('Product removed from cart', 'success');
                loadCart();
            }
        } catch (error) {
            console.error('Error removing from cart:', error);
        }
    }
    
    // Clear cart
    async function clearCart() {
        if (!confirm('Are you sure you want to clear your cart?')) {
            return;
        }
        
        try {
            const response = await fetch(API_BASE, {
                method: 'DELETE'
            });
            
            const data = await response.json();
            
            if (data.success) {
                showNotification('Cart cleared', 'success');
                loadCart();
            }
        } catch (error) {
            console.error('Error clearing cart:', error);
        }
    }
    
    // Update cart UI
    function updateCartUI() {
        updateCartCount();
        updateCartItems();
        updateCartTotal();
    }
    
    // Update cart count badge
    function updateCartCount() {
        const countEl = document.getElementById('cart-count');
        if (countEl) {
            countEl.textContent = `(${cartData.count})`;
        }
    }
    
    // Update cart items list
    function updateCartItems() {
        const itemsEl = document.getElementById('cart-items');
        if (!itemsEl) return;
        
        if (cartData.items.length === 0) {
            itemsEl.innerHTML = '<div class="cart-empty"><p>🛒 Your cart is empty</p></div>';
            document.getElementById('checkout-btn').disabled = true;
            return;
        }
        
        document.getElementById('checkout-btn').disabled = false;
        
        const html = cartData.items.map(item => `
            <div class="cart-item">
                <img src="${item.image_url}" alt="${item.name}" class="cart-item-image">
                <div class="cart-item-details">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">€${parseFloat(item.price).toFixed(2)}</div>
                    <div class="cart-item-quantity">
                        <button class="qty-btn" onclick="window.cartUpdateQty(${item.product_id}, ${item.quantity - 1})">−</button>
                        <span class="qty-value">${item.quantity}</span>
                        <button class="qty-btn" onclick="window.cartUpdateQty(${item.product_id}, ${item.quantity + 1})" ${item.quantity >= item.stock ? 'disabled' : ''}>+</button>
                    </div>
                    <button class="cart-item-remove" onclick="window.cartRemove(${item.product_id})">Remove</button>
                </div>
                <div class="cart-item-subtotal">
                    €${parseFloat(item.subtotal).toFixed(2)}
                </div>
            </div>
        `).join('');
        
        itemsEl.innerHTML = html;
    }
    
    // Update cart total
    function updateCartTotal() {
        const totalEl = document.getElementById('cart-total-amount');
        if (totalEl) {
            totalEl.textContent = `€${parseFloat(cartData.total).toFixed(2)}`;
        }
    }
    
    // Open cart sidebar
    function openCart() {
        document.getElementById('cart-sidebar').classList.add('open');
        document.getElementById('cart-overlay').style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    
    // Close cart sidebar
    function closeCart() {
        document.getElementById('cart-sidebar').classList.remove('open');
        document.getElementById('cart-overlay').style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Open checkout modal
    function openCheckout() {
        if (cartData.items.length === 0) return;
        
        // Update checkout summary
        const checkoutItems = document.getElementById('checkout-items');
        const html = cartData.items.map(item => `
            <div class="checkout-item">
                <span>${item.name} × ${item.quantity}</span>
                <span>€${parseFloat(item.subtotal).toFixed(2)}</span>
            </div>
        `).join('');
        
        checkoutItems.innerHTML = html;
        document.getElementById('checkout-total-amount').textContent = `€${parseFloat(cartData.total).toFixed(2)}`;
        
        closeCart();
        document.getElementById('checkout-modal').style.display = 'flex';
    }
    
    // Close checkout modal
    function closeCheckout() {
        document.getElementById('checkout-modal').style.display = 'none';
        document.getElementById('checkout-form').reset();
    }
    
    // Handle checkout form submission
    async function handleCheckout(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        
        try {
            const response = await fetch(`${API_BASE}/checkout`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                closeCheckout();
                showSuccessModal(result.order_number);
                loadCart();
            } else {
                showNotification('❌ ' + result.message, 'error');
            }
        } catch (error) {
            console.error('Error during checkout:', error);
            showNotification('❌ Failed to place order', 'error');
        }
    }
    
    // Show success modal
    function showSuccessModal(orderNumber) {
        document.getElementById('order-number').textContent = orderNumber;
        document.getElementById('success-modal').style.display = 'flex';
    }
    
    // Close success modal
    function closeSuccess() {
        document.getElementById('success-modal').style.display = 'none';
    }
    
    // Show notification
    function showNotification(message, type = 'info') {
        // Simple alert for now - can be enhanced with toast notifications
        const styles = {
            success: '✓',
            error: '❌',
            info: 'ℹ️'
        };
        
        console.log(`${styles[type]} ${message}`);
        
        // You can replace this with a proper toast notification library
        const notification = document.createElement('div');
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            z-index: 9999;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // Expose functions globally for onclick handlers
    window.cartUpdateQty = updateQuantity;
    window.cartRemove = removeFromCart;
    window.cartAddToCart = addToCart;
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCart);
    } else {
        initCart();
    }
})();