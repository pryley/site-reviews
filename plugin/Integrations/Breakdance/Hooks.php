<?php

namespace GeminiLabs\SiteReviews\Integrations\Breakdance;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

class Hooks extends IntegrationHooks
{
    public function levelPluginsLoaded(): ?int
    {
        return 5;
    }

    /**
     * The "breakdance_loaded" hook is triggered on plugins_loaded:10
     * so we need to load hooks earlier than that.
     * 
     * @action plugins_loaded:5
     */
    public function onPluginsLoaded(): void
    {
        if (!$this->isInstalled()) {
            return;
        }
        if (!$this->isVersionSupported()) {
            $this->notify('Breakdance');
            return;
        }
        $this->hook(Controller::class, [
            ['interceptGetPostsQuery', 'breakdance_ajax_breakdance_get_posts', 1],
            ['interceptGetPostsQuery', 'wp_ajax_breakdance_get_posts', 1],
            ['interceptGetPostsQuery', 'wp_ajax_nopriv_breakdance_get_posts', 1],
            ['printInlineStyles', 'unofficial_i_am_kevin_geary_master_of_all_things_css_and_html'],
            ['registerDesignControls', 'init'],
            ['registerElements', 'breakdance_loaded', 5], // run early
            ['registerRoutes', 'breakdance_loaded', 5], // run early
        ]);
    }

    protected function isInstalled(): bool
    {
        return class_exists('Breakdance\Elements\Element')
            && function_exists('Breakdance\AJAX\get_nonce_for_ajax_requests')
            && function_exists('Breakdance\AJAX\get_nonce_key_for_ajax_requests')
            && function_exists('Breakdance\AJAX\register_handler')
            && function_exists('Breakdance\Elements\c')
            && function_exists('Breakdance\Elements\PresetSections\getPresetSection')
            && function_exists('Breakdance\Elements\registerCategory')
            && function_exists('Breakdance\Permissions\hasMinimumPermission');
    }

    protected function supportedVersion(): string
    {
        return '2.2.0';
    }

    protected function version(): string
    {
        return defined('__BREAKDANCE_VERSION')
            ? (string) \__BREAKDANCE_VERSION
            : '';
    }
}
