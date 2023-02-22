<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="shortcode-site_reviews">
            <span class="title">Display the reviews</span>
            <span class="badge code">[site_reviews]</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="shortcode-site_reviews" class="inside">
        <h3>This shortcode displays your most recently submitted reviews.</h3>
        <div class="glsr-notice-inline components-notice is-info">
            <p class="components-notice__content">Each example below demonstrates a different shortcode option. If you need to use multiple options, simply combine the options together (separated with a space) in the same shortcode.</p>
        </div>
        <?php
            $options = [
                'assigned_posts' => trailingslashit(__DIR__).'site_reviews/assigned_posts.php',
                'assigned_terms' => trailingslashit(__DIR__).'site_reviews/assigned_terms.php',
                'assigned_users' => trailingslashit(__DIR__).'site_reviews/assigned_users.php',
                'class' => trailingslashit(__DIR__).'site_reviews/class.php',
                'display' => trailingslashit(__DIR__).'site_reviews/display.php',
                'fallback' => trailingslashit(__DIR__).'site_reviews/fallback.php',
                'filters' => trailingslashit(__DIR__).'site_reviews/filters.php',
                'form' => trailingslashit(__DIR__).'site_reviews/form.php',
                'hide' => trailingslashit(__DIR__).'site_reviews/hide.php',
                'id' => trailingslashit(__DIR__).'site_reviews/id.php',
                'offset' => trailingslashit(__DIR__).'site_reviews/offset.php',
                'pagination' => trailingslashit(__DIR__).'site_reviews/pagination.php',
                'rating' => trailingslashit(__DIR__).'site_reviews/rating.php',
                'rating_field' => trailingslashit(__DIR__).'site_reviews/rating_field.php',
                'schema' => trailingslashit(__DIR__).'site_reviews/schema.php',
                'terms' => trailingslashit(__DIR__).'site_reviews/terms.php',
                'theme' => trailingslashit(__DIR__).'site_reviews/theme.php',
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
