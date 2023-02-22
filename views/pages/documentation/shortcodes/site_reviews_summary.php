<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="shortcode-site_reviews_summary">
            <span class="title">Display the reviews summary</span>
            <span class="badge code">[site_reviews_summary]</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="shortcode-site_reviews_summary" class="inside">
        <h3>This shortcode displays a summary of your reviews.</h3>
        <div class="glsr-notice-inline components-notice is-info">
            <p class="components-notice__content">Each example below demonstrates a different shortcode option. If you need to use multiple options, simply combine the options together (separated with a space) in the same shortcode.</p>
        </div>
        <?php
            $options = [
                'assigned_posts' => trailingslashit(__DIR__).'site_reviews_summary/assigned_posts.php',
                'assigned_terms' => trailingslashit(__DIR__).'site_reviews_summary/assigned_terms.php',
                'assigned_users' => trailingslashit(__DIR__).'site_reviews_summary/assigned_users.php',
                'class' => trailingslashit(__DIR__).'site_reviews_summary/class.php',
                'filters' => trailingslashit(__DIR__).'site_reviews_summary/filters.php',
                'hide' => trailingslashit(__DIR__).'site_reviews_summary/hide.php',
                'id' => trailingslashit(__DIR__).'site_reviews_summary/id.php',
                'labels' => trailingslashit(__DIR__).'site_reviews_summary/labels.php',
                'rating' => trailingslashit(__DIR__).'site_reviews_summary/rating.php',
                'rating_field' => trailingslashit(__DIR__).'site_reviews_summary/rating_field.php',
                'reviews_id' => trailingslashit(__DIR__).'site_reviews_summary/reviews_id.php',
                'schema' => trailingslashit(__DIR__).'site_reviews_summary/schema.php',
                'terms' => trailingslashit(__DIR__).'site_reviews_summary/terms.php',
                'text' => trailingslashit(__DIR__).'site_reviews_summary/text.php',
                'theme' => trailingslashit(__DIR__).'site_reviews_summary/theme.php',
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
