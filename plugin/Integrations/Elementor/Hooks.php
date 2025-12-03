<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

use GeminiLabs\SiteReviews\Integrations\IntegrationHooks;

class Hooks extends IntegrationHooks
{
    public function run(): void
    {
        if (!$this->isInstalled()) {
            return;
        }
        if (!$this->isVersionSupported()) {
            $this->notify('Elementor');
            return;
        }
        $this->hook(Controller::class, [
            ['filterGeneratedSchema', 'site-reviews/schema/generate'],
            ['filterModalWrappedBy', 'site-reviews/modal_wrapped_by'],
            ['filterPublicInlineScript', 'site-reviews/enqueue/public/inline-script/after', 1],
            ['filterStarRatingDefaults', 'site-reviews/defaults/star-rating/defaults'],
            ['registerAjaxActions', 'elementor/ajax/register_actions'],
            ['registerCategory', 'elementor/elements/categories_registered'],
            ['registerControls', 'elementor/controls/register'],
            ['registerInlineStyles', 'admin_enqueue_scripts', 20],
            ['registerInlineStyles', 'elementor/editor/after_enqueue_styles'],
            ['registerInlineStyles', 'elementor/preview/enqueue_styles'],
            ['registerScripts', 'elementor/editor/after_enqueue_scripts'],
            ['registerWidgets', 'elementor/widgets/register'],
            ['searchAssignedTerms', 'site-reviews/route/ajax/elementor-assigned_terms'],
        ]);
    }

    protected function isInstalled(): bool
    {
        return class_exists('Elementor\Plugin');
    }

    protected function supportedVersion(): string
    {
        return '3.19.0';
    }

    protected function version(): string
    {
        return defined('ELEMENTOR_VERSION')
            ? (string) \ELEMENTOR_VERSION
            : '';
    }
}
