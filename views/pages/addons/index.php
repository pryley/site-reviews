<?php defined('ABSPATH') || die; ?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?= esc_html(get_admin_page_title()); ?></h1>
    <?= $notices; ?>
    <div class="glsr-addons">
    <?php
        $template->render('partials/addons/addon', [
            'context' => [
                'description' => _x('All of the add-ons! Grab the special introductory discount while it still lasts...', 'admin-text', 'site-reviews').' 👀',
                'link' => 'https://niftyplugins.com/plugins/site-reviews-premium/',
                'link_text' => _x('Discover Premium', 'admin-text', 'site-reviews'),
                'slug' => 'premium',
                'title' => 'Site Reviews Premium',
            ],
            'plugin' => '',
        ]);
        $template->render('partials/addons/addon', [
            'context' => [
                'description' => _x('Allow your website visitors to sort, filter by rating, and search reviews.', 'admin-text', 'site-reviews'),
                'link' => 'https://niftyplugins.com/plugins/site-reviews-filters/',
                'link_text' => _x('View Add-on', 'admin-text', 'site-reviews'),
                'slug' => 'filters',
                'title' => 'Review Filters',
            ],
            'plugin' => 'site-reviews-filters/site-reviews-filters.php',
        ]);
        $template->render('partials/addons/addon', [
            'context' => [
                'description' => _x('Create unique review forms with custom fields and review templates.', 'admin-text', 'site-reviews'),
                'link' => 'https://niftyplugins.com/plugins/site-reviews-forms/',
                'link_text' => _x('View Add-on', 'admin-text', 'site-reviews'),
                'slug' => 'forms',
                'title' => 'Review Forms',
            ],
            'plugin' => 'site-reviews-forms/site-reviews-forms.php',
        ]);
        $template->render('partials/addons/addon', [
            'context' => [
                'description' => _x('Allow your website visitors to add images with captions to their reviews.', 'admin-text', 'site-reviews'),
                'link' => 'https://niftyplugins.com/plugins/site-reviews-images/',
                'link_text' => _x('View Add-on', 'admin-text', 'site-reviews'),
                'slug' => 'images',
                'title' => 'Review Images',
            ],
            'plugin' => 'site-reviews-images/site-reviews-images.php',
        ]);
        $template->render('partials/addons/addon', [
            'context' => [
                'description' => _x('Create custom review designs, display reviews in a grid, change the rating images, and more. Early access available to Premium members only.', 'admin-text', 'site-reviews'),
                'link' => 'https://niftyplugins.com/account/',
                'link_text' => _x('Request Early Access', 'admin-text', 'site-reviews'),
                'slug' => 'themes',
                'title' => 'Review Themes',
            ],
            'plugin' => 'site-reviews-themes/site-reviews-themes.php',
        ]);
        $template->render('partials/addons/addon', [
            'context' => [
                'description' => _x('Integrate with the Trustalyze Confidence System and post reviews to the blockchain.', 'admin-text', 'site-reviews'),
                'link' => 'https://niftyplugins.com/plugins/trustalyze/',
                'link_text' => _x('View Add-on', 'admin-text', 'site-reviews'),
                'slug' => 'trustalyze',
                'title' => 'Trustalyze',
            ],
            'plugin' => 'site-reviews-trustalyze/site-reviews-trustalyze.php',
        ]);
        $template->render('partials/addons/addon', [
            'context' => [
                'description' => _x('Integrate Site Reviews with your Woocommerce products.', 'admin-text', 'site-reviews'),
                'link' => 'https://niftyplugins.com/plugins/woocommerce-reviews/',
                'link_text' => _x('View Add-on', 'admin-text', 'site-reviews'),
                'slug' => 'woocommerce',
                'title' => 'Woocommerce Reviews',
            ],
            'plugin' => 'site-reviews-woocommerce/site-reviews-woocommerce.php',
        ]);
    ?>
    </div>
</div>
