<?php defined('WPINC') || die; ?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?= esc_html(get_admin_page_title()); ?></h1>
    <?= $notices; ?>
    <p><?= __('Add-ons extend the functionality of Site Reviews.', 'site-reviews'); ?></p>
    <div class="glsr-addons wp-clearfix">
    <?php
        $template->render('partials/addons/addon', [
            'context' => [
                'description' => __('Allow your visitors to submit multiple images with their reviews.', 'site-reviews'),
                'link' => 'https://niftyplugins.com/addons/site-reviews-images/',
                'slug' => 'images',
                'title' => 'Images',
            ],
            'plugin' => 'site-reviews-images/site-reviews-images.php',
        ]);
        $template->render('partials/addons/addon', [
            'context' => [
                'description' => __('Sync your Tripadvisor reviews to your website and manage them with Site Reviews.', 'site-reviews'),
                'link' => 'https://niftyplugins.com/addons/site-reviews-tripadvisor/',
                'slug' => 'tripadvisor',
                'title' => 'Tripadvisor Reviews',
            ],
            'plugin' => 'site-reviews-tripadvisor/site-reviews-tripadvisor.php',
        ]);
    ?>
    </div>
</div>
