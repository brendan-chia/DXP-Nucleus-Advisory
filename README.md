# 🧪 DXP Nucleus Advisory — Testing Platform

A modular WordPress plugin for **Nucleus Advisory's DXP Testing Environment**. Provides lead capture, analytics tracking, and templated landing pages.

**Live URL:** [nucleusadvisory.co/testing-lab](https://nucleusadvisory.co/testing-lab/) (Requires login to Nucleus Advisory WP Admin to view)

---

## 🚀 Quick Start

1. Upload the `nucleus-dxp/` folder to `wp-content/plugins/`
2. Activate **"DXP Testing Version"** in WP Admin → Plugins
3. Create a WordPress page with slug `testing-lab`
4. Open in Oxygen Builder → Add Shortcode → `[nucleus_testing_page]`
5. Save and visit the page

---

## 📁 Plugin Structure

```
nucleus-dxp/
├── nucleus-dxp.php              ← Main loader
├── includes/
│   ├── form-handler.php         ← Lead form shortcode + AJAX
│   ├── analytics.php            ← GTM + GA4 scripts
│   └── admin-dashboard.php      ← WP Admin leads viewer
├── templates/
│   └── testing-page.php         ← Landing page HTML
└── assets/
    ├── css/
    │   └── testing-page.css     ← Page styles
    └── js/
        └── tracking.js          ← GA4 event tracking
```

### File Responsibilities

| File | Purpose | Who Edits |
|------|---------|-----------|
| `nucleus-dxp.php` | Loads modules, registers shortcodes, enqueues assets | Developer |
| `includes/form-handler.php` | Form HTML, validation, AJAX save to DB | Developer |
| `includes/analytics.php` | GTM/GA4 IDs and script injection | Developer |
| `includes/admin-dashboard.php` | WP Admin leads table | Developer |
| `templates/testing-page.php` | Page content and layout | **Anyone** |
| `assets/css/testing-page.css` | Visual styling | **Anyone** |
| `assets/js/tracking.js` | Click event tracking | Developer |

---

## 📊 Analytics

### Configuration
IDs are defined in `includes/analytics.php`:
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
| `download_brochure` | Clicks download button | Custom |
| `generate_lead` | Submits lead form | Custom |

### Data Flow
```
User visits page → GTM + gtag.js loads → User interacts →
Events fire to GA4 → GA4 processes (24h) → Looker Studio displays
```

---

## 🗄️ Database

| Field | Type | Description |
|-------|------|-------------|
| `id` | mediumint | Auto-increment primary key |
| `name` | tinytext | Full name |
| `email` | varchar(100) | Work email |
| `company` | varchar(100) | Company name |
| `phone` | varchar(20) | Phone number |
| `submitted_at` | datetime | Submission timestamp |

Table: `wp_nucleus_leads_testing` (created on plugin activation)

---

## ➕ How to Create a New Page

### 1. Create the Template
Create `templates/pricing-page.php`:
```html
<section class="pricing-hero">
    <div class="nucleus-container">
        <h1>Your Page Title</h1>
        <p>Your content here.</p>
    </div>
</section>
```

### 2. Create the CSS
Create `assets/css/pricing-page.css`:
```css
.pricing-hero {
    padding: 180px 0 100px;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
}
```

### 3. Register in `nucleus-dxp.php`
Add the shortcode:
```php
function nucleus_pricing_page_shortcode() {
    ob_start();
    include NUCLEUS_DXP_PATH . 'templates/pricing-page.php';
    return ob_get_clean();
}
add_shortcode('nucleus_pricing_page', 'nucleus_pricing_page_shortcode');
```

Update `nucleus_dxp_enqueue_assets()`:
```php
if (is_page('pricing')) {
    wp_enqueue_style('nucleus-pricing', NUCLEUS_DXP_URL . 'assets/css/pricing-page.css', array(), '1.0');
    wp_enqueue_script('nucleus-tracking', NUCLEUS_DXP_URL . 'assets/js/tracking.js', array(), '2.0', true);
}
```

### 4. Create WordPress Page
1. WP Admin → Pages → Add New
2. Title: "Pricing", Slug: `pricing`
3. Oxygen Builder → Shortcode element → `[nucleus_pricing_page]`
4. Save

### 5. Add Tracking (Optional)
In `assets/js/tracking.js`:
```javascript
document.querySelectorAll('.pricing-cta-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        gtag('event', 'pricing_click', { 'plan': this.dataset.plan });
    });
});
```

---

## 🔧 Troubleshooting

| Issue | Solution |
|-------|---------|
| Page returns 404 | WP Admin → Settings → Permalinks → Save |
| Styles not loading | Check `is_page('slug')` matches your WordPress page slug |
| Events not in GA4 | Check console for `✅ Tracking Active`; verify Measurement ID |
| Form not submitting | Check console for errors; verify `admin-ajax.php` accessible |
| Leads table empty | Submit a test form; table created on first activation |

---

## 📋 Key Design Decisions

1. **Shortcode over Oxygen Code Block** — Oxygen had a bug reverting long code after save
2. **Direct gtag() over GTM-only** — Custom events bypass GTM trigger configuration
3. **Modular structure** — 799-line monolith split into 7 focused files
4. **Plugin over theme** — Survives theme updates, can be toggled independently

---

## 🛠️ Tech Stack

- **CMS:** WordPress
- **Page Builder:** Oxygen Builder
- **Analytics:** Google Tag Manager + Google Analytics 4
- **Visualization:** Looker Studio
- **Language:** PHP, HTML, CSS, JavaScript
