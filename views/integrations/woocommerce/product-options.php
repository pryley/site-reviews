<?php defined('WPINC') || die; ?>

<div class="options_group reviews">
<?php
    woocommerce_wp_checkbox([
        'cbvalue' => 'open',
        'id' => 'comment_status',
        'label' => _x('Enable reviews', 'admin-text', 'site-reviews'),
        'value' => $product->get_reviews_allowed('edit') ? 'open' : 'closed',
    ]);
    do_action('woocommerce_product_options_reviews');
?>
</div>
