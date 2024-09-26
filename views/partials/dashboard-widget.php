<?php defined('ABSPATH') || exit; ?>

<ul class="glsr-dashboard__items">
    <?php
    foreach ($data as $key => $values) {
        $label = _nx_noop('%s review', '%s reviews', 'admin-text', 'site-reviews');
        $label = translate_nooped_plural($label, $values['value'], 'site-reviews');
    ?>
    <li class="glsr-dashboard__item">
        <a href="<?php echo $values['url']; ?>" class="dashicons-before <?php echo $values['dashicon']; ?> count-<?php echo $values['value']; ?>">
            <span>
                <strong><?php echo sprintf($label, number_format_i18n($values['value'])); ?></strong>
                <?php echo $values['label']; ?>
            </span>
        </a>
    </li>
    <?php } ?>
</ul>

<p class="glsr-dashboard__footer">
    <span>
        <a href="<?php echo glsr_admin_url('tools', 'system-info'); ?>">
            <?php echo _x('Version', 'admin-text', 'site-reviews'); ?> <?php echo glsr()->version; ?>
        </a>
    </span>
    <span>
        <a href="<?php echo glsr_admin_url('documentation'); ?>">
            <?php echo _x('Help', 'admin-text', 'site-reviews'); ?>
        </a>
    </span>
    <span>
        <a href="https://wordpress.org/support/plugin/site-reviews" target="_blank">
            <?php echo _x('Support Forum', 'admin-text', 'site-reviews'); ?>
            <span class="screen-reader-text">
                <?php echo _x('(opens in a new tab)', 'admin-text', 'site-reviews'); ?>
            </span>
            <span aria-hidden="true" class="dashicons dashicons-external"></span>
        </a>
    </span>
</p>
