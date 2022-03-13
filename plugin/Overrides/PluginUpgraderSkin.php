<?php

namespace GeminiLabs\SiteReviews\Overrides;

class PluginUpgraderSkin extends \Plugin_Upgrader_Skin
{
    public function after()
    {
        $this->plugin = $this->upgrader->plugin_info();
        $this->decrement_update_count('plugin');
        if (!empty($this->plugin) && !is_wp_error($this->result) && $this->plugin_active) {
            // Currently used only when JS is off for a single plugin update?
            $args = [
                'action' => 'reactivate-plugin',
                'networkwide' => $this->plugin_network_active,
                'plugin' => urlencode($this->plugin),
            ];
            $html = '<iframe title="%s" style="border:0;overflow:hidden" width="100%%" height="170" src="%s"></iframe>';
            $url = wp_nonce_url(add_query_arg($args, 'update.php'), 'reactivate-plugin_'.$this->plugin);
            printf($html, esc_attr__('Update progress'), $url);
        } else {
            $format = '<a href="%s" target="_parent">%s</a>';
            $actions = [
                'tools_page' => sprintf($format, glsr_admin_url('tools', 'general'), _x('Go back', 'admin-text', 'site-reviews')),
                'plugins_page' => sprintf($format, self_admin_url('plugins.php'), __('Go to Plugins page')),
            ];
            $this->feedback(implode(' | ', $actions));
        }
    }

    public function feedback($feedback, ...$args)
    {
        if ('process_success' === $feedback) {
            printf('<p style="margin-bottom:0;">%s</p>', $this->upgrader->strings[$feedback]);
            wp_ob_end_flush_all();
            flush();
            return;
        }
        parent::feedback($feedback, ...$args);
    }
}
