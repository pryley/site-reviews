<?php defined('ABSPATH') || exit;

$screen = get_current_screen();
foreach ($addons as $addon) {
    $plugin = sprintf('%1$s/%1$s.php', $addon::ID);
    if (!is_plugin_active($plugin)) {
        continue;
    }
    $args = [
        'action' => 'deactivate',
        'plugin' => $plugin,
        'plugin_status' => 'all',
    ];
    $url = add_query_arg($args, self_admin_url('plugins.php'));
    $url = wp_nonce_url($url, "deactivate-plugin_{$plugin}");
    $hasAction = ($screen && $screen->in_admin('network'))
        ? current_user_can('manage_network_plugins')
        : current_user_can('deactivate_plugin', $plugin);
?>
<div class="notice notice-error is-dismissible glsr-notice" data-dismiss="retired">
    <p>
        <?php echo sprintf(_x('The %s addon has been merged into Site Reviews. Please deactivate it.', 'admin-text', 'site-reviews'),
            '<strong>'.$addon::NAME.'</strong>'
        ); ?>
    </p>
    <?php if ($hasAction) { ?>
        <p class="glsr-notice-buttons">
            <a class="button button-primary" href="<?php echo $url; ?>">
                <?php echo sprintf(_x('Deactivate %s', 'admin-text', 'site-reviews'), $addon::NAME); ?>
            </a>
        </p>
    <?php } ?>
</div>
<?php } ?>
