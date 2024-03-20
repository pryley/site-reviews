<?php defined('ABSPATH') || exit; ?>

<div class="notice notice-error is-dismissible glsr-notice" data-dismiss="gatekeeper-internal">
    <p>
        <?php
            $message = _nx(
                '%s needs an update to work with %s.',
                '%s needs an update to work with the following plugins: %s',
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
