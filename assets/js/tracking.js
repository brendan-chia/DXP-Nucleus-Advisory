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

    // Track Feature Card Clicks
    document.querySelectorAll('.feature-card').forEach(function (card) {
        card.addEventListener('click', function () {
            var featureName = this.querySelector('h3') ? this.querySelector('h3').innerText : 'Unknown';
            console.log('✅ Feature Clicked:', featureName);
            if (typeof gtag === 'function') {
                gtag('event', 'view_feature', { 'feature_name': featureName });
            }
        });
    });

    // Track Service Tag Clicks
    document.querySelectorAll('.service-tag').forEach(function (tag) {
        tag.addEventListener('click', function () {
            var serviceName = this.innerText;
            console.log('✅ Service Viewed:', serviceName);
            if (typeof gtag === 'function') {
                gtag('event', 'view_service', { 'service_name': serviceName });
            }
        });
    });

    // Track Download Brochure Clicks
    document.querySelectorAll('.download-brochure-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            console.log('✅ Brochure Download Clicked');
            if (typeof gtag === 'function') {
                gtag('event', 'download_brochure');
            }
        });
    });

    console.log('✅ Nucleus DXP Tracking Active');
});
