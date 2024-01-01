<?php defined('ABSPATH') || exit; ?>

<ul class="glsr-dashboard__items">
    <?php
    foreach ($data as $key => $values) {
        $label = _nx_noop('%d review', '%d reviews', 'admin-text', 'site-reviews');
        $label = translate_nooped_plural($label, $values['value'], 'site-reviews');
    ?>
    <li class="glsr-dashboard__item">
        <a href="<?= $values['url']; ?>" class="dashicons-before <?= $values['dashicon']; ?> count-<?= $values['value']; ?>">
            <span>
                <strong><?= sprintf($label, number_format_i18n($values['value'])); ?></strong>
                <?= $values['label']; ?>
            </span>
        </a>
    </li>
    <?php } ?>
</ul>

<p class="glsr-dashboard__footer">
    <span><?= _x('Version', 'admin-text', 'site-reviews'); ?> <?= glsr()->version; ?></span>
    <a href="<?= glsr_admin_url('documentation'); ?>">
        <?= _x('Help', 'admin-text', 'site-reviews'); ?>
    </a>
    <a href="https://wordpress.org/support/plugin/site-reviews" target="_blank">
        <?= _x('Support Forum', 'admin-text', 'site-reviews'); ?>
        <span class="screen-reader-text">
            <?= _x('(opens in a new tab)', 'admin-text', 'site-reviews'); ?>
        </span>
        <span aria-hidden="true" class="dashicons dashicons-external" style="font-size:18px;vertical-align:-3px;"></span>
    </a>
</p>
