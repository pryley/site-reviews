<?php defined('ABSPATH') || exit; ?>

<?php if (glsr()->can('edit_users')): ?>
<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-repair-permissions">
            <span class="title dashicons-before dashicons-admin-tools"><?= _x('Repair Permissions', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-repair-permissions" class="inside">
        <p><?= _x('Site Reviews provides custom post_type capabilities that mirror the capabilities of your posts by default. For example, if a user role has permission to edit others posts, then that role will also have permission to edit other users reviews.', 'admin-text', 'site-reviews'); ?></p>
        <p><?= _x('If you have changed the capabilities of your user roles (Administrator, Editor, Author, and Contributor) and you suspect that Site Reviews is not working correctly due to your changes, click the <strong>Hard Reset</strong> button.', 'admin-text', 'site-reviews'); ?></p>
        <form method="post">
            <?php wp_nonce_field('repair-permissions'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="repair-permissions">
            <input type="hidden" name="{{ id }}[alt]" value="0" data-alt>
            <button type="submit" class="glsr-button components-button is-primary"
                data-ajax-click
                data-ajax-scroll
                data-loading="<?= esc_attr_x('Repairing, please wait...', 'admin-text', 'site-reviews'); ?>"
            ><?= _x('Repair Permissions', 'admin-text', 'site-reviews'); ?>
            </button>
            <button type="submit" class="glsr-button components-button is-secondary"
                data-ajax-click
                data-ajax-scroll
                data-alt
                data-loading="<?= esc_attr_x('Repairing, please wait...', 'admin-text', 'site-reviews'); ?>"
            ><?= _x('Hard Reset', 'admin-text', 'site-reviews'); ?>
            </button>
        </form>
    </div>
</div>
<?php endif; ?>
