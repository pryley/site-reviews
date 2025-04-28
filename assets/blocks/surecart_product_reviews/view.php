<?php

// get product from initial state.
$product = sc_get_product();

// make sure we have a product.
if (empty($product->id)) {
    return '';
}

?>
<div <?php echo wp_kses_data(get_block_wrapper_attributes()); ?>>
    <?php echo $content; ?>
</div>
