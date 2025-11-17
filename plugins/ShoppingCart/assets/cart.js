
document.addEventListener('DOMContentLoaded', function () {
    const CART_API_URL = window.location.origin + '/mini-ecommerce/api/cart';

    const cartBtn        = document.getElementById('cart-btn');
    const cartOverlay    = document.getElementById('cart-overlay');
    const cartSidebar    = document.getElementById('cart-sidebar');
    const closeCartBtn   = document.getElementById('close-cart');
    const cartItemsBox   = document.getElementById('cart-items');
    const cartTotalText  = document.getElementById('cart-total-amount');
    const cartCountSpan  = document.getElementById('cart-count');
    const checkoutBtn    = document.getElementById('checkout-btn');
    const clearCartBtn   = document.getElementById('clear-cart-btn');

    const checkoutModal  = document.getElementById('checkout-modal');
    const closeCheckout  = document.getElementById('close-checkout');
    const checkoutForm   = document.getElementById('checkout-form');
    const checkoutItems  = document.getElementById('checkout-items');
    const checkoutTotal  = document.getElementById('checkout-total-amount');

    const successModal   = document.getElementById('success-modal');
    const closeSuccess   = document.getElementById('close-success');
    const orderNumberBox = document.getElementById('order-number');

    function openCart() {
        if (!cartSidebar || !cartOverlay) return;
        cartOverlay.style.display = 'block';
        cartOverlay.classList.add('active');
        cartSidebar.classList.add('active');
        loadCart();
    }

    function closeCart() {
        if (!cartSidebar || !cartOverlay) return;
        cartOverlay.classList.remove('active');
        cartSidebar.classList.remove('active');
        setTimeout(() => {
            cartOverlay.style.display = 'none';
        }, 250);
    }

    async function loadCart() {
        try {
            const res  = await fetch(CART_API_URL, { method: 'GET' });
            const data = await res.json();

            if (!data.success) {
                console.error('❌ Failed to load cart', data.message);
                return;
            }

            renderCartItems(data.items || []);
            updateCartSummary(data.total || 0, data.count || 0);
        } catch (err) {
            console.error('❌ Error loading cart', err);
        }
    }

    function renderCartItems(items) {
        if (!cartItemsBox) return;

        if (!items.length) {
            cartItemsBox.innerHTML = `
                <div class="cart-empty">
                    <p>🛒 Your cart is empty</p>
                </div>
            `;
            checkoutBtn && (checkoutBtn.disabled = true);
            return;
        }

        const html = items.map(item => {
            const price   = parseFloat(item.price || item.product_price || 0);
            const subtotal = parseFloat(item.subtotal || (price * item.quantity));
            const name    = item.name || item.product_name || 'Product';
            const image   = item.image_url || '';
            const slug    = item.slug ? `/product/${item.slug}` : '#';

            return `
                <div class="cart-item" data-product-id="${item.product_id}">
                    <a href="${slug}">
                        <img src="${image}" alt="${name}">
                    </a>
                    <div class="cart-item-details">
                        <div class="cart-item-name">${name}</div>
                        <div class="cart-item-price">
                            €${price.toFixed(2)} × 
                            <span class="cart-item-qty">${item.quantity}</span>
                            = <strong>€${subtotal.toFixed(2)}</strong>
                        </div>
                        <div class="cart-item-actions">
                            <button class="cart-qty-btn" data-action="dec">-</button>
                            <button class="cart-qty-btn" data-action="inc">+</button>
                            <button class="cart-remove" title="Remove">&times;</button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        cartItemsBox.innerHTML = html;
        checkoutBtn && (checkoutBtn.disabled = false);
    }

    function updateCartSummary(total, count) {
        if (cartTotalText) {
            cartTotalText.textContent = '€' + Number(total).toFixed(2);
        }
        if (cartCountSpan) {
            cartCountSpan.textContent = `(${count})`;
        }
        if (checkoutTotal) {
            checkoutTotal.textContent = '€' + Number(total).toFixed(2);
        }
    }

    async function addToCart(productId, quantity = 1) {
        try {
            const res = await fetch(CART_API_URL, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    action: 'add',
                    product_id: productId,
                    quantity: quantity
                })
            });

            const data = await res.json();
            if (!data.success) {
                console.error('❌ ❌', data.message);
                alert(data.message || 'Failed to add to cart');
                return;
            }

            updateCartSummary(data.total ?? 0, data.cart_count ?? 0);
            // Cart neu laden & öffnen
            openCart();
        } catch (err) {
            console.error('❌ Error addToCart', err);
            alert('Failed to add to cart (network error).');
        }
    }

    async function updateItemQuantity(productId, newQuantity) {
        try {
            const res = await fetch(CART_API_URL, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    action: 'update',
                    product_id: productId,
                    quantity: newQuantity
                })
            });

            const data = await res.json();
            if (!data.success) {
                console.error('❌ update failed', data.message);
                return;
            }

            loadCart();
        } catch (err) {
            console.error('❌ Error updateItemQuantity', err);
        }
    }

    async function removeItem(productId) {
        try {
            const res = await fetch(CART_API_URL, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    action: 'remove',
                    product_id: productId
                })
            });

            const data = await res.json();
            if (!data.success) {
                console.error('❌ remove failed', data.message);
                return;
            }

            loadCart();
        } catch (err) {
            console.error('❌ Error removeItem', err);
        }
    }

    async function clearCart() {
        try {
            const res = await fetch(CART_API_URL, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ action: 'clear' })
            });

            const data = await res.json();
            if (!data.success) {
                console.error('❌ clear failed', data.message);
                return;
            }

            loadCart();
        } catch (err) {
            console.error('❌ Error clearCart', err);
        }
    }

    // --- Checkout / Order ---

    function openCheckout() {
        if (!checkoutModal) return;
        if (cartItemsBox && checkoutItems) {
            checkoutItems.innerHTML = cartItemsBox.innerHTML;
        }
        checkoutModal.style.display = 'flex';
    }

    function closeCheckoutModal() {
        if (!checkoutModal) return;
        checkoutModal.style.display = 'none';
    }

    async function submitCheckoutForm(e) {
        e.preventDefault();
        if (!checkoutForm) return;

        const formData = new FormData(checkoutForm);
        const payload  = Object.fromEntries(formData.entries());
        payload.action = 'checkout';

        try {
            const res  = await fetch(CART_API_URL, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            if (!data.success) {
                alert(data.message || 'Failed to place order.');
                return;
            }

            // Erfolg → Success-Modal
            closeCheckoutModal();
            if (successModal) {
                if (orderNumberBox && data.order_number) {
                    orderNumberBox.textContent = data.order_number;
                }
                successModal.style.display = 'flex';
            }

            // Cart leeren / neu laden
            loadCart();
            checkoutForm.reset();

        } catch (err) {
            console.error('❌ checkout error', err);
            alert('Failed to place order (network error).');
        }
    }

    function closeSuccessModal() {
        if (!successModal) return;
        successModal.style.display = 'none';
        closeCart();
    }

    // --- Event Listener ---

    // Cart Button in Header
    if (cartBtn) {
        cartBtn.addEventListener('click', function (e) {
            e.preventDefault();
            openCart();
        });
    }

    if (closeCartBtn) {
        closeCartBtn.addEventListener('click', function () {
            closeCart();
        });
    }

    if (cartOverlay) {
        cartOverlay.addEventListener('click', function () {
            closeCart();
        });
    }

    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', function () {
            if (confirm('Clear all items from your cart?')) {
                clearCart();
            }
        });
    }

    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function () {
            openCheckout();
        });
    }

    if (closeCheckout) {
        closeCheckout.addEventListener('click', function () {
            closeCheckoutModal();
        });
    }

    if (checkoutForm) {
        checkoutForm.addEventListener('submit', submitCheckoutForm);
    }

    if (closeSuccess) {
        closeSuccess.addEventListener('click', function () {
            closeSuccessModal();
        });
    }

    if (cartItemsBox) {
        cartItemsBox.addEventListener('click', function (e) {
            const itemEl = e.target.closest('.cart-item');
            if (!itemEl) return;

            const productId = parseInt(itemEl.dataset.productId, 10);

            if (e.target.matches('.cart-qty-btn')) {
                const action = e.target.dataset.action;
                const qtySpan = itemEl.querySelector('.cart-item-qty');
                let current   = parseInt(qtySpan.textContent, 10) || 1;

                if (action === 'inc') current++;
                if (action === 'dec') current = Math.max(1, current - 1);

                updateItemQuantity(productId, current);
            }

            if (e.target.matches('.cart-remove')) {
                removeItem(productId);
            }
        });
    }

    // Globale Listener für "Add to Cart"-Buttons
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.add-to-cart-btn');
        if (!btn) return;

        const productId = parseInt(btn.dataset.productId, 10);
        if (!productId) {
            console.error('No product_id on add-to-cart-btn');
            return;
        }

        addToCart(productId, 1);
    });

    // Initial den Cart-Zähler laden
    loadCart();
});
