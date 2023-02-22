<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="shortcode-site_review">
            <span class="title">Display a single review</span>
            <span class="badge code">[site_review]</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="shortcode-site_review" class="inside">
        <h3>This shortcode displays a single review.</h3>
        <div class="glsr-notice-inline components-notice is-info">
            <p class="components-notice__content">Each example below demonstrates a different shortcode option. If you need to use multiple options, simply combine the options together (separated with a space) in the same shortcode.</p>
        </div>
        <?php
            $options = [
                'class' => trailingslashit(__DIR__).'site_review/class.php',
                'fallback' => trailingslashit(__DIR__).'site_review/fallback.php',
                'form' => trailingslashit(__DIR__).'site_review/form.php',
                'hide' => trailingslashit(__DIR__).'site_review/hide.php',
                'id' => trailingslashit(__DIR__).'site_review/id.php',
                'post_id' => trailingslashit(__DIR__).'site_review/post_id.php',
                'theme' => trailingslashit(__DIR__).'site_review/theme.php',
            ];
            $filename = pathinfo(__FILE__, PATHINFO_FILENAME);
            $options = glsr()->filterArray('documentation/shortcode/'.$filename, $options);
            ksort($options);
            foreach ($options as $option) {
                include $option;
            }
        ?>
    </div>
</div>
