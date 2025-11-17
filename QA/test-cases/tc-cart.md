# TEST CASES – SHOPPING CART

## TC-C001 – Add item to cart
Steps:
1. Open Product
2. Click Add to Cart

Expected:
- Sidebar opens
- Console shows trackEvent("add_to_cart")
- API returns success

---

## TC-C002 – Update quantity
Expected:
- Quantity updates in DB
- New total displayed

---

## TC-C003 – Remove item
Expected:
- Item disappears from sidebar
