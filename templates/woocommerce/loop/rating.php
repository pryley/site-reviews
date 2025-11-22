<?php defined('ABSPATH') || exit;
/**
 * @version 1.0.0
 */
?>
<div class="<?php echo esc_attr($class); ?> glsrw-loop-rating" style="display: inline-block; margin: 0 auto;">
    <?php echo glsr_star_rating($ratings->average, $ratings->reviews, ['theme' => $theme]); ?>
</div>
