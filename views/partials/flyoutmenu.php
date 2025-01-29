<?php defined('ABSPATH') || exit; ?>

<div id="glsr-flyout">
    <div class="glsr-flyout-items">
        <?php foreach ($items as $index => $item) { ?>
            <a href="<?php echo esc_url($item['url']); ?>"
                class="glsr-flyout-button glsr-flyout-item<?php echo 'dashicons-star-filled' === $item['icon'] ? ' glsr-flyout-premium' : ''; ?>"
                tabindex="0"
                <?php if (wp_parse_url($item['url'], PHP_URL_HOST) !== wp_parse_url(get_home_url(), PHP_URL_HOST)) { ?>
                    rel="noopener noreferrer"
                    target="_blank"
                <?php } ?>
            >
                <div class="glsr-flyout-label">
                    <div><?php echo esc_html($item['title']); ?></div>
                </div>
                <i class="dashicons <?php echo esc_attr($item['icon']); ?>"></i>
            </a>
        <?php } ?>
    </div>
    <a href="javascript:void(0);" class="glsr-flyout-button glsr-flyout-mascot" tabindex="0">
        <div class="glsr-flyout-label">
            <div><?php echo _x('Do you need help?', 'admin-text', 'site-reviews'); ?></div>
        </div>
        <?php echo \GeminiLabs\SiteReviews\Helper::svg('assets/images/icon.svg', false); ?>
    </a>
</div>
