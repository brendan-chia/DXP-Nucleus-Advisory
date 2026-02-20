/**
 * Nucleus DXP — Event Tracking
 * =============================
 * Tracks user interactions and sends events to GA4.
 * Edit this file to add/modify tracked events.
 *
 * Events fired:
 *   - view_feature   (click on a feature card)
 *   - view_service   (click on a service tag)
 *   - download_brochure (click on download button)
 *   - generate_lead  (form submit — handled in form-handler.php)
 */

document.addEventListener('DOMContentLoaded', function () {

    // Track Feature Card Clicks (What We Do + Value Prop)
    // Supports both old (.feature-card) and new (.what-item, .value-card) classes
    const featureSelectors = '.feature-card, .what-item, .value-card';
    document.querySelectorAll(featureSelectors).forEach(function (card) {
        card.addEventListener('click', function () {
            // Try to find title in h3 or h4
            let titleEl = this.querySelector('h3') || this.querySelector('h4');
            var featureName = titleEl ? titleEl.innerText : 'Unknown Feature';

            console.log('✅ Feature Clicked:', featureName);
            if (typeof gtag === 'function') {
                gtag('event', 'view_feature', { 'feature_name': featureName });
            }
        });
    });

    // Track Service Tag Clicks
    // Supports both old (.service-tag) and new (.stag) classes
    const serviceSelectors = '.service-tag, .stag';
    document.querySelectorAll(serviceSelectors).forEach(function (tag) {
        tag.addEventListener('click', function () {
            var serviceName = this.innerText;
            console.log('✅ Service Viewed:', serviceName);
            if (typeof gtag === 'function') {
                gtag('event', 'view_service', { 'service_name': serviceName });
            }
        });
    });

    // Track Download Brochure Clicks
    // We added the specific class .download-brochure-btn back to the link
    document.querySelectorAll('.download-brochure-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            // Prevent default if it's a # link (for demo purposes)
            if (this.getAttribute('href') === '#') {
                e.preventDefault();
            }

            console.log('✅ Brochure Download Clicked');
            if (typeof gtag === 'function') {
                gtag('event', 'download_brochure');
            }
        });
    });

    console.log('✅ Nucleus DXP Tracking Active (v2.1)');
});
