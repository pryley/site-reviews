<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

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
            ['filterControls', 'site-reviews/bricks/element/controls', 50],
            ['filterModalWrappedBy', 'site-reviews/modal_wrapped_by'],
            ['filterSettingsClass', 'bricks/element/settings', 10, 2],
            ['filterSettingsMultiCheckbox', 'bricks/element/settings', 10, 2],
            ['filterSettingsPrefixedId', 'bricks/element/settings', 10, 2],
            ['filterThemeStyleControlGroups', 'bricks/theme_styles/control_groups'],
            ['filterThemeStyleControls', 'bricks/theme_styles/controls'],
            ['printInlineStyles', 'admin_enqueue_scripts', 20],
            ['printInlineStyles', 'wp_enqueue_scripts', 20],
            ['registerElements', 'init', 11],
            ['searchAssignedPosts', 'wp_ajax_bricks_glsr_assigned_posts'],
            ['searchAssignedTerms', 'wp_ajax_bricks_glsr_assigned_terms'],
            ['searchAssignedUsers', 'wp_ajax_bricks_glsr_assigned_users'],
            ['searchAuthor', 'wp_ajax_bricks_glsr_author'],
            ['searchPostId', 'wp_ajax_bricks_glsr_post_id'],
        ]);
    }

    protected function isInstalled(): bool
    {
        return 'bricks' === wp_get_theme(get_template())->get('TextDomain');
    }

    protected function supportedVersion(): string
    {
        return '2.1.4';
    }

    protected function version(): string
    {
        if ($this->isInstalled()) {
            return (string) wp_get_theme(get_template())->get('Version');
        }
        return '';
    }
}
