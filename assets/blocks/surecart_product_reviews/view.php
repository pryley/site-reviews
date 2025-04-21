<?php

// get product from initial state.
$product = sc_get_product();

// make sure we have a product.
if (empty($product->id)) {
    return '';
}
$theme = glsr_get_option('integrations.surecart.style');

?>
<div <?php echo wp_kses_data(get_block_wrapper_attributes()); ?> data-style="<?php echo esc_attr($theme); ?>">
    <?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
