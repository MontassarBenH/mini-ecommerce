# TEST CASES – REVIEW & RATING SYSTEM
**System:** Custom Review Plugin  
**Pages:** Product Detail Page (/product/slug)  
**DB:** product_reviews table  
**Last updated:** 2025-XX-XX  

---

## ✔ RC-001 — Review section loads correctly

### Steps
1. Open any product detail page.
2. Scroll to the reviews section.

### Expected
- “Customer Reviews” heading visible.
- Average rating displayed correctly.
- Count of reviews displayed.
- Star icons styled properly (no broken layout).
- “Write a Review” form visible below (if not hidden).

---

## ✔ RC-002 — Submit a valid review

### Test Data:
- Name: `John`
- Rating: 5
- Title: "Great!"
- Comment: "Highly recommend!"

### Steps
1. Fill all fields.
2. Click Submit.

### Expected
- Redirect to the same product page with `?review_submitted=1`.
- “Thank you for your review!” success alert should appear.
- New review shown in the review list.
- Stars displayed properly (★★★★★).

---

## ✔ RC-003 — Submit review without rating (validation)

### Steps
1. Leave star rating empty.
2. Fill name + comment.
3. Submit.

### Expected
- Error message: **“Please select a rating between 1 and 5.”**
- Review NOT saved to database.
- No redirect.

---

## ✔ RC-004 — Submit review without comment (validation)

### Steps
1. Select any rating.
2. Leave comment empty.
3. Submit.

### Expected
- Error message: “Please write a short comment.”
- Review NOT saved.

---

## ✔ RC-005 — UI star selection works

### Steps
1. Hover stars from 1 to 5.
2. Select between 1 and 5.

### Expected
- Empty stars turn gold only up to the hovered/selected star.
- Final selected stars stay highlighted when the mouse leaves.

---

## ✔ RC-006 — Reviews are stored in DB correctly

### Query
```sql
SELECT * FROM product_reviews WHERE product_id = X ORDER BY id DESC LIMIT 1;

Expected

rating = 1–5

author_name stored

title stored (nullable)

comment stored

product_id matches

✔ RC-007 — Review list displays correctly
Steps

Submit 2–3 reviews.

Reload page.

Expected

For each review:

Avatar placeholder shows the first letter of author name.

Name shown correctly.

Date formatted YYYY-MM-DD.

Title visible (if provided).

Comment shows correctly.

Stars appear on one line with correct spacing.

✔ RC-008 — Handle very long comments
Steps

Enter a 500–1000 character review.

Submit.

Expected

Review displays wrapped but does not break layout.

No overflow issues.

No page stretch or scroll issues.

✔ RC-009 — XSS protection
Payload
<script>alert("XSS")</script>

Steps

Enter payload in review comment or name.

Submit.

Expected

HTML tags escaped: <script> becomes &lt;script&gt;

No popup appears.

Page remains safe.

✔ RC-010 — Review average rating updates correctly
Steps

Submit two reviews: rating 5 and rating 3.

Reload page.

Expected

Average rating = (5 + 3)/2 = 4.0

UI displays “4.0/5”

Stars above price updated via plugin hook.

✔ RC-011 — Review count updates
Steps

Note the current review count.

Add a new review.

Expected

Review count increases by +1.

Shown correctly in:

Product page star badge

“Customer Reviews” header

✔ RC-012 — Empty review list UI
Steps

Open a product with zero reviews.

Expected

Instead of review list, a message appears:

“No reviews yet. Be the first to review!”

Form still visible.

✔ RC-013 — Multiple reviews sorted by newest first
Steps

Add 3 reviews.

Reload page.

Expected

Most recent review appears at the top.

Correct ORDER BY id DESC or created_at DESC.

✔ RC-014 — Review submission redirect (PRG pattern)
Steps

Submit a valid review.

Refresh page (F5).

Expected

Review does NOT resubmit a second time.

No duplicate in DB.

✔ RC-015 — RTL / special characters support
Test Input

Name: “علي”

Comment: “منتج ممتاز”

Expected

UTF-8 characters display properly.

No encoding issues.

✔ RC-016 — Plugin CSS loads correctly
Steps

Inspect elements.

Expected

ReviewStars.css is included via head_css hook.

No overlapping with product CSS.

✔ RC-017 — Review plugin does not break layout on mobile
Steps

Resize window / use mobile emulation.

Expected

Form fields align properly.

Stars do not overlap.

Review card width = container width.


---

# ✅ **FERTIG: Die Review-Test-Case-Datei ist vollständig**