<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Helper;

class Controller extends AbstractController
{
    /**
     * Fix Star Rating control when review form is used inside an Elementor Pro Popup.
     *
     * @filter site-reviews/enqueue/public/inline-script/after
     */
    public function filterElementorPublicInlineScript(string $script): string
    {
        if (defined('ELEMENTOR_VERSION')) {
            $script .= 'function glsr_init_elementor(){GLSR.Event.trigger("site-reviews/init")}"undefined"!==typeof jQuery&&(';
            $script .= 'jQuery(window).on("elementor/frontend/init",function(){';
            $script .= 'elementorFrontend.elements.$window.on("elementor/popup/show",glsr_init_elementor);';
            $script .= 'elementorFrontend.hooks.addAction("frontend/element_ready/site_review.default",glsr_init_elementor);';
            $script .= 'elementorFrontend.hooks.addAction("frontend/element_ready/site_reviews.default",glsr_init_elementor);';
            $script .= 'elementorFrontend.hooks.addAction("frontend/element_ready/site_reviews_form.default",glsr_init_elementor);';
            $script .= '}));';
        }
        return $script;
    }

    /**
     * Fix Star Rating CSS class prefix in the Elementor editor.
     *
     * @filter site-reviews/defaults/star-rating/defaults
     */
    public function filterElementorStarRatingDefaults(array $defaults): array
    {
        if ('elementor' === filter_input(INPUT_GET, 'action')) {
            $defaults['prefix'] = 'glsr-';
        }
        return $defaults;
    }

    /**
     * @filter site-reviews/schema/generate
     */
    public function filterGeneratedSchema(array $schema): array
    {
        return empty($schema)
            ? glsr(SchemaParser::class)->generate()
            : $schema;
    }

    /**
     * @param \Elementor\Core\Files\CSS\Post $cssFile
     * @param \Elementor\Element_Base        $element
     *
     * @action elementor/element/parse_css
     */
    public function parseElementCss($cssFile, $element): void
    {
        $shortcode = $element->get_name();
        $shortcodes = [
            'site_review',
            'site_reviews',
            'site_reviews_form',
            'site_reviews_summary',
        ];
        if (!in_array($shortcode, $shortcodes)) {
            return;
        }
        $ratingColor = $element->get_settings('rating_color') ?: ($element->get_settings('__globals__')['rating_color'] ?? '');
        if (empty($ratingColor)) {
            return;
        }
        $selector = "{$cssFile->get_element_unique_selector($element)} .glsr:not([data-theme])";
        $stylesheet = $cssFile->get_stylesheet();
        $fn = fn ($variable) => [
            'mask-image' => $variable,
            'mask-size' => '100%',
        ];
        $stars = [
            'empty' => 'var(--glsr-star-empty)',
            'error' => 'var(--glsr-star-error)',
            'full' => 'var(--glsr-star-full)',
            'half' => 'var(--glsr-star-half)',
        ];
        if (in_array($shortcode, ['site_review', 'site_reviews'])) {
            $stylesheet->add_rules("{$selector} .glsr-review .glsr-star-empty", $fn($stars['empty']));
            $stylesheet->add_rules("{$selector} .glsr-review .glsr-star-full", $fn($stars['full']));
            $stylesheet->add_rules("{$selector} .glsr-review .glsr-star-half", $fn($stars['half']));
        } elseif ('site_reviews_form' === $shortcode) {
            $stylesheet->add_rules("{$selector} .glsr-field:not(.glsr-field-is-invalid) .glsr-star-rating--stars > span", $fn($stars['empty']));
            $stylesheet->add_rules("{$selector} .glsr-field:not(.glsr-field-is-invalid) .glsr-star-rating--stars > span:is(.gl-active,.gl-selected)", $fn($stars['full']));
            $stylesheet->add_rules("{$selector} .glsr-field-is-invalid .glsr-star-rating--stars > span.gl-active", $fn($stars['error']));
        } elseif ('site_reviews_summary' === $shortcode) {
            $stylesheet->add_rules("{$selector} .glsr-star-empty", $fn($stars['empty']));
            $stylesheet->add_rules("{$selector} .glsr-star-full", $fn($stars['full']));
            $stylesheet->add_rules("{$selector} .glsr-star-half", $fn($stars['half']));
        }
    }

    /**
     * @param $manager \Elementor\Elements_Manager
     *
     * @action elementor/elements/categories_registered
     */
    public function registerElementorCategory($manager): void
    {
        $manager->add_category(glsr()->id, [
            'title' => glsr()->name,
            'icon' => 'eicon-star-o', // default icon
        ]);
    }

    /**
     * @param $manager \Elementor\Widgets_Manager
     *
     * @action elementor/widgets/register
     */
    public function registerElementorWidgets($manager): void
    {
        $manager->register(new ElementorFormWidget());
        $manager->register(new ElementorReviewsWidget());
        $manager->register(new ElementorReviewWidget());
        $manager->register(new ElementorSummaryWidget());
    }

    /**
     * @action elementor/editor/after_enqueue_styles
     * @action elementor/preview/enqueue_styles
     */
    public function registerInlineStyles(): void
    {
        $iconForm = Helper::svg('assets/images/icons/elementor/elementor-form.svg', true);
        $iconReview = Helper::svg('assets/images/icons/elementor/elementor-review.svg', true);
        $iconReviews = Helper::svg('assets/images/icons/elementor/elementor-reviews.svg', true);
        $iconSummary = Helper::svg('assets/images/icons/elementor/elementor-summary.svg', true);
        $css = "
            [class*=\"eicon-glsr-\"]::before {
                background-color: currentColor;
                content: '.';
                display: block;
                width: 1em;
            }
            .eicon-glsr-form::before {
                -webkit-mask-image: url(\"{$iconForm}\");mask-image: url(\"{$iconForm}\");
                -webkit-mask-repeat: no-repeat;mask-repeat: no-repeat;
            }
            .eicon-glsr-review::before {
                -webkit-mask-image: url(\"{$iconReview}\");mask-image: url(\"{$iconReview}\");
                -webkit-mask-repeat: no-repeat;mask-repeat: no-repeat;
            }
            .eicon-glsr-reviews::before {
                -webkit-mask-image: url(\"{$iconReviews}\");mask-image: url(\"{$iconReviews}\");
                -webkit-mask-repeat: no-repeat;mask-repeat: no-repeat;
            }
            .eicon-glsr-summary::before {
                -webkit-mask-image: url(\"{$iconSummary}\");mask-image: url(\"{$iconSummary}\");
                -webkit-mask-repeat: no-repeat;mask-repeat: no-repeat;
            }
        ";
        wp_add_inline_style('elementor-editor', $css);
        wp_add_inline_style('elementor-frontend', $css."
            [class*=\"eicon-glsr-\"]::before {
                font-size: 28px;
                margin: 0 auto;
            }
        ");
    }

    /**
     * @action elementor/editor/after_enqueue_scripts
     */
    public function registerScripts(): void
    {
        wp_enqueue_script(
            glsr()->id.'/elementor',
            glsr()->url('assets/scripts/elementor-editor.js'),
            [],
            glsr()->version,
            ['strategy' => 'defer']
        );
    }
}
