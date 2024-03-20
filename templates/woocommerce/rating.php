<?php defined('WPINC') || exit; ?>

<div class="woocommerce-product-rating <?php echo esc_attr($style); ?>">
    <?php echo glsr_star_rating($ratings->average, $ratings->reviews, ['theme' => $theme]); ?>
    <?php if ($product->get_reviews_allowed()) { ?>
        <a href="#reviews" class="woocommerce-review-link" rel="nofollow">(<?php echo sprintf(_n('%s customer review', '%s customer reviews', $ratings->reviews, 'woocommerce'), '<span class="count">'.esc_html($ratings->reviews).'</span>'); ?>)</a>
    <?php } ?>
</div>
