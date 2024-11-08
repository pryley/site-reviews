<?php

namespace GeminiLabs\SiteReviews\Integrations\FusionBuilder;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Helper;

class Controller extends AbstractController
{
    /**
     * @action fusion_builder_enqueue_live_scripts
     */
    public function enqueueBuilderStyles(): void
    {
        $iconForm = Helper::svg('assets/images/icons/fusion/fusion-form.svg', true);
        $iconReview = Helper::svg('assets/images/icons/fusion/fusion-review.svg', true);
        $iconReviews = Helper::svg('assets/images/icons/fusion/fusion-reviews.svg', true);
        $iconSummary = Helper::svg('assets/images/icons/fusion/fusion-summary.svg', true);
        $css = "
            [class*=\"fusion-glsr-\"]::before {
                background-color: currentColor;
                content: '.';
                display: block;
                mask-position: center;
                mask-size: 36px;
            }
            .fusion-glsr-form::before {
                -webkit-mask-image: url(\"{$iconForm}\");mask-image: url(\"{$iconForm}\");
                -webkit-mask-repeat: no-repeat;mask-repeat: no-repeat;
            }
            .fusion-glsr-review::before {
                -webkit-mask-image: url(\"{$iconReview}\");mask-image: url(\"{$iconReview}\");
                -webkit-mask-repeat: no-repeat;mask-repeat: no-repeat;
            }
            .fusion-glsr-reviews::before {
                -webkit-mask-image: url(\"{$iconReviews}\");mask-image: url(\"{$iconReviews}\");
                -webkit-mask-repeat: no-repeat;mask-repeat: no-repeat;
            }
            .fusion-glsr-summary::before {
                -webkit-mask-image: url(\"{$iconSummary}\");mask-image: url(\"{$iconSummary}\");
                -webkit-mask-repeat: no-repeat;mask-repeat: no-repeat;
            }
        ";
        wp_add_inline_style('fusion-builder-frame-builder-css', $css);
    }

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
        if (function_exists('fusion_builder_auto_activate_element')) {
            fusion_builder_auto_activate_element('site_review');
            fusion_builder_auto_activate_element('site_reviews');
            fusion_builder_auto_activate_element('site_reviews_form');
            fusion_builder_auto_activate_element('site_reviews_summary');
         }
    }

    /**
     * @action fusion_builder_before_init
     */
    public function registerFusionElements(): void
    {
        if (class_exists('Fusion_Element')) {
            FusionSiteReview::registerElement();
            FusionSiteReviews::registerElement();
            FusionSiteReviewsForm::registerElement();
            FusionSiteReviewsSummary::registerElement();
        }
    }
}
