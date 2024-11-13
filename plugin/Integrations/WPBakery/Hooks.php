<?php

namespace GeminiLabs\SiteReviews\Integrations\WPBakery;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        if (!$this->isInstalled()) {
            return;
        }
        if (!$this->isVersionSupported()) {
            $this->notify('WPBakery Page Builder');
            return;
        }
        $this->hook(Controller::class, [
            ['enqueueInlineScripts', 'vc_frontend_editor_enqueue_js_css'],
            ['enqueueInlineStyles', 'vc_backend_editor_enqueue_js_css'],
            ['enqueueInlineStyles', 'vc_frontend_editor_enqueue_js_css'],
            ['filterAutocompleteAssignedPosts', 'vc_autocomplete_site_reviews_assigned_posts_callback'],
            ['filterAutocompleteAssignedPosts', 'vc_autocomplete_site_reviews_form_assigned_posts_callback'],
            ['filterAutocompleteAssignedPosts', 'vc_autocomplete_site_reviews_summary_assigned_posts_callback'],
            ['filterAutocompleteAssignedTerms', 'vc_autocomplete_site_reviews_assigned_terms_callback'],
            ['filterAutocompleteAssignedTerms', 'vc_autocomplete_site_reviews_form_assigned_terms_callback'],
            ['filterAutocompleteAssignedTerms', 'vc_autocomplete_site_reviews_summary_assigned_terms_callback'],
            ['filterAutocompleteAssignedUsers', 'vc_autocomplete_site_reviews_assigned_users_callback'],
            ['filterAutocompleteAssignedUsers', 'vc_autocomplete_site_reviews_form_assigned_users_callback'],
            ['filterAutocompleteAssignedUsers', 'vc_autocomplete_site_reviews_summary_assigned_users_callback'],
            ['filterAutocompletePostId', 'vc_autocomplete_site_review_post_id_callback'],
            ['filterSettingOutput', 'vc_single_param_edit_holder_output', 10, 4],
            ['registerParameters', 'vc_load_default_params'],
            ['registerShortcodes', 'vc_before_init', 5],
        ]);
    }

    protected function isInstalled(): bool
    {
        return class_exists('WPBakeryShortCode')
            && function_exists('vc_add_shortcode_param')
            && function_exists('vc_map')
            && defined('WPB_VC_VERSION');
    }

    protected function supportedVersion(): string
    {
        return '8.0';
    }

    protected function version(): string
    {
        return defined('WPB_VC_VERSION')
            ? (string) \WPB_VC_VERSION
            : '';
    }
}
