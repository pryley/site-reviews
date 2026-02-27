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
            ['filterModalWrappedBy', 'site-reviews/modal_wrapped_by'],
            ['filterPostIdCallback', 'vc_autocomplete_site_review_post_id_callback', 10, 2],
            ['filterPostIdRender', 'vc_autocomplete_site_review_post_id_render', 10, 3],
            ['filterWrapAttrClass', 'site-reviews/shortcode/wrap/attributes', 10, 3],
            ['filterWrapAttrStyle', 'site-reviews/shortcode/wrap/attributes', 10, 3],
            ['registerParameters', 'vc_load_default_params'],
            ['registerShortcodes', 'vc_before_init', 5],
        ]);
        foreach ([
            'site_reviews',
            'site_reviews_form',
            'site_reviews_summary',
        ] as $shortcode) {
            $this->hook(Controller::class, [
                ['filterAssignedPostsCallback', "vc_autocomplete_{$shortcode}_assigned_posts_callback", 10, 2],
                ['filterAssignedPostsRender', "vc_autocomplete_{$shortcode}_assigned_posts_render", 10, 3],
                ['filterAssignedTermsCallback', "vc_autocomplete_{$shortcode}_assigned_terms_callback", 10, 2],
                ['filterAssignedTermsRender', "vc_autocomplete_{$shortcode}_assigned_terms_render", 10, 3],
                ['filterAssignedUsersCallback', "vc_autocomplete_{$shortcode}_assigned_users_callback", 10, 2],
                ['filterAssignedUsersRender', "vc_autocomplete_{$shortcode}_assigned_users_render", 10, 3],
                ['filterAuthorCallback', "vc_autocomplete_{$shortcode}_author_callback", 10, 2],
                ['filterAuthorRender', "vc_autocomplete_{$shortcode}_author_render", 10, 3],
            ]);
        }
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
