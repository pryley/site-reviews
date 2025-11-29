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
            ['filterControls', 'bricks/elements/site_review/controls', 100],
            ['filterControls', 'bricks/elements/site_reviews/controls', 100],
            ['filterControls', 'bricks/elements/site_reviews_form/controls', 100],
            ['filterControls', 'bricks/elements/site_reviews_summary/controls', 100],
            ['filterSettingsClass', 'bricks/element/settings', 100, 2],
            ['filterSettingsMultiCheckbox', 'bricks/element/settings', 10, 2],
            ['filterSettingsPrefixedId', 'bricks/element/settings', 10, 2],
            ['filterThemeStyleControls', 'bricks/theme_styles/controls'],
            ['filterThemeStyleGroups', 'bricks/theme_styles/control_groups'],
            ['printInlineStyles', 'wp_enqueue_scripts', 15],
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
