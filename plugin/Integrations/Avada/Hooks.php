<?php

namespace GeminiLabs\SiteReviews\Integrations\Avada;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        if (!$this->isInstalled()) {
            return;
        }
        if (!$this->isVersionSupported()) {
            $this->notify('Avada Builder');
            return;
        }
        $this->hook(Controller::class, [
            ['enqueueBuilderStyles', 'fusion_builder_admin_scripts_hook'],
            ['enqueueBuilderStyles', 'fusion_builder_enqueue_live_scripts'],
            ['filterBuilderPluginElements', 'fusion_builder_plugin_elements'],
            ['filterButtonClass', 'site-reviews/defaults/style-classes/defaults'],
            ['filterModalWrappedBy', 'site-reviews/modal_wrapped_by'],
            ['filterPublicInlineScript', 'site-reviews/enqueue/public/inline-script/after'],
            ['filterRegisterWidgets', 'site-reviews/register/widgets'],
            ['filterWrapAttrClass', 'site-reviews/shortcode/wrap/attributes', 10, 3],
            ['filterWrapAttrStyle', 'site-reviews/shortcode/wrap/attributes', 10, 3],
            ['onActivated', 'site-reviews/activated'],
            ['registerElements', 'fusion_builder_shortcodes_init'],
            ['runSearchQuery', 'wp_ajax_glsr_fusion_search_query'],
        ]);
    }

    protected function isInstalled(): bool
    {
        return function_exists('FusionBuilder')
            && defined('FUSION_BUILDER_VERSION');
    }

    protected function supportedVersion(): string
    {
        return '3.12.0';
    }

    protected function version(): string
    {
        if (defined('FUSION_BUILDER_VERSION')) {
            return (string) \FUSION_BUILDER_VERSION;
        }
        return '';
    }
}
