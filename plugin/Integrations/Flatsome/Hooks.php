<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        if (!$this->isInstalled()) {
            return;
        }
        if (!$this->isVersionSupported()) {
            $this->notify('Flatsome');
            return;
        }
        $this->hook(Controller::class, [
            ['printInlineScripts', 'ux_builder_enqueue_scripts'],
            ['printInlineStyles', 'ux_builder_enqueue_scripts'],
            ['registerShortcodes', 'init'],
            ['searchAssignedPosts', 'wp_ajax_ux_builder_search_posts', 1],
            ['searchAssignedUsers', 'wp_ajax_ux_builder_search_posts', 2],
        ]);
    }

    protected function isInstalled(): bool
    {
        return 'flatsome' === wp_get_theme(get_template())->get('TextDomain');
    }

    protected function supportedVersion(): string
    {
        return '3.19.0';
    }

    protected function version(): string
    {
        if ($this->isInstalled()) {
            return (string) wp_get_theme(get_template())->get('Version');
        }
        return '';
    }
}
