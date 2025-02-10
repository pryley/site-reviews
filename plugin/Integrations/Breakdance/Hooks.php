<?php

namespace GeminiLabs\SiteReviews\Integrations\Breakdance;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

class Hooks extends IntegrationHooks
{
    public function hasPluginsLoaded(): bool
    {
        return true;
    }

    /**
     * @action plugins_loaded:0
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
            ['registerElements', 'breakdance_loaded', 5],
            // Unfortunately we can't perform this action yet because Breakdance
            // does not support AJAX searching in multiselect controls.
            // ['registerRoutes', 'breakdance_loaded'],
            ['searchAssignedPosts', 'breakdance_ajax_breakdance_get_posts', 1],
            ['searchAssignedPosts', 'wp_ajax_breakdance_get_posts', 1],
            ['searchAssignedPosts', 'wp_ajax_nopriv_breakdance_get_posts', 1],
            ['searchAssignedTerms', 'breakdance_ajax_breakdance_get_posts', 1],
            ['searchAssignedTerms', 'wp_ajax_breakdance_get_posts', 1],
            ['searchAssignedTerms', 'wp_ajax_nopriv_breakdance_get_posts', 1],
            ['searchAssignedUsers', 'breakdance_ajax_breakdance_get_posts', 1],
            ['searchAssignedUsers', 'wp_ajax_breakdance_get_posts', 1],
            ['searchAssignedUsers', 'wp_ajax_nopriv_breakdance_get_posts', 1],
            ['searchPostId', 'breakdance_ajax_breakdance_get_posts', 1],
            ['searchPostId', 'wp_ajax_breakdance_get_posts', 1],
            ['searchPostId', 'wp_ajax_nopriv_breakdance_get_posts', 1],
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
