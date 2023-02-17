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
        <h3>This shortcode displays your most recently submitted reviews.</h3>
        <div class="glsr-notice-inline components-notice is-info">
            <p class="components-notice__content">Each example below demonstrates a different shortcode option. If you need to use multiple options, simply combine the options together (separated with a space) in the same shortcode.</p>
        </div>
        <?php
            $options = [
                trailingslashit(__DIR__).'site_review/class.php',
                trailingslashit(__DIR__).'site_review/fallback.php',
                trailingslashit(__DIR__).'site_review/hide.php',
                trailingslashit(__DIR__).'site_review/id.php',
                trailingslashit(__DIR__).'site_review/post_id.php',
            ];
            $filename = pathinfo(__FILE__, PATHINFO_FILENAME);
            $options = glsr()->filterArrayUnique('documentation/shortcodes/'.$filename, $options);
            foreach ($options as $option) {
                include $option;
            }
        ?>
    </div>
</div>
