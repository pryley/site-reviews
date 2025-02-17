<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        if (!$this->isInstalled()) {
            return;
        }
        if (!$this->isVersionSupported()) {
            $this->notify('Bricks');
            return;
        }
        $this->hook(Controller::class, [
            ['filterBuilderI18n', 'bricks/builder/i18n'],
            ['filterSettingsMultiCheckbox', 'bricks/element/settings', 10, 2],
            ['filterSettingsPrefixedId', 'bricks/element/settings', 10, 2],
            ['printInlineStyles', 'wp_enqueue_scripts', 15],
            ['registerElements', 'init', 11],
            ['searchAssignedPosts', 'wp_ajax_bricks_glsr_assigned_posts'],
            ['searchAssignedTerms', 'wp_ajax_bricks_glsr_assigned_terms'],
            ['searchAssignedUsers', 'wp_ajax_bricks_glsr_assigned_users'],
            ['searchPostId', 'wp_ajax_bricks_glsr_post_id'],
        ]);
    }

    protected function isInstalled(): bool
    {
        return 'bricks' === wp_get_theme(get_template())->get('TextDomain');
    }

    protected function supportedVersion(): string
    {
        return '1.11';
    }

    protected function version(): string
    {
        if ($this->isInstalled()) {
            return (string) wp_get_theme(get_template())->get('Version');
        }
        return '';
    }
}
