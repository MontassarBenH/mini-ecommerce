<div id="cart-overlay" class="cart-overlay" style="display: none;"></div>

<div id="cart-sidebar" class="cart-sidebar">
    <div class="cart-header">
        <h2>Shopping Cart</h2>
        <button id="close-cart" class="close-cart">&times;</button>
    </div>
    
    <div id="cart-items" class="cart-items">
        <div class="cart-empty">
            <p>🛒 Your cart is empty</p>
        </div>
    </div>
    
    <div class="cart-footer">
        <div class="cart-total">
            <span>Total:</span>
            <span id="cart-total-amount">€0.00</span>
        </div>
        <button id="checkout-btn" class="btn-checkout" disabled>
            Proceed to Checkout
        </button>
        <button id="clear-cart-btn" class="btn-clear">Clear Cart</button>
    </div>
</div>

<!-- Checkout Modal -->
<div id="checkout-modal" class="checkout-modal" style="display: none;">
    <div class="checkout-content">
        <div class="checkout-header">
            <h2>Checkout</h2>
            <button id="close-checkout" class="close-checkout">&times;</button>
        </div>
        
        <form id="checkout-form">
            <div class="form-group">
                <label for="customer-name">Full Name *</label>
                <input type="text" id="customer-name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="customer-email">Email *</label>
                <input type="email" id="customer-email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="customer-phone">Phone</label>
                <input type="tel" id="customer-phone" name="phone">
            </div>
            
            <div class="form-group">
                <label for="customer-address">Shipping Address *</label>
                <textarea id="customer-address" name="address" rows="3" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="payment-method">Payment Method</label>
                <select id="payment-method" name="payment_method">
                    <option value="credit_card">Credit Card</option>
                    <option value="paypal">PayPal</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>
            </div>
            
            <div class="checkout-summary">
                <h3>Order Summary</h3>
                <div id="checkout-items"></div>
                <div class="checkout-total">
                    <strong>Total:</strong>
                    <strong id="checkout-total-amount">€0.00</strong>
                </div>
            </div>
            
            <button type="submit" class="btn-place-order">Place Order</button>
        </form>
    </div>
</div>

<!-- Success Modal -->
<div id="success-modal" class="success-modal" style="display: none;">
    <div class="success-content">
        <div class="success-icon">✓</div>
        <h2>Order Placed Successfully!</h2>
        <p>Your order number is: <strong id="order-number"></strong></p>
        <p>We'll send a confirmation email to your address.</p>
        <button id="close-success" class="btn-primary">Continue Shopping</button>
    </div>
</div>