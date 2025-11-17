# TEST CASES – CHECKOUT MODAL

## TC-CH001 – Modal opens
Expected:
- Overlay displayed
- Page scroll locked

## TC-CH002 – Required fields validated
Expected:
- Name required
- Email required
- Address required

## TC-CH003 – Successful checkout
Expected:
- Order saved in DB
- Cart cleared
- trackEvent("purchase") executed
