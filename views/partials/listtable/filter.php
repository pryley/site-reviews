<?php defined('ABSPATH') || exit; ?>

<div id="<?php echo esc_attr($id); ?>" class="glsr-filter <?php echo esc_attr($class); ?>" role="combobox" aria-haspopup="true" aria-expanded="false" data-action="<?php echo esc_attr($action); ?>">
    <input type="hidden" class="glsr-filter__value" 
        name="<?php echo esc_attr($name); ?>" 
        value="<?php echo esc_attr($value); ?>"
    />
    <span class="glsr-filter__selected" role="textbox" aria-readonly="true" tabindex="0" title="<?php echo esc_attr($selected); ?>"><?php echo esc_html($selected); ?></span>
    <div class="glsr-filter__dropdown">
        <input type="search" class="glsr-filter__search"
            aria-autocomplete="list" aria-controls="<?php echo esc_attr($id); ?>-listbox" aria-label="<?php echo esc_attr_x('Search', 'admin-text', 'site-reviews'); ?>"
            autocapitalize="none" autocomplete="off" autocorrect="off" spellcheck="false"
            placeholder="<?php echo esc_attr_x('Search...', 'admin-text', 'site-reviews'); ?>"
            role="searchbox"
            tabindex="0"
        />
        <div id="<?php echo esc_attr($id); ?>-listbox" class="glsr-filter__results" role="listbox" aria-expanded="false" aria-hidden="true"></div>
    </div>
</div>
