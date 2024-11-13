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
            ['enqueueBuilderStyles', 'fusion_builder_enqueue_live_scripts'],
            ['filterButtonClass', 'site-reviews/defaults/style-classes/defaults'],
            ['filterPublicInlineScript', 'site-reviews/enqueue/public/inline-script/after'],
            ['onActivated', 'site-reviews/activated'],
            ['registerFusionElements', 'fusion_builder_before_init'],
        ]);
    }

    protected function isInstalled(): bool
    {
        return class_exists('FusionBuilder') && defined('FUSION_BUILDER_VERSION');
    }

    protected function supportedVersion(): string
    {
        return '3.11.0';
    }

    protected function version(): string
    {
        if (defined('FUSION_BUILDER_VERSION')) {
            return (string) \FUSION_BUILDER_VERSION;
        }
        return '';
    }
}
