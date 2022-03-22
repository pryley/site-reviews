<?php

namespace GeminiLabs\SiteReviews\Overrides;

class PluginUpgrader extends \Plugin_Upgrader
{
    public function rollback($version, $args = [])
    {
        $args = wp_parse_args($args, ['clear_update_cache' => true]);
        $this->init();
        $this->upgrade_strings();
        add_filter('upgrader_pre_install', [$this, 'deactivate_plugin_before_upgrade'], 10, 2);
        add_filter('upgrader_clear_destination', [$this, 'delete_old_plugin'], 10, 4);
        $plugin = dirname($this->skin->plugin);
        $this->run([
            'package' => sprintf('https://downloads.wordpress.org/plugin/%s.%s.zip', $plugin, $version),
            'destination' => WP_PLUGIN_DIR,
            'clear_destination' => true,
            'clear_working' => true,
            'hook_extra' => [
                'plugin' => $this->skin->plugin,
                'type' => 'plugin',
                'action' => 'update',
            ],
        ]);
        remove_filter('upgrader_pre_install', [$this, 'deactivate_plugin_before_upgrade']);
        remove_filter('upgrader_clear_destination', [$this, 'delete_old_plugin']);
        if (!$this->result || is_wp_error($this->result)) {
            return $this->result;
        }
        wp_clean_plugins_cache($args['clear_update_cache']);
        return true;
    }

    public function upgrade_strings()
    {
        parent::upgrade_strings();
        $this->strings['process_success'] = _x('Plugin rollback successful! Please wait while it reactivates&#8230', 'admin-text', 'site-reviews');
    }
}
