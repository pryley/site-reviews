<?php defined('ABSPATH') || exit; ?>

<?php if (count($settings) > 1) { ?>
    <ul class="glsr-subsubsub subsubsub">
    <?php foreach ($settings as $key => $rows) { ?>
        <li><a href="<?php echo glsr_admin_url('settings', 'integrations', $key); ?>" tabindex="0"><?php echo $subsubsub[$key] ?? ucfirst($key); ?></a><span>|</span></li>
    <?php } ?>
    </ul>
<?php } ?>

<?php foreach ($settings as $key => $rows) { ?>
    <div class="glsr-nav-view-section" id="<?php echo esc_attr($key); ?>">
        <?php glsr()->action("settings/{$key}", $rows); ?>
    </div>
<?php } ?>
