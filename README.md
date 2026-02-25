# 🧬 DXP Nucleus Advisory — WordPress Plugin

A modular WordPress plugin for **Nucleus Advisory's DXP Platform**. Provides AI-powered consulting landing pages, self-discovery assessment products (with Shopify integration), lead capture, analytics tracking, and an admin dashboard.

> ⚠️ **Important:** After making any changes, you must **re-zip and re-upload the plugin** via WP Admin → Plugins → Add New → Upload Plugin to see the updated output on the live site.

---

## 🌐 Live Pages

| Page | URL | Shortcode |
|------|-----|-----------|
| Testing Lab (Landing) | [nucleusadvisory.co/testing-lab](https://nucleusadvisory.co/testing-lab/) | `[nucleus_testing_page]` |
| Assessments (Products) | [nucleusadvisory.co/assessments](https://nucleusadvisory.co/assessments/) | `[nucleus_products_landing]` |
| Single Product | Per product permalink | `[nucleus_single_product]` |

---

## ✨ Current Features

### 🏠 Testing Lab — Landing Page
- **Hero Section** — Gradient background with floating animated orbs, mouse parallax, contact form (Contact Form 7), trust indicators
- **Partners & Expertise** — Dark navy section with horizontal icon→text cards and glowing accent lines on hover
- **Empowering Transformation** — Light section with numbered vertical feature cards (01–04) and hover animations
- **Services at a Glance** — Tag cloud of all consulting services with gradient hover effects
- **The Power of AI Banner** — Full-width dark banner with floating star particles and People Data Platform tagline
- **Expertise Glimpse** — Animated orbital visual with spinning rings and bouncing icons
- **CTA Section** — Pulsing glow call-to-action with "Contact Us" and "View Product →" buttons (links to /assessments/)
- **Scroll Reveal** — IntersectionObserver-based animations across all sections

### 📦 Product Manager (Custom Post Type)
- **Custom Post Type** — `nucleus_product` with full WordPress admin support
- **Product Meta Fields** — Subtitle, Price, Hero Summary, Shopify Buy Button Code
- **Auto Oxygen Setup** — Automatically assigns the Header Footer template (ID: 36) and sets Shortcode content when a product is created
- **Shopify Integration** — Paste Shopify Buy Button embed code; renders "Add to Cart" on the product page
- **Terms Checkbox** — Buyers must agree to Privacy, Delivery, and Refund policies before purchasing

### 🛍️ Products Landing Page
- **Dark Navy Hero** — Same gradient + radial blue/purple glows + floating star particles as the AI banner
- **Spotlight Carousel** — Auto-rotating product cards with image, title, subtitle, summary, price, and "View Assessment →" CTA
- **Carousel Controls** — Dot pagination, prev/next arrows, auto-play with progress bar
- **How It Works** — 3-step numbered cards with hover lift animations and gradient bottom accent lines
- **Included in Every Package** — 6-item grid showing what each assessment includes
- **CTA Section** — Dark navy with dot-pattern background
- **Disclaimers** — Expandable legal sections (educational purpose, data collection, turnaround time, refund policy, agreement)

### 📄 Single Product Page
- **Dark Navy Hero** — Same gradient + floating particles, product image left, info right
- **Shopify Buy Button** — Embedded checkout with terms agreement checkbox
- **What's Included Section** — Auto-split layout (JS-powered) that creates side-by-side columns from WordPress content
- **Styled Lists** — Three list types auto-detected via JS: ✓ checkmark grid, numbered framework cards, accent-border impact cards
- **Scroll Reveal** — Staggered list animations on scroll

### 📊 Analytics & Tracking
- **Google Tag Manager** — Container injection (GTM-NKL3T3HW)
- **Google Analytics 4** — Direct gtag.js integration (G-V6CKR789PG)
- **Custom Events** — `view_feature`, `view_service`, `generate_lead`, product views

### 🗄️ Lead Capture & Admin Dashboard
- **Lead Form** — Contact Form 7 integration on the testing page
- **Database Table** — `wp_nucleus_leads_testing` for lead storage
- **Admin Dashboard** — WP Admin leads viewer with submission data
- **REST API** — Custom endpoints for programmatic access

---

## 📁 Plugin Structure

```
nucleus-dxp/
├── nucleus-dxp.php                  ← Main loader, shortcodes, asset enqueueing
├── includes/
│   ├── product-manager.php          ← Product CPT, meta boxes, product shortcodes
│   ├── form-handler.php             ← Lead form shortcode + AJAX handler
│   ├── analytics.php                ← GTM + GA4 script injection
│   ├── admin-dashboard.php          ← WP Admin leads viewer + reports
│   └── rest-api.php                 ← REST API endpoints
├── templates/
│   ├── testing-page.php             ← Testing Lab landing page
│   ├── products-landing.php         ← Assessments listing page (carousel)
│   └── single-product.php           ← Individual product detail page
└── assets/
    ├── css/
    │   ├── testing-page.css         ← Testing Lab styles + animations
    │   ├── products-landing.css     ← Products listing styles
    │   └── single-product.css       ← Single product styles
    └── js/
        └── tracking.js              ← GA4 custom event tracking
```

### File Responsibilities

| File | Purpose |
|------|---------|
| `nucleus-dxp.php` | Loads all modules, registers `[nucleus_testing_page]` shortcode, enqueues CSS/JS |
| `includes/product-manager.php` | Registers `nucleus_product` CPT, meta boxes, `[nucleus_products_landing]` and `[nucleus_single_product]` shortcodes |
| `includes/form-handler.php` | Lead form HTML, validation, AJAX save to database |
| `includes/analytics.php` | GTM/GA4 IDs and script injection |
| `includes/admin-dashboard.php` | WP Admin leads table, submission viewer, sales reports |
| `includes/rest-api.php` | Custom REST API endpoints for data access |
| `templates/testing-page.php` | Landing page content — hero, partners, features, services, AI banner, glimpse, CTA |
| `templates/products-landing.php` | Product carousel, how-it-works, packages, disclaimers |
| `templates/single-product.php` | Product hero, buy button, auto-split content layout |
| `assets/css/testing-page.css` | Full design system: tokens, sections, animations, responsive breakpoints |
| `assets/css/products-landing.css` | Product listing styles, carousel, dark hero with particles |
| `assets/css/single-product.css` | Product detail styles, list card types, particles |
| `assets/js/tracking.js` | GA4 click event tracking for features, services, downloads |

---

## 🚀 Deployment

### First-Time Setup
1. Download/clone this repository
2. Zip the entire `nucleus-dxp/` folder
3. WP Admin → Plugins → Add New → Upload Plugin → Install & Activate
4. Create WordPress pages and assign shortcodes via Oxygen Builder

### Updating After Changes
1. Make your code changes in this repository
2. **Re-zip** the `nucleus-dxp/` folder
3. WP Admin → Plugins → Deactivate & Delete the old version
4. WP Admin → Plugins → Add New → Upload Plugin → Install & Activate
5. Hard refresh the page (`Ctrl + Shift + R`) to clear cached CSS

> 💡 **Tip:** CSS versions are bumped in the PHP files (`nucleus-dxp.php` and `product-manager.php`) to force cache busting. If styles aren't updating, check that the version number was incremented.

---

## 🎨 Design System

All three pages share a consistent design language:

| Token | Value | Usage |
|-------|-------|-------|
| Navy | `#0a1628` | Dark backgrounds, hero sections |
| Navy Light | `#1a2d4a` | Gradient endpoints |
| Blue | `#2563eb` | Primary accent, buttons, links |
| Purple | `#7c3aed` | Gradient endpoints, secondary accent |
| Sky | `#e0f2fe` | Section tags, light accents |
| Light BG | `#f8fafc` | Alternating section backgrounds |
| Font | Inter (Google Fonts) | All text |
| Border Radius | `14px` (cards), `10px` (buttons) | Consistent rounding |

### Shared Visual Elements
- **Dark Navy Gradient Hero** — `linear-gradient(160deg, #0a1628, #1a2d4a, #0f2044)` with radial blue/purple glows
- **Floating Star Particles** — 6 animated dots with staggered float animations
- **Blue→Purple Gradient** — Used for accent lines, text gradients, and hover effects
- **Hover Animations** — `translateY(-6px)` lifts with shadow + gradient bottom lines

---

## 📊 Analytics Configuration

### IDs (defined in `includes/analytics.php`)
```php
define('NUCLEUS_GA4_ID', 'G-V6CKR789PG');
define('NUCLEUS_GTM_ID', 'GTM-NKL3T3HW');
```

### Tracked Events

| Event Name | Trigger | Type |
|-----------|---------|------|
| `page_view` | Page load | Auto (GA4) |
| `scroll` | User scrolls | Auto (GA4) |
| `user_engagement` | Active on page | Auto (GA4) |
| `form_start` | Clicks form field | Auto (GA4) |
| `view_feature` | Clicks feature card | Custom |
| `view_service` | Clicks service tag | Custom |
| `generate_lead` | Submits lead form | Custom |

---

## 🗄️ Database

Table: `wp_nucleus_leads_testing` (created on plugin activation)

| Field | Type | Description |
|-------|------|-------------|
| `id` | mediumint | Auto-increment primary key |
| `name` | tinytext | Full name |
| `email` | varchar(100) | Work email |
| `company` | varchar(100) | Company name |
| `phone` | varchar(20) | Phone number |
| `submitted_at` | datetime | Submission timestamp |

---

## 🔧 Troubleshooting

| Issue | Solution |
|-------|---------|
| Page returns 404 | WP Admin → Settings → Permalinks → Save |
| Styles not updating | Re-upload the plugin and hard refresh (`Ctrl+Shift+R`) |
| Hero hidden by header | Increase top padding in the hero CSS (currently `160px` for products) |
| Events not in GA4 | Check console for `✅ Tracking Active`; verify Measurement ID |
| Form not submitting | Check console for errors; verify `admin-ajax.php` accessible |
| Product page blank | Ensure Oxygen template ID 36 exists; check `ct_other_template` meta |
| Shopify button not showing | Check product meta `_nucleus_product_shopify_button` has embed code |

---

## 📋 Key Design Decisions

1. **Shortcode over Oxygen Code Block** — Oxygen had a bug reverting long code after save
2. **Direct gtag() over GTM-only** — Custom events bypass GTM trigger configuration
3. **Plugin over theme** — Survives theme updates, can be toggled independently
4. **Auto Oxygen template** — Products auto-assign template ID 36 on creation, zero manual setup
5. **JS-powered layout** — Single product content auto-splits into 2-column layout via DOM manipulation
6. **Consistent design system** — All pages share the same color palette, animations, and visual treatments

---

## 🛠️ Tech Stack

- **CMS:** WordPress
- **Page Builder:** Oxygen Builder
- **E-Commerce:** Shopify Buy Button (embedded)
- **Forms:** Contact Form 7
- **Analytics:** Google Tag Manager + Google Analytics 4
- **Visualization:** Looker Studio
- **Languages:** PHP, HTML, CSS, JavaScript
