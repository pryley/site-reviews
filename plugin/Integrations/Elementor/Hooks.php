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
            ['filterElementorPublicInlineScript', 'site-reviews/enqueue/public/inline-script/after', 1],
            ['filterElementorStarRatingDefaults', 'site-reviews/defaults/star-rating/defaults'],
            ['filterGeneratedSchema', 'site-reviews/schema/generate'],
            ['parseElementCss', 'elementor/element/parse_css', 10, 2],
            ['registerElementorCategory', 'elementor/elements/categories_registered'],
            ['registerElementorWidgets', 'elementor/widgets/register'],
            ['registerInlineStyles', 'admin_enqueue_scripts', 20],
            ['registerInlineStyles', 'elementor/editor/after_enqueue_styles'],
            ['registerInlineStyles', 'elementor/preview/enqueue_styles'],
            ['registerScripts', 'elementor/editor/after_enqueue_scripts'],
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
