<?php defined('WPINC') || exit; ?>

<div id="site-reviews" class="panel woocommerce_options_panel hidden">
    <div class="options_group">
        <div class="inline notice notice-alt notice-info woocommerce-message" style="display: flex; margin: 15px 10px 10px;">
            <p style="margin: .5em 0; padding: 2px; font-size: 13px; line-height: 1.5;">
                <?php
                    printf(_x('Here you can override the Site Reviews shortcodes for this product. To change the shortcodes for all products, %sClick here%s.', '<a>Click here</a> (admin-text)', 'site-reviews'),
                        sprintf('<a href="%s">', glsr_admin_url('settings', 'integrations', 'woocommerce')), '</a>'
                    );
                ?>
            </p>
        </div>
        <?php
            woocommerce_wp_text_input([
                'desc_tip' => true,
                'description' => _x('Enter a custom [site_reviews_summary] shortcode to override the one saved in the settings.', 'admin-text', 'site-reviews'),
                'id'  => 'site_reviews_summary',
                'label' => _x('Summary Shortcode', 'admin-text', 'site-reviews'),
            ]);
            woocommerce_wp_text_input([
                'desc_tip' => true,
                'description' => _x('Enter a custom [site_reviews] shortcode to override the one saved in the settings.', 'admin-text', 'site-reviews'),
                'id'  => 'site_reviews',
                'label' => _x('Reviews Shortcode', 'admin-text', 'site-reviews'),
            ]);
            woocommerce_wp_text_input([
                'desc_tip' => true,
                'description' => _x('Enter a custom [site_reviews_form] shortcode to override the one saved in the settings.', 'admin-text', 'site-reviews'),
                'id'  => 'site_reviews_form',
                'label' => _x('Form Shortcode', 'admin-text', 'site-reviews'),
            ]);
        ?>
    </div>
    <div class="options_group reviews">
        <?php
            woocommerce_wp_checkbox([
                'cbvalue' => 'open',
                'id' => 'comment_status',
                'label' => _x('Enable reviews', 'admin-text', 'site-reviews'),
                'value' => $product->get_reviews_allowed('edit') ? 'open' : 'closed',
            ]);
        ?>
    </div>
</div>
