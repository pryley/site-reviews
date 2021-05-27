<?php defined('ABSPATH') || die; ?>

<fieldset class="metabox-prefs">
    <legend><?= _x('Filters', 'admin-text', 'site-reviews'); ?></legend>
    <?php foreach ($filters as $name => $filter) : ?>
        <label>
            <input class="enable-filter-tog" name="<?= $setting; ?>[]" type="checkbox" value="<?= $name; ?>" <?php checked(in_array($name, $enabled), true); ?> />
            <?= $filter; ?>
        </label>
    <?php endforeach; ?>
</fieldset>
