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
        $this->strings['downloading_package'] = sprintf(__('Downloading from %s&#8230;'), '<span class="code">%s</span>');
        $this->strings['installing_package']   = __( 'Installing the new version&#8230;' );
        $this->strings['no_package'] = __('Update package not available.');
        $this->strings['process_failed'] = __('Plugin rollback failed.');
        $this->strings['process_success'] = __('Reactivating plugin&#8230');
        $this->strings['remove_old'] = __('Removing the old version&#8230;');
        $this->strings['remove_old_failed'] = __('Could not remove the old plugin.');
        $this->strings['unpack_package'] = __('Unpacking the update&#8230;');
        $this->strings['up_to_date'] = __('The plugin is at the latest version.');
    }
}
