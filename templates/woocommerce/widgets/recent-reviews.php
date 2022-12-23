<?php defined('WPINC') || die; ?>

<ul class="product_list_widget">
    <?php foreach ($reviews as $review): ?>
        <li>
            <?php do_action('woocommerce_widget_product_review_item_start', $args); ?>
            <a href="<?= esc_url($review->product()->get_permalink()); ?>#review-<?= $review->ID; ?>">
                <?= $review->product()->get_image(); ?>
                <span class="product-title"><?= $review->product()->get_name(); ?></span>
            </a>
            <div class="<?= $style; ?> glsrw-loop-rating">
                <?= glsr_star_rating($review->rating, 0, ['theme' => $theme]); ?>
            </div>
            <span class="reviewer">
                <?= sprintf(esc_html__('by %s', 'woocommerce'), $review->author); ?>
            </span>
            <?php do_action('woocommerce_widget_product_review_item_end', $args); ?>
        </li>
    <?php endforeach; ?>
</ul>
