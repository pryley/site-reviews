<?php defined('WPINC') || exit; ?>

<div class="<?php echo esc_attr($style); ?> glsrw-loop-rating" style="display: inline-block; margin: 0 auto;">
    <?php echo glsr_star_rating($ratings->average, $ratings->reviews, ['theme' => $theme]); ?>
</div>
