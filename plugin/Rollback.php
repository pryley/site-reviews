<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Overrides\PluginUpgrader;
use GeminiLabs\SiteReviews\Overrides\PluginUpgraderSkin;

class Rollback
{
    protected $version;

    /**
     * @param string $plugin
     * @return void
     */
    public function reactivate($plugin)
    {
        $failure = filter_input(INPUT_GET, 'failure');
        $success = filter_input(INPUT_GET, 'success');
        $nonce = filter_input(INPUT_GET, '_wpnonce');
        $networkwide = filter_input(INPUT_GET, 'networkwide');
        if (empty($failure) && empty($success)) {
            wp_redirect(admin_url('update.php?action=reactivate-plugin&failure=true&plugin='.urlencode($plugin).'&_wpnonce='.$nonce));
            activate_plugin($plugin, '', !empty($networkwide), true);
            wp_redirect(admin_url('update.php?action=activate-plugin&success=true&plugin='.urlencode($plugin).'&_wpnonce='.$nonce));
            exit();
        }
        iframe_header(__('Plugin Reactivation'), true);
        if ($success) {
            printf('<p>%s</p>', __('Plugin reactivated successfully.'));
            $format = '<a href="%s" target="_parent">%s</a>';
            $actions = [
                sprintf($format, glsr_admin_url('tools', 'general'), _x('Go backz', 'admin-text', 'site-reviews')),
                sprintf($format, self_admin_url('plugins.php'), __('Go to Plugins page')),
            ];
            printf('<p>%s</p>', implode(' | ', $actions));
        }
        if ($failure) {
            printf('<p>%s</p>', __('Plugin failed to reactivate due to a fatal error.'));
            error_reporting(E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR);
            ini_set('display_errors', true); // Ensure that fatal errors are displayed.
            wp_register_plugin_realpath(WP_PLUGIN_DIR.'/'.$plugin);
            include WP_PLUGIN_DIR.'/'.$plugin;
        }
        iframe_footer();
    }

    /**
     * @param string $version
     * @return void
     */
    public function rollback($version)
    {
        global $title, $parent_file;
        $plugin = 'classic-editor/classic-editor.php';
        $parent_file = 'edit.php?post_type='.glsr()->post_type;
        $title = _x('Rollback Site Reviews', 'admin-text', 'site-reviews');
        $nonce = 'upgrade-plugin_'.$plugin;
        $url = 'update.php?action=upgrade-plugin&plugin='.urlencode($plugin);
        wp_enqueue_script('updates');
        require_once ABSPATH.'wp-admin/admin-header.php';
        $upgrader = new PluginUpgrader(
            new PluginUpgraderSkin(compact('title', 'nonce', 'url', 'plugin'))
        );
        $upgrader->rollback($version);
        require_once ABSPATH.'wp-admin/admin-footer.php';
    }

    /**
     * @param string $version
     * @return void
     */
    public function rollbackAjax($version)
    {
        set_transient(glsr()->prefix.'rollback_version', $version, MINUTE_IN_SECONDS);
        return [
            'data' => [
                '_ajax_nonce' => wp_create_nonce('updates'),
                'action' => 'update-plugin',
                'plugin' => plugin_basename(glsr()->file),
                'slug' => glsr()->id,
            ],
            'url' => glsr_admin_url('welcome'),
        ];
    }
}
