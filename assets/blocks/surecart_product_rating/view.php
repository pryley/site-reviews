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

$textTemplate = sanitize_text_field($attributes['text']);
$textTemplate = str_contains($textTemplate, '{num}') ? $textTemplate : '{num}';

$context = [
    'reviewCount' => $ratings->reviews,
    'textTemplate' => $textTemplate,
];

$ratingColor = sanitize_key($attributes['style_rating_color'] ?? '');
$ratingColorCustom = (string) ($attributes['style_rating_color_custom'] ?? '');
// A ; would start a second declaration.
$ratingColorCustom = preg_match('/[;{}\\\\<>"\']/', $ratingColorCustom) ? '' : $ratingColorCustom;

$blockClassAttr = '';
if (!empty($ratingColor) || !empty($ratingColorCustom)) {
    $blockClassAttr = 'has-rating-color';
}

$blockStyleAttr = '';
if (!empty($ratingColor)) {
    // A theme preset variable is undefined once the theme that provided it is no
    // longer active. Without a fallback the declaration is invalid at computed-value
    // time and the stars become transparent.
    $fallback = $ratingColorCustom ?: 'currentColor';
    $blockStyleAttr = "--glsr-review-star-bg: var(--wp--preset--color--{$ratingColor}, {$fallback});";
} elseif (!empty($ratingColorCustom)) {
    $blockStyleAttr = "--glsr-review-star-bg: {$ratingColorCustom};";
}

$blockAttributes = get_block_wrapper_attributes([
    'class' => $blockClassAttr,
    'style' => $blockStyleAttr,
]);

?>
<div <?php echo wp_kses_data($blockAttributes); ?>
    <?php echo wp_interactivity_data_wp_context($context); ?>
    data-wp-init="callbacks.init"
    data-wp-interactive="site-reviews/surecart-product-rating"
    data-wp-key="surecart-product-rating-<?php echo (int) $product->id; ?>"
>
    <?php echo glsr_star_rating($ratings->average, $ratings->reviews, compact('theme')); ?>
    <?php if ($attributes['has_text']) { ?>
        <?php if ($attributes['is_link']) { ?>
            <a href="<?php echo esc_url($attributes['link_url']); ?>"
                data-wp-on--click="actions.scroll"
                data-wp-text="state.formattedText"
            ></a>
        <?php } else { ?>
            <span data-wp-text="state.formattedText"></span>
        <?php } ?>
    <?php } ?>
</div>
