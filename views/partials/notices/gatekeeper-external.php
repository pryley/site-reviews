<?php defined('ABSPATH') || exit; ?>

<div class="notice notice-error is-dismissible glsr-notice" data-dismiss="gatekeeper-external">
    <p>
        <?php
            $message = _nx(
                '%s requires the latest version of %s to enable the integration.',
                '%s requires the latest version of the following plugins to enable integration: %s',
                count($errors),
                'admin-text',
                'site-reviews'
            );
            printf($message, sprintf('<strong>%s</strong>', glsr()->name), $links);
        ?>
    </p>
    <?php if (!empty($actions)) { ?>
        <p class="glsr-notice-buttons">
            <?php echo $actions; ?>
        </p>
    <?php } ?>
</div>
