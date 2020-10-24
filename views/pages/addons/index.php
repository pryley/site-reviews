<?php defined('ABSPATH') || die; ?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?= esc_html(get_admin_page_title()); ?></h1>
    <?= $notices; ?>
    <p><?= _x('I hope that you will find these add-ons useful! Each purchase goes towards the continued support and development of Site Reviews.', 'admin-text', 'site-reviews'); ?></p>
    <div class="glsr-addons">
    <?php
        $template->render('partials/addons/addon', [
            'context' => [
                'description' => _x('Allow your website visitors to sort, filter by rating, and search reviews.', 'admin-text', 'site-reviews'),
                'link' => 'https://niftyplugins.com/plugins/site-reviews-filters/',
                'slug' => 'filters',
                'title' => 'Filters',
            ],
            'plugin' => 'site-reviews-filters/site-reviews-filters.php',
        ]);
        $template->render('partials/addons/addon', [
            'context' => [
                'description' => _x('Allow your website visitors to add images with captions to their reviews.', 'admin-text', 'site-reviews'),
                'link' => 'https://niftyplugins.com/plugins/site-reviews-images/',
                'slug' => 'images',
                'title' => 'Images',
            ],
            'plugin' => 'site-reviews-images/site-reviews-images.php',
        ]);
        $template->render('partials/addons/addon', [
            'context' => [
                'description' => _x('Integrate with the Trustalyze Confidence System and post reviews to the blockchain.', 'admin-text', 'site-reviews'),
                'link' => 'https://niftyplugins.com/plugins/trustalyze/',
                'slug' => 'trustalyze',
                'title' => 'Trustalyze',
            ],
            'plugin' => 'site-reviews-trustalyze/site-reviews-trustalyze.php',
        ]);
    ?>
    </div>
</div>
