<?php defined('ABSPATH') || exit; ?>

<div class="wrap">
    <hr class="wp-header-end" />
    <?= $notices; ?>
    <div class="glsr-premium-hero">
        <div class="glsr-premium-hero-image"></div>
        <div class="glsr-premium-hero-content">
            <h2><?= _x('Site Reviews Premium', 'admin-text', 'site-reviews'); ?></h2>
            <p>
                <?= sprintf(_x('Gain access to ALL of our free and paid add-ons with Site Reviews Premium, including access to future add-ons as they are released and <a href="%s" target="_blank">priority support</a>!', 'admin-text', 'site-reviews'),
                    'https://niftyplugins.com/account/support/'
                ); ?>
            </p>
            <a href="https://niftyplugins.com/plugins/site-reviews-premium/" class="button button-hero button-primary" target="_blank"><?= _x('Check it out!', 'admin-text', 'site-reviews'); ?></a>
        </div>
    </div>

    <div class="glsr-addons">
    <?php
        $template->render('partials/addons/addon', [
            'context' => [
                'description' => _x('Allow your website visitors to sort, filter by rating, and search reviews.', 'admin-text', 'site-reviews'),
                'link' => 'https://niftyplugins.com/plugins/site-reviews-filters/',
                'link_text' => _x('View Add-on', 'admin-text', 'site-reviews'),
                'slug' => 'filters',
                'title' => 'Review Filters',
            ],
            'id' => 'site-reviews-filters',
            'plugin' => 'site-reviews-filters/site-reviews-filters.php',
        ]);
        $template->render('partials/addons/addon', [
            'context' => [
                'description' => _x('Form builder with 22 different field types, each form has its own Review Template which can be customised as needed.', 'admin-text', 'site-reviews'),
                'link' => 'https://niftyplugins.com/plugins/site-reviews-forms/',
                'link_text' => _x('View Add-on', 'admin-text', 'site-reviews'),
                'slug' => 'forms',
                'title' => 'Review Forms',
            ],
            'id' => 'site-reviews-forms',
            'plugin' => 'site-reviews-forms/site-reviews-forms.php',
        ]);
        $template->render('partials/addons/addon', [
            'context' => [
                'description' => _x('Allow your website visitors to submit images (and optional captions) with their reviews.', 'admin-text', 'site-reviews'),
                'link' => 'https://niftyplugins.com/plugins/site-reviews-images/',
                'link_text' => _x('View Add-on', 'admin-text', 'site-reviews'),
                'slug' => 'images',
                'title' => 'Review Images',
            ],
            'id' => 'site-reviews-images',
            'plugin' => 'site-reviews-images/site-reviews-images.php',
        ]);
        $template->render('partials/addons/addon', [
            'context' => [
                'description' => _x('Create notification emails with custom conditions and schedule them to send after a review is submitted.', 'admin-text', 'site-reviews'),
                'link' => 'https://niftyplugins.com/account/downloads/',
                'link_text' => _x('Premium members only', 'admin-text', 'site-reviews'),
                'slug' => 'notifications',
                'title' => 'Review Notifications (beta)',
            ],
            'id' => 'site-reviews-notifications',
            'plugin' => 'site-reviews-notifications/site-reviews-notifications.php',
        ]);
        $template->render('partials/addons/addon', [
            'context' => [
                'description' => _x('Design reviews with a drag-and-drop builder, display reviews in a grid or carousel, choose custom rating images, and more!', 'admin-text', 'site-reviews'),
                'link' => 'https://niftyplugins.com/account/downloads/',
                'link_text' => _x('Premium members only', 'admin-text', 'site-reviews'),
                'slug' => 'themes',
                'title' => 'Review Themes (beta)',
            ],
            'id' => 'site-reviews-themes',
            'plugin' => 'site-reviews-themes/site-reviews-themes.php',
        ]);
        $template->render('partials/addons/addon', [
            'context' => [
                'description' => _x('Integrate Site Reviews with GamiPress and award your users for submitting and receiving reviews. Includes over 30 activity triggers!', 'admin-text', 'site-reviews'),
                'link' => 'https://niftyplugins.com/plugins/site-reviews-gamipress/',
                'link_text' => _x('View Add-on', 'admin-text', 'site-reviews'),
                'slug' => 'gamipress',
                'title' => 'GamiPress Reviews',
            ],
            'id' => 'site-reviews-gamipress',
            'plugin' => 'site-reviews-gamipress/site-reviews-gamipress.php',
        ]);
        $template->render('partials/addons/addon', [
            'context' => [
                'description' => _x('Integrate Site Reviews with your Woocommerce products.', 'admin-text', 'site-reviews'),
                'link' => 'https://niftyplugins.com/plugins/woocommerce-reviews/',
                'link_text' => _x('View Add-on', 'admin-text', 'site-reviews'),
                'slug' => 'woocommerce',
                'title' => 'Woocommerce Reviews',
            ],
            'id' => 'site-reviews-woocommerce',
            'plugin' => 'site-reviews-woocommerce/site-reviews-woocommerce.php',
        ]);
    ?>
    </div>
</div>
