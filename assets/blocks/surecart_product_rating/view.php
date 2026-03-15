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

$textTemplate = str_contains($attributes['text'], '{num}')
    ? $attributes['text']
    : '{num}';

$context = [
    'reviewCount' => $ratings->reviews,
    'textTemplate' => $textTemplate,
];

$blockClassAttr = '';
if (!empty($attributes['style_rating_color']) || !empty($attributes['style_rating_color_custom'])) {
    $blockClassAttr = 'has-rating-color';
}

$blockStyleAttr = '';
if (!empty($attributes['style_rating_color'])) {
    $blockStyleAttr = "--glsr-review-star-bg: var(--wp--preset--color--{$attributes['style_rating_color']});";
} elseif (!empty($attributes['style_rating_color_custom'])) {
    $blockStyleAttr = "--glsr-review-star-bg: {$attributes['style_rating_color_custom']};";
}

$blockAttributes = get_block_wrapper_attributes([
    'class' => $blockClassAttr,
    'style' => $blockStyleAttr,
]);

?>
<div <?php echo wp_kses_data($blockAttributes); ?>
    data-wp-context='<?php echo (wp_json_encode($context) ?: '{}'); ?>'
    data-wp-init="callbacks.init"
    data-wp-interactive="site-reviews/surecart-product-rating"
    data-wp-key="surecart-product-rating-<?php echo (int) $product->id; ?>"
>
    <?php echo glsr_star_rating($ratings->average, $ratings->reviews, compact('theme')); ?>
    <?php if ($attributes['has_text']) { ?>
        <?php if ($attributes['is_link']) { ?>
            <a href="<?php echo esc_attr($attributes['link_url']); ?>"
                data-wp-on--click="actions.scroll"
                data-wp-text="state.formattedText"
            ></a>
        <?php } else { ?>
            <span data-wp-text="state.formattedText"></span>
        <?php } ?>
    <?php } ?>
</div>
