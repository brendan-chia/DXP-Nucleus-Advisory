<!--
    Single Product Template — Minimalist Design
    Variables: $title, $subtitle, $price, $hero_summary, $shopify_button, $thumbnail_url, $content
-->

<div class="nucleus-single-product-wrapper">

    <!-- Hero Section -->
    <div class="n-product-hero">
        <div class="np-hero-particles">
            <span class="np-particle np-p1"></span>
            <span class="np-particle np-p2"></span>
            <span class="np-particle np-p3"></span>
            <span class="np-particle np-p4"></span>
            <span class="np-particle np-p5"></span>
            <span class="np-particle np-p6"></span>
        </div>
        <div class="n-product-hero-inner">

            <!-- Left: Product Image -->
            <div class="n-product-image-col">
                <?php if ($thumbnail_url): ?>
                    <img src="<?php echo esc_url($thumbnail_url); ?>" alt="<?php echo esc_attr($title); ?>"
                        class="n-product-image">
                <?php else: ?>
                    <div class="n-product-image-placeholder">No Image</div>
                <?php endif; ?>
            </div>

            <!-- Right: Product Info -->
            <div class="n-product-info-col">
                <span class="n-product-badge">Premium Assessment</span>
                <h1 class="n-product-title"><?php echo esc_html($title); ?></h1>
                <?php if ($subtitle): ?>
                    <p class="n-product-subtitle"><?php echo esc_html($subtitle); ?></p>
                <?php endif; ?>

                <?php if ($price): ?>
                    <div class="n-product-price"><?php echo esc_html($price); ?></div>
                <?php endif; ?>

                <?php if ($hero_summary): ?>
                    <div class="n-product-summary">
                        <?php echo nl2br(esc_html($hero_summary)); ?>
                    </div>
                <?php endif; ?>

                <?php if ($shopify_button): ?>
                    <div class="n-product-terms">
                        <label class="n-terms-checkbox">
                            <input type="checkbox" id="nucleus-terms-checkbox">
                            <span>I agree to the
                                <a href="/wp-content/uploads/2026/02/Nucleus_Advisory_Privacy_Policy.pdf"
                                    target="_blank">Privacy Policy</a>,
                                <a href="/wp-content/uploads/2026/02/Nucleus_Advisory_Delivery_Policy.pdf"
                                    target="_blank">Delivery Policy</a>
                                and
                                <a href="/wp-content/uploads/2026/02/Nucleus_Advisory_Refund_Policy.pdf"
                                    target="_blank">Refund Policy</a>.
                            </span>
                        </label>

                    </div>
                    <div class="n-product-buy-button" id="nucleus-buy-button-wrapper">
                        <?php echo $shopify_button; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- What's Included Section -->
    <?php if ($content && trim(strip_tags($content))): ?>
        <div class="n-product-details-section">
            <div class="n-product-details-inner">
                <h2 class="n-details-title">What's Included</h2>
                <div class="n-details-content">
                    <?php echo $content; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // --- Terms checkbox ---
            const checkbox = document.getElementById("nucleus-terms-checkbox");
            const buttonWrapper = document.getElementById("nucleus-buy-button-wrapper");

            if (checkbox && buttonWrapper) {
                buttonWrapper.style.opacity = "0.5";
                buttonWrapper.style.pointerEvents = "none";
                checkbox.addEventListener("change", function () {
                    if (this.checked) {
                        buttonWrapper.style.opacity = "1";
                        buttonWrapper.style.pointerEvents = "auto";
                    } else {
                        buttonWrapper.style.opacity = "0.5";
                        buttonWrapper.style.pointerEvents = "none";
                    }
                });
            }

            // --- Auto-Stunning Split Layout Builder ---
            // Automatically creates the side-by-side layout for the 2nd and 3rd sections
            const detailsContainer = document.querySelector('.n-details-content');
            if (detailsContainer) {
                const targetH3s = detailsContainer.querySelectorAll('h3');

                // Only wrap if we have at least 3 sections
                if (targetH3s.length >= 3) {
                    const h3_2 = targetH3s[1];
                    const h3_3 = targetH3s[2];

                    const splitWrapper = document.createElement('div');
                    splitWrapper.className = 'n-details-split-layout';

                    const colLeft = document.createElement('div');
                    colLeft.className = 'n-details-col n-details-col-left';

                    const colRight = document.createElement('div');
                    colRight.className = 'n-details-col n-details-col-right';

                    splitWrapper.appendChild(colLeft);
                    splitWrapper.appendChild(colRight);

                    h3_2.parentNode.insertBefore(splitWrapper, h3_2);

                    // Move everything from 2nd H3 to 3rd H3 into the left column
                    let curr = h3_2;
                    while (curr && curr !== h3_3) {
                        let next = curr.nextSibling;
                        colLeft.appendChild(curr);
                        curr = next;
                    }

                    // Move everything from 3rd H3 onwards into the right column
                    curr = h3_3;
                    while (curr) {
                        let next = curr.nextSibling;
                        colRight.appendChild(curr);
                        curr = next;
                    }
                }

                // --- Assign robust CSS classes to lists ---
                const colLeftUl = detailsContainer.querySelector('.n-details-col-left ul');
                if (colLeftUl) colLeftUl.classList.add('n-list-framework');

                const colRightUl = detailsContainer.querySelector('.n-details-col-right ul');
                if (colRightUl) colRightUl.classList.add('n-list-impact');

                // The first UL that isn't inside our new split columns gets the "receive" class
                const firstUl = detailsContainer.querySelector('ul:not(.n-list-framework):not(.n-list-impact)');
                if (firstUl) firstUl.classList.add('n-list-receive');
            }

            // --- Scroll-reveal for lists ---
            var lists = document.querySelectorAll('.n-details-content ul');
            if (lists.length) {
                var observer = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.15 });
                lists.forEach(function (el) { observer.observe(el); });
            }
        });
    </script>

</div>