<?php defined('ABSPATH') || exit; ?>

<h2 class="title"><?php echo _x('Custom Text Settings', 'admin-text', 'site-reviews'); ?></h2>

<div class="components-notice is-info" style="margin-left:0;">
    <p class="components-notice__content">
        <?php echo sprintf(_x('If you have a multilingual website, use the %s plugin instead. When Loco Translate asks for the location of the new translation file, select "Custom".', 'admin-text', 'site-reviews'), '<a href="https://wordpress.org/plugins/loco-translate/" target="_blank">Loco Translate</a>'); ?>
    </p>
</div>

<p>
    <?php echo _x('Here you can customise any English text of the plugin that is shown on the frontend of your website, including the field labels and placeholders of the review form.', 'admin-text', 'site-reviews'); ?>
    <?php echo _x('If you are using the Polylang plugin, any custom text you enter here will be translatable on the Polylang "Strings translations" page.', 'admin-text', 'site-reviews'); ?>
</p>

<div class="glsr-strings-form">
    <div class="glsr-search-box" id="glsr-search-translations">
        <span class="screen-reader-text"><?php echo _x('Search here for translatable text', 'admin-text', 'site-reviews'); ?></span>
        <div class="glsr-search-box-wrap">
            <span class="glsr-spinner"><span class="spinner"></span></span>
            <input type="search" class="glsr-search-input" autocomplete="off" placeholder="<?php echo _x('Search here for text to change...', 'admin-text', 'site-reviews'); ?>">
            <div class="glsr-search-results" data-prefix="{{ database_key }}"></div>
        </div>
    </div>
    <table class="glsr-strings-table wp-list-table widefat striped {{ class }}">
        <thead>
            <tr>
                <th scope="col" class="manage-column column-primary"><?php echo _x('Original Text', 'admin-text', 'site-reviews'); ?></th>
                <th scope="col" class="manage-column"><?php echo _x('Custom Text', 'admin-text', 'site-reviews'); ?></th>
            </tr>
        </thead>
        <tbody>{{ strings }}</tbody>
    </table>
    <input type="hidden" name="{{ database_key }}[settings][strings][]">
</div>

<script type="text/html" id="tmpl-glsr-string-plural">
<?php include glsr()->path('views/partials/strings/plural.php'); ?>
</script>
<script type="text/html" id="tmpl-glsr-string-single">
<?php include glsr()->path('views/partials/strings/single.php'); ?>
</script>
