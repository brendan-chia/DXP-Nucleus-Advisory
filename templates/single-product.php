<!--
    Single Product Template — Minimalist Design
    Variables: $title, $subtitle, $price, $hero_summary, $thumbnail_url, $content
-->

<div class="nucleus-single-product-wrapper">

    <!-- Hero Section -->
    <div class="n-product-hero">
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

</div>