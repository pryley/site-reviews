<?php defined('ABSPATH') || exit; ?>

<div id="<?= esc_attr($id); ?>" class="glsr-filter <?= sanitize_html_class($class); ?>" role="combobox" aria-haspopup="true" aria-expanded="false" data-action="<?= esc_attr($action); ?>">
    <input class="glsr-filter__value" type="hidden" name="<?= esc_attr($name); ?>" value="<?= esc_attr($value); ?>">
    <span class="glsr-filter__selected" role="textbox" aria-readonly="true" tabindex="0" title="<?= esc_attr($selected); ?>"><?= esc_html($selected); ?></span>
    <div class="glsr-filter__dropdown">
        <input class="glsr-filter__search" type="search" role="searchbox"
            aria-autocomplete="list" aria-controls="<?= esc_attr($id); ?>-listbox" aria-label="<?= esc_attr_x('Search', 'admin-text', 'site-reviews'); ?>"
            autocapitalize="none" autocomplete="off" autocorrect="off" spellcheck="false"
            placeholder="<?= esc_attr_x('Search...', 'admin-text', 'site-reviews'); ?>"
            tabindex="0"
        >
        <div id="<?= esc_attr($id); ?>-listbox" class="glsr-filter__results" role="listbox" aria-expanded="false" aria-hidden="true"></div>
    </div>
</div>
