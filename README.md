📘 README.md – Mini E-Commerce Playground

Ein vollständiges Full-Stack E-Commerce-System mit Plugins, SEO, Tracking, Reviews, Warenkorb & automatisierten Tests.

🧩 Mini E-Commerce Playground

Ein voll funktionsfähiges E-Commerce-System, entwickelt als Portfolio-Projekt mit Fokus auf:

Software-Architektur (Plugin-System)

E-Commerce-Features (Cart, Reviews, Orders)

SEO-Optimierung (JSON-LD, Sitemap, Robots, OG, Twitter Cards)

SEA & Tracking (Google Tag Manager + Events)

QA / Testing (Smoke Tests, Regression Tests, Automated PHPUnit Tests, Performance Tests)

🚀 Features
🛒 Shopping Cart Plugin

Sidebar-Cart (slide-in)

Add / Update / Remove items

Persistenz per cart_items Tabelle (session-based)

Checkout modal + Order creation

Frontend dynamisch mit JavaScript

Tested with PHPUnit

⭐ ReviewStars Plugin

Custom Review-System für jedes Produkt

1–5 Sterne mit modernem UI

Nutzer kann:

Review schreiben

Sterne bewerten

Name & Titel optional

Anzeige:

Durchschnittsbewertung

Anzahl Reviews

Einzelne Bewertungen

🔍 SEO Optimizer Plugin

Automatisierte Suchmaschinenoptimierung:

Meta Tags

Title

Description

Canonical

Robots

Open Graph

Twitter Cards

Structured Data (JSON-LD)

Organization

Website Search

Breadcrumbs

Product

AggregateRating

Sitemap Generator (sitemap.php)

Robots.txt

Critical CSS + Preload + Preconnect

📦 Produkte & Kategorien (JSON API)

/api/products

/api/products/{slug}

/api/categories

Filter, Suche, Slugs, Preise, Stock, Bilder

🎯 SEA / Tracking Module

Google Tag Manager Integration

E-Commerce Tracking Events:

view_item

add_to_cart

purchase

Campaign Landing Page Templates

Tracking-ready Product Detail Pages

🧪 Testing & QA Module

Komplette Teststrategie mit Dokumentation:

✔ Manual Test Documentation

Test Plan

Smoke Test Suite

Regression Tests

Test Cases

Review Tests (tc-reviews.md)

Bug report templates

✔ Automated Testing (PHPUnit)

Unit Tests

API Tests

Plugin Tests

Regression Test für Review-Bug

Performance Tests (mit Time Assertions)

Smoke Tests als Gruppe

Coverage Reports (wenn Xdebug aktiviert)

HTML Testdox Reports

✔ Performance Testing

API Response Time Checks (<150–200ms)

Rendering Performance (Server)

Lighthouse Frontend-Audit (Google Chrome)

🏗 Projektstruktur
mini-ecommerce/
│
│
│── │ ──── controllers/
│   │   ├── ProductController.php
│   │   ├── CategoryController.php
│   │   └── CartController.php
│   └── index.php
│
├── plugins/
│   ├── ShoppingCart/
│   │   ├── ShoppingCart.php
│   │   ├── plugin.json
│   │   ├── assets/
│   │   │   ├── cart.css
│   │   │   └── cart.js
│   │   └── views/cart-modal.php
│   │
│   ├── ReviewStars/
│   │   ├── ReviewStars.php
│   │   ├── plugin.json
│   │   ├── assets/stars.css
│   │   └── views/stars.php
│   │
│   └── SEOOptimizer/
│       ├── SEOOptimizer.php
│       ├── plugin.json
│       └── assets/css/critical.css
│
├── seo/
│   ├── ImageOptimizer.php
│   └── seo-audit.php
│
├── tests/
│   ├── ApiProductsTest.php
│   ├── ApiCategoriesTest.php
│   ├── CartTest.php
│   ├── ReviewStarsTest.php
│   ├── PerformanceApiTest.php
│   ├── PerformanceCartTest.php
│   ├── SmokeTest.php
│   └── RegressionReviewBugTest.php
│
├── views/
│   ├── home.php
│   ├── product-detail.php
│   ├── products.php
│   └── category.php
│
├── phpunit.xml
├── sitemap.php
├── robots.txt
└── README.md

🔧 Installation
1. Projekt lokal klonen
git clone 
cd mini-ecommerce

2. Composer installieren (falls nicht vorhanden)

https://getcomposer.org/download/

3. Dependencies installieren

⚠ Wichtig: Dein PHP liegt in XAMPP

& "C:\xampp\php\php.exe" composer.phar install

4. PHPUnit testen
& "C:\xampp\php\php.exe" vendor\bin\phpunit

🤖 Automated Testing
✔ Alle Tests ausführen
& "C:\xampp\php\php.exe" vendor\bin\phpunit

✔ Nur Smoke Test Suite
php vendor/bin/phpunit --group smoke

✔ Regression Tests (z. B. Review-Bug)
php vendor/bin/phpunit --group regression

✔ Performance Tests
php vendor/bin/phpunit --group performance

✔ HTML Test Report generieren
php vendor/bin/phpunit --testdox-html build/test-report.html

📝 Manuelles Testing
🔥 Smoke Test Suite
Test	Status	Beschreibung
Startseite lädt	✅	CSS/JS ok
Produktseite lädt	✅	Slug funktioniert
Produkt kann in Warenkorb	✅	Cart Modal
Checkout öffnet	✅	Modal sichtbar
Reviews werden angezeigt	✅	Plugin lädt
🐞 Regression Tests
Beispiel: Doppelter Review-Bug

Der Fehler wurde reproduziert, gefixt und automatisiert getestet.

Test-Datei:

tests/RegressionReviewBugTest.php

📋 Manuelle Testfälle

Alle dokumentiert in:

/docs/tests/tc-reviews.md
/docs/tests/test-plan.md
/docs/tests/testcases/


Beispiele:

TC-REV-001 – Review mit 5 Sternen absenden

TC-REV-004 – Durchschnitt korrekt berechnen

TC-CART-003 – Menge aktualisieren

TC-PROD-006 – Preisformat testen

📊 Performance Testing
✔ PHPUnit Performance Tests

Messen Response-Zeiten der API-Kontroller:

getProducts() < 200ms

getProduct(slug) < 150ms

Cart->addToCart() < 150ms

usw.

✔ Lighthouse Audit

Frontend Performance gemessen über Chrome DevTools

Berichte gespeichert unter:

/docs/performance/lighthouse/

🔍 SEO Testing

JSON-LD Validierung via Rich Result Tool

Meta Tag Tests

Canonical Check

Sitemap/Robots Test

SEO Audit Report unter /seo/seo-audit.php

📈 SEA / Tracking

Google Tag Manager injected in <head>

DataLayer Events:

view_item

add_to_cart

begin_checkout

purchase

Konfigurierbar über:

assets/js/gtm.js
views/product-detail.php
views/home.php

🏁 Fazit

Dieses Projekt zeigt Full-Stack Entwicklung, Plugin-Architektur, SEO/SEA-Optimierung, Testing-Professionalität und CI-taugliche QA-Struktur.

Perfekt für professionelle Bewerbungen als:

Full-Stack Developer

Web Developer

QA/Testing Engineer

Automation Engineer

E-Commerce Developer