<?php defined('ABSPATH') || exit; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="shortcode-site_reviews_form">
            <span class="title">Display the review form</span>
            <span class="badge code">[site_reviews_form]</span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="shortcode-site_reviews_form" class="inside">
        <h3>This shortcode displays the review form.</h3>
        <div class="glsr-notice-inline components-notice is-info">
            <p class="components-notice__content">Each example below demonstrates a different shortcode option. If you need to use multiple options, simply combine the options together (separated with a space) in the same shortcode.</p>
        </div>
        <?php
            $options = [
                'assigned_posts' => trailingslashit(__DIR__).'site_reviews_form/assigned_posts.php',
                'assigned_terms' => trailingslashit(__DIR__).'site_reviews_form/assigned_terms.php',
                'assigned_users' => trailingslashit(__DIR__).'site_reviews_form/assigned_users.php',
                'class' => trailingslashit(__DIR__).'site_reviews_form/class.php',
                'description' => trailingslashit(__DIR__).'site_reviews_form/description.php',
                'form' => trailingslashit(__DIR__).'site_reviews_form/form.php',
                'hide' => trailingslashit(__DIR__).'site_reviews_form/hide.php',
                'id' => trailingslashit(__DIR__).'site_reviews_form/id.php',
                'reviews_id' => trailingslashit(__DIR__).'site_reviews_form/reviews_id.php',
                'theme' => trailingslashit(__DIR__).'site_reviews_form/theme.php',
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
