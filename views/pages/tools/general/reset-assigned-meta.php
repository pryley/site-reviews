<?php defined('ABSPATH') || exit; ?>

<?php if (current_user_can('edit_others_posts')): ?>
<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-reset-assigned-meta">
            <span class="title dashicons-before dashicons-admin-tools"><?php echo _x('Reset Assigned Meta Values', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-reset-assigned-meta" class="inside">
        <p><?php echo _x('Site Reviews stores the individual review count, average rating, and ranking for each assigned post, category, and user. If you suspect that these meta values are incorrect (perhaps you cloned a page that had reviews assigned to it), you may use this tool to recalculate them.', 'admin-text', 'site-reviews'); ?></p>
        <form method="post">
            <?php wp_nonce_field('reset-assigned-meta'); ?>
            <input type="hidden" name="{{ id }}[_action]" value="reset-assigned-meta">
            <button type="submit" class="glsr-button button button-large button-primary"
                data-ajax-click
                data-ajax-scroll
                data-loading="<?php echo esc_attr_x('Resetting values, please wait...', 'admin-text', 'site-reviews'); ?>"
            ><?php echo _x('Reset Meta Values', 'admin-text', 'site-reviews'); ?>
            </button>
        </form>
    </div>
</div>
<?php endif; ?>
