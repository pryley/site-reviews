<?php defined('ABSPATH') || exit; ?>

<div id="glsr-flyout">
    <div id="glsr-flyout-items">
        <?php foreach ($items as $index => $item) { ?>
            <a href="<?php echo esc_url($item['url']); ?>"
               target="_blank"
               rel="noopener noreferrer"
               class="glsr-flyout-button glsr-flyout-item glsr-flyout-item-<?php echo $index; ?>"
               <?php echo !empty($item['bgcolor']) ? 'style="background-color:'.esc_attr($item['bgcolor']).'"' : ''; ?>
               <?php echo !empty($item['hover_bgcolor']) ? 'onMouseOver="this.style.backgroundColor=\''.esc_attr($item['hover_bgcolor']).'\'" onMouseOut="this.style.backgroundColor=\''.esc_attr($item['bgcolor']).'\'"' : ''; ?>
            >
                <div class="glsr-flyout-label"><?php echo esc_html($item['title']); ?></div>
                <i class="dashicons <?php echo sanitize_html_class($item['icon']); ?>"></i>
            </a>
        <?php } ?>
    </div>
    <a href="#" class="glsr-flyout-button glsr-flyout-head">
        <div class="glsr-flyout-label">
            <?php echo _x('See Quick Links', 'admin-text', 'site-reviews'); ?>
        </div>
        <div class="glsr-flyout-mascot">
            <?php echo file_get_contents(glsr()->path('assets/images/mascot-alt.svg')); ?>
        </div>
    </a>
</div>
