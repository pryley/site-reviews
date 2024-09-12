<?php defined('ABSPATH') || exit; ?>

<?php if (glsr()->can('update_plugins')): ?>
<div class="glsr-card postbox">
    <h3 class="glsr-card-heading">
        <button type="button" class="glsr-accordion-trigger" aria-expanded="false" aria-controls="tools-rollback-plugin">
            <span class="title dashicons-before dashicons-admin-tools"><?php echo _x('Rollback Plugin', 'admin-text', 'site-reviews'); ?></span>
            <span class="icon"></span>
        </button>
    </h3>
    <div id="tools-rollback-plugin" class="inside">
        <?php if (empty($rollback_versions)) : ?>
            <div class="glsr-notice-inline components-notice is-error" style="margin-bottom:1em;">
                <p class="components-notice__content">
                    <?php echo sprintf(_x('Unable to connect to %s to get the available plugin versions.', 'wordpress.org (admin-text)', 'site-reviews'),
                        '<a href="wordpress.org" target="_blank">wordpress.org</a>'
                    ); ?>
                </p>
            </div>
        <?php else: ?>
            <div class="glsr-notice-inline components-notice is-warning">
                <p class="components-notice__content">
                    <?php echo sprintf(_x('If you are using this tool to fix a problem with %s, please %ssubmit a support request%s so that it can be fixed.', 'admin-text', 'site-reviews'),
                        glsr()->name,
                        '<a data-expand="#support-contact-support" href="'.glsr_admin_url('documentation', 'support').'">', '</a>'
                    ); ?>
                </p>
            </div>
            <p>
                <?php echo sprintf(_x('You currently have version %s installed of %s. Run this tool to rollback to a previous release.', 'admin-text', 'site-reviews'),
                    '<strong>'.glsr()->version.'</strong>',
                    glsr()->name
                ); ?>
            </p>
            <script><?php echo $rollback_script; ?></script>
            <form id="rollback-plugin" method="get" action="<?php echo admin_url('update.php'); ?>">
                <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('rollback-'.glsr()->id); ?>">
                <input type="hidden" name="action" value="<?php echo 'rollback-'.glsr()->id; ?>">
                <p>
                    <label for="rollback_version"><strong><?php echo _x('Rollback Version To', 'admin-text', 'site-reviews'); ?></strong></label><br>
                    <select name="version" id="rollback_version">
                        <?php foreach ($rollback_versions as $version) : ?>
                            <option value="<?php echo $version; ?>"><?php echo glsr()->name; ?> <?php echo $version; ?></option>
                        <?php endforeach; ?>
                    </select>
                </p>
                <button type="submit" class="glsr-button button button-large button-primary"
                    data-loading="<?php echo esc_attr_x('Rolling back to %s, please wait...', 'admin-text', 'site-reviews'); ?>"
                ><?php echo _x('Rollback Plugin', 'admin-text', 'site-reviews'); ?>
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
