<?php

// get product from initial state.
$product = sc_get_product();

// make sure we have a product.
if (empty($product->id)) {
    return '';
}

$ratings = glsr_get_ratings([
    'assigned_posts' => $product->post->ID,
]);
$displayEmpty = glsr_get_option('integrations.surecart.display_empty', false, 'bool');
$theme = glsr_get_option('integrations.surecart.style');

if (!$displayEmpty && $ratings->average <= 0) {
    return '';
}

?>
<div <?php echo wp_kses_data(get_block_wrapper_attributes()); ?>
    data-wp-interactive="site-reviews/surecart-product-rating"
>
    <div>
        <?php echo glsr_star_rating($ratings->average, $ratings->reviews, compact('theme')); ?>
    </div>
    <?php if ($attributes['has_text']) { ?>
        <div>
            <?php if ($attributes['is_link']) { ?>
                <a href="<?php echo esc_attr($attributes['link_url']); ?>"
                    data-wp-on--click="actions.scroll"
                >
                    <?php echo str_replace('{num}', $ratings->reviews, $attributes['text']); ?>
                </a>
            <?php } else { ?>
                <span>
                    <?php echo str_replace('{num}', $ratings->reviews, $attributes['text']); ?>
                </span>
            <?php } ?>
        </div>
    <?php } ?>
</div>
