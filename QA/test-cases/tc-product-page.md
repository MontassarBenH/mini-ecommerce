# TEST CASES – PRODUCT PAGE

## TC-P001 – Load product correctly
**Steps**
1. Open `/product/<slug>`
2. Observe product information

**Expected**
- Product image loads
- SKU is visible
- Correct price is displayed
- Stock badge shown correctly

---

## TC-P002 – Breadcrumbs render correctly
**Expected**
- Home > Products > Category > Product

---

## TC-P003 – Add to Cart button visible
**Expected**
- If stock > 0 → button enabled  
- If stock = 0 → button disabled

---

## TC-P004 – SEO metadata loaded
**Check in DevTools**
- `<title>` correct  
- Description correct  
- OG tags present  
- Canonical present  
- JSON-LD Product schema present

---
