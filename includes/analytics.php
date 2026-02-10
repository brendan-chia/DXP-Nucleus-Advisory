<?php
/**
 * Analytics: GTM + GA4 Script Injection
 * Only loads on the 'testing-lab' page
 */

if (!defined('ABSPATH'))
    exit;

// GA4 Measurement ID (change this if you switch GA4 properties)
define('NUCLEUS_GA4_ID', 'G-V6CKR789PG');
define('NUCLEUS_GTM_ID', 'GTM-NKL3T3HW');

// Inject GTM + GA4 into <head>
function nucleus_core_add_gtm_head()
{
    if (is_page('testing-lab')) {
        ?>
        <!-- Google Tag Manager -->
        <script>(function (w, d, s, l, i) {
                w[l] = w[l] || []; w[l].push({
                    'gtm.start':
                        new Date().getTime(), event: 'gtm.js'
                }); var f = d.getElementsByTagName(s)[0],
                    j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : ''; j.async = true; j.src =
                        'https://www.googletagmanager.com/gtm.js?id=' + i + dl; f.parentNode.insertBefore(j, f);
            })(window, document, 'script', 'dataLayer', '<?php echo NUCLEUS_GTM_ID; ?>');</script>
        <!-- End Google Tag Manager -->

        <!-- GA4 Direct (for custom events) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo NUCLEUS_GA4_ID; ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', '<?php echo NUCLEUS_GA4_ID; ?>', { send_page_view: false });
        </script>
        <?php
    }
}
add_action('wp_head', 'nucleus_core_add_gtm_head');

// Inject GTM noscript into <body>
function nucleus_core_add_gtm_body()
{
    if (is_page('testing-lab')) {
        ?>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo NUCLEUS_GTM_ID; ?>" height="0" width="0"
                style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        <?php
    }
}
add_action('wp_body_open', 'nucleus_core_add_gtm_body');
