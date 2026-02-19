<?php defined('ABSPATH') || exit;

$form = new \GeminiLabs\SiteReviews\Modules\Html\Form([], [], glsr()->config('export'));

?>
<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-export-reviews">
            <span class="title dashicons-before dashicons-admin-tools"><?php echo _x('Export Reviews', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-export-reviews" class="inside">
        <div class="glsr-notice-inline components-notice is-info">
            <p class="components-notice__content">
                <?php echo sprintf(
                    _x('You can also use the WordPress %s and %s tools to export and import your reviews and categories.', 'admin-text', 'site-reviews'),
                    sprintf('<a href="%s">%s</a>', admin_url('export.php'), _x('Export', 'admin-text', 'site-reviews')),
                    sprintf('<a href="%s">%s</a>', admin_url('import.php'), _x('Import', 'admin-text', 'site-reviews'))
                ); ?>
            </p>
        </div>
        <p><?php echo sprintf(
            _x('Here you can export your reviews to a %s file. If you are planning to import these reviews into a different website, you may want to export assignment values as slugs\usernames because the IDs on the other website will likely be different.', 'admin-text', 'site-reviews'),
            '<code>*.csv</code>',
            '<code>post_type:slug</code>'
        ); ?></p>
        <form method="post">
            <?php wp_nonce_field('export-reviews'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="export-reviews">
            <fieldset>
                <?php echo implode('', $form->visible()); ?>
            </fieldset>
            <div>
                <button type="submit" class="glsr-button button button-large button-primary">
                    <?php echo _x('Export Reviews', 'admin-text', 'site-reviews'); ?>
                </button>
            </div>
        </form>
    </div>
</div>
