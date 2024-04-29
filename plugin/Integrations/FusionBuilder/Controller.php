<?php

namespace GeminiLabs\SiteReviews\Integrations\FusionBuilder;

use GeminiLabs\SiteReviews\Controllers\AbstractController;

class Controller extends AbstractController
{
    /**
     * @filter site-reviews/defaults/style-classes/defaults
     */
    public function filterButtonClass(array $defaults): array
    {
        if ('default' !== glsr_get_option('general.style')) {
            return $defaults;
        }
        if (!defined('AVADA_VERSION')) {
            return $defaults;
        }
        $defaults['button'] = 'glsr-button fusion-button fusion-button-default fusion-button-default-size fusion-button-default-span fusion-button-default-type';
        return $defaults;
    }

    /**
     * @filter site-reviews/enqueue/public/inline-script/after
     */
    public function filterPublicInlineScript(string $script): string
    {
        $script .= '"undefined"!==typeof jQuery&&(';
        $script .= 'jQuery(window).on("load fusion-element-render-site_review fusion-element-render-site_reviews fusion-element-render-site_reviews_form fusion-element-render-site_reviews_summary",function(){';
        $script .= 'jQuery(".fusion-builder-live").length&&(GLSR.Event.trigger("site-reviews/init"))';
        $script .= '})';
        $script .= ');';
        return $script;
    }

    /**
     * @action site-reviews/activated
     */
    public function onActivated(): void
    {
        fusion_builder_auto_activate_element('site_review');
        fusion_builder_auto_activate_element('site_reviews');
        fusion_builder_auto_activate_element('site_reviews_form');
        fusion_builder_auto_activate_element('site_reviews_summary');
    }

    /**
     * @action fusion_builder_before_init
     */
    public function registerFusionElements(): void
    {
        FusionLatestReviews::registerElement();
        FusionRatingSummary::registerElement();
        FusionReviewForm::registerElement();
        FusionSingleReview::registerElement();
    }
}
