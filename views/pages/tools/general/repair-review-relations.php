<?php glsr()->hasPermission('settings') || die; ?>

<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-repair-review-relations">
            <span class="title dashicons-before dashicons-admin-tools"><?= _x('Repair Review Relations', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-repair-review-relations" class="inside">
        <?php if (!empty($myisam_tables)) { ?>
            <div class="components-notice is-info" style="margin-bottom:1em;">
                <p class="components-notice__content"><?= sprintf(
                    _x('Once you have repaired the review relationships, it is recommended that you run the %s tool to prevent the problem from happening again.', 'admin-text', 'site-reviews'),
                        sprintf('<a data-expand="#tools-optimise-db-tables" href="%s">%s</a>',
                            admin_url('edit.php?post_type='.glsr()->post_type.'&page=tools#tab-general'),
                            _x('Optimise Your Database Tables', 'admin-text', 'site-reviews')
                        )
                    ); ?>
                </p>
            </div>
            <p><?= _x('Site Reviews stores review details in a custom database table, these entries are linked to the review post type in the WordPress posts table using the review\'s Post ID.', 'admin-text', 'site-reviews'); ?></p>
            <p><?= _x('If your database tables use the standard InnoDB engine, foreign indexes are used to maintain the relationship between the two so that when a review is deleted from the WordPress posts table, the review details are also removed from the custom table.', 'admin-text', 'site-reviews'); ?></p>
            <p><?= _x('However, if your database tables use the outdated MyISAM engine, then foreign indexes cannot be used so Site Reviews depends on the built-in WordPress hooks (which are triggered when a review is deleted) in order to remove the relationship and delete the review details from the custom database table.', 'admin-text', 'site-reviews'); ?></p>
            <p><?= _x('Depending on the WordPress hooks is problematic because there are many unknown factors involved which may prevent these hooks from being triggered (i.e. manually deleting a review from the database, using a third-party plugin which overrides the hooks, etc.), this can mess up the relationships between the review in the WordPress posts table and the review details in the custom database table and when this happens, it may cause your reviews to incorrectly use the content of a non-review post type.', 'admin-text', 'site-reviews'); ?></p>
            <p><?= _x('This tool will repair the review relationships in your database by removing any review details in the custom database table that do not point to a valid review.', 'admin-text', 'site-reviews'); ?></p>
            <form method="post">
                <?php wp_nonce_field('repair-review-relations'); ?>
                <input type="hidden" name="{{ id }}[_action]" value="repair-review-relations">
                <button type="submit" class="glsr-button components-button is-secondary" id="repair-review-relations" data-ajax-click data-ajax-scroll>
                    <span data-loading="<?= esc_attr_x('Repairing relations, please wait...', 'admin-text', 'site-reviews'); ?>"><?= _x('Repair Relations', 'admin-text', 'site-reviews'); ?></span>
                </button>
            </form>
        <?php } else { ?>
            <div class="components-notice is-success" style="margin-bottom:1em;">
                <p class="components-notice__content"><?= _x('Repair is unnecessary because your database tables use the InnoDB engine!', 'admin-text', 'site-reviews'); ?> ✨</p>
            </div>
        <?php } ?>
    </div>
</div>
