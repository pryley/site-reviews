<?php defined('ABSPATH') || exit; ?>

<fieldset class="metabox-prefs">
    <legend><?php echo esc_html_x('Filters', 'admin-text', 'site-reviews'); ?></legend>
    <?php foreach ($filters as $name => $filter) : ?>
        <label>
            <input class="enable-filter-tog" name="<?php echo esc_attr($setting); ?>[]" type="checkbox" value="<?php echo esc_attr($name); ?>" <?php checked(in_array($name, $enabled), true); ?> />
            <?php echo $filter; ?>
        </label>
    <?php endforeach; ?>
    <div style="margin-top:8px;">
        <?php echo _x('Enabling a filter will only display the dropdown if there are options to filter by, otherwise it will remain hidden.', 'admin-text', 'site-reviews'); ?>
    </div>
</fieldset>
