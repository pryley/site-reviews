<?php defined('WPINC') || exit; ?>

<div id="reviews" class="woocommerce-Reviews">
    <div id="comments">
        <h2 class="woocommerce-Reviews-title">
        <?php
            if ($ratings->reviews) {
                $title = sprintf(esc_html(_n('%1$s review for %2$s', '%1$s reviews for %2$s', $ratings->reviews, 'woocommerce')), esc_html($ratings->reviews), '<span>'.get_the_title().'</span>');
                echo apply_filters('woocommerce_reviews_title', $title, $ratings->reviews, $product); // WPCS: XSS ok.
            } else {
                esc_html_e('Reviews', 'woocommerce');
            }
        ?>
        </h2>
        <?php echo $summary; ?>
        <p>&nbsp;</p>
        <?php echo $reviews; ?>
    </div>
    <?php if ($verified) { ?>
        <div id="review_form_wrapper">
            <div id="review_form">
                <div id="respond" class="comment-respond">
                    <?php echo $form; ?>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <p class="woocommerce-verification-required">
            <?php esc_html_e('Only logged in customers who have purchased this product may leave a review.', 'woocommerce'); ?>
        </p>
    <?php } ?>
</div>
