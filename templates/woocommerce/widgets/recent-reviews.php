<?php defined('WPINC') || exit; ?>

<ul class="product_list_widget">
    <?php foreach ($reviews as $review) { ?>
        <li>
            <?php do_action('woocommerce_widget_product_review_item_start', $args); ?>
            <a href="<?php echo esc_url($review->product()->get_permalink()); ?>#review-<?php echo $review->ID; ?>">
                <?php echo $review->product()->get_image(); ?>
                <span class="product-title"><?php echo $review->product()->get_name(); ?></span>
            </a>
            <div class="<?php echo esc_attr($style); ?> glsrw-loop-rating">
                <?php echo glsr_star_rating($review->rating, 0, ['theme' => $theme]); ?>
            </div>
            <span class="reviewer">
                <?php echo sprintf(esc_html__('by %s', 'woocommerce'), $review->author); ?>
            </span>
            <?php do_action('woocommerce_widget_product_review_item_end', $args); ?>
        </li>
    <?php } ?>
</ul>
