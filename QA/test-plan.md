# 📘 TEST PLAN – Mini E-Commerce Playground  
**Version:** 1.0  
**Date:** 2025-02-XX  
**Author:** QA Engineering / (Ben)

---

## 1. PROJECT OVERVIEW
Mini E-Commerce Playground is a modular, plugin-based shop system created for portfolio demonstration.  
This Test Plan defines the testing strategy, scope, processes, environments, and responsibilities.

---

## 2. TEST OBJECTIVES
- Validate functional correctness (Shopping Cart, Products, Reviews, SEO, Tracking).
- Ensure UI/UX consistency on all pages.
- Validate plugin interoperability.
- Verify SEO readiness (Module 3).
- Validate SEA tracking (Module 4).
- Confirm security & data validation measures.
- Ensure no regressions after plugin updates.

---

## 3. TEST SCOPE

### IN SCOPE
- Product listing & detail pages  
- Shopping cart (add/update/remove/clear)
- Checkout modal
- Review submission & display
- Ratings aggregation
- Category pages
- SEO metadata & structured data
- GTM + tracking events (non-production)
- Sitemap & robots
- Page performance (Lighthouse baseline)

### OUT OF SCOPE
- Real payment processing  
- Real email sending  
- Production GTM container  
- Multi-language support

---

## 4. TEST TYPES

### ✔ Functional Testing
- End-to-end flows  
- Plugin interaction  
- Form validation  

### ✔ UI / UX Testing
- Consistent styling  
- Mobile responsiveness  
- Modal behaviour  

### ✔ Regression Testing
Based on `/qa/regression-checklist.md`.

### ✔ Performance Testing
- Lighthouse audit  
- Chrome Performance Profile  
- Load simulation (optional)

### ✔ SEO Testing
- Check JSON-LD  
- Check `meta` tags  
- Check sitemap  
- Check robots.txt  

### ✔ Tracking / SEA Testing
- Validate dataLayer events  
- Validate GTM preview mode  

### ✔ Database Testing
- Ensure no duplicates  
- Ensure database constraints work  
- Ensure cascading deletes are correct  

---

## 5. TEST ENVIRONMENT

### Browser Matrix
| Browser | Version | Status      |
|---------|---------|-------------|
| Chrome  | Latest  | Mandatory   |  
| Firefox | Latest  | Recommended |
| Safari  | Latest  | Optional    |
| Edge    | Latest  | Recommended |

### Devices
- Desktop (1920px)
- Tablet (768px)
- Mobile (375px)

### Server
- Localhost (XAMPP)
- PHP 8.1+
- MySQL 5.7 / 8.0

---

## 6. TEST DATA

### Product Test Data
| ID | Name         | Stock | Price  |
|----|--------------|-------|--------|
| 1  | Wooden Tower | 10    | 299.00 |
| 2  | Swing Set    | 5     | 149.00 |

### Cart Test Data
- Empty cart  
- Cart with 1 product  
- Cart with multiple products  
- Product with stock = 0  

### Review Test Data
- Valid rating + comment  
- Missing rating  
- Missing comment  
- XSS attempt `<script>`

---

## 7. EXIT CRITERIA
Testing is completed when:
- 100% test cases executed  
- 0 critical bugs  
- ≤ 2 medium bugs  
- All SEO tests pass  
- All tracking tests pass  
- Lighthouse score ≥ 85  

---

## 8. APPROVALS
| Role          | Name  |
|---------------|-------|
| QA Engineer   | Ben   |
| Developer     | Ben   |
| Project Owner | Ben   | 

