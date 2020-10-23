<?php defined('ABSPATH') || die; ?>

<fieldset class="inline-edit-col-right">
    <div class="inline-edit-col">
        <div class="inline-edit-group wp-clearfix">
            <span class="title"><?= _x('Assigned Users', 'admin-text', 'site-reviews'); ?></span>
            <div class="glsr-search-multibox" id="glsr-search-users">
                <div class="glsr-spinner"><span class="spinner"></span></div>
                <div class="glsr-search-multibox-entries">
                    <div class="glsr-selected-entries"></div>
                    <input class="glsr-search-input" type="search" autocomplete="off" placeholder="<?= esc_attr_x('Search by ID or name...', 'admin-text', 'site-reviews'); ?>">
                </div>
                <div class="glsr-search-results"></div>
            </div>
        </div>
    </div>
    <script type="text/html" id="tmpl-glsr-assigned-users">
    <?php include glsr()->path('views/partials/editor/assigned-entry.php'); ?>
    </script>
</fieldset>
