<?php

namespace GeminiLabs\SiteReviews\Integrations\FusionBuilder;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        if (!$this->isInstalled()) {
            return;
        }
        if (!$this->isVersionSupported()) {
            $this->notify('Fusion Builder');
            return;
        }
        $this->hook(Controller::class, [
            ['enqueueBuilderStyles', 'fusion_builder_admin_scripts_hook'],
            ['enqueueBuilderStyles', 'fusion_builder_enqueue_live_scripts'],
            ['filterBuilderIconMap', 'fusion_builder_icon_map'],
            ['filterButtonClass', 'site-reviews/defaults/style-classes/defaults'],
            ['filterPublicInlineScript', 'site-reviews/enqueue/public/inline-script/after'],
            ['filterRegisterWidgets', 'site-reviews/register/widgets'],
            ['onActivated', 'site-reviews/activated'],
            ['registerFusionElements', 'fusion_builder_before_init'],
            ['runGetQuery', 'wp_ajax_glsr_fusion_get_query'],
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
