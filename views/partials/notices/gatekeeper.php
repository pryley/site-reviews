<?php defined('ABSPATH') || exit; ?>

<p>
    <?php echo $message; ?>
</p>
<?php if (!empty($actions)) { ?>
    <p class="glsr-notice-buttons">
        <?php echo $actions; ?>
    </p>
<?php } ?>
