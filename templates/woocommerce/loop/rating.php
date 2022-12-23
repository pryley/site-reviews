<?php defined('WPINC') || die; ?>

<div class="<?= $style; ?> glsrw-loop-rating" style="display: inline-block; margin: 0 auto;">
    <?= glsr_star_rating($ratings->average, $ratings->reviews, ['theme' => $theme]); ?>
</div>
