<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

use GeminiLabs\SiteReviews\Controllers\AbstractController;

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
        $css = "
            .eicon-glsr-review::before,.eicon-glsr-reviews::before {
                background-color: currentColor;
                content: '.';
                display: block;
                width: 1em;
            }
            .eicon-glsr-review::before {
                -webkit-mask-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M4.327 27.994a.36.36 0 0 1-.293-.07.54.54 0 0 1-.181-.266.52.52 0 0 1 0-.336l2.596-7.054H4.132a4 4 0 0 1-2.066-.56c-.633-.373-1.135-.878-1.508-1.512A4.02 4.02 0 0 1 0 16.124V4.143a4.02 4.02 0 0 1 .558-2.072A4.19 4.19 0 0 1 2.066.56C2.699.187 3.387 0 4.132 0h19.737a4 4 0 0 1 2.066.56c.633.373 1.135.877 1.507 1.512A4.02 4.02 0 0 1 28 4.143v12.065a4.1 4.1 0 0 1-.558 2.086 4.16 4.16 0 0 1-1.507 1.526c-.632.374-1.321.56-2.066.56h-9.715l-9.519 7.614h-.307zM4.132.952a3.26 3.26 0 0 0-1.577.448A3.22 3.22 0 0 0 1.41 2.548a3.06 3.06 0 0 0-.433 1.595v12.065c0 .579.144 1.11.433 1.596s.67.868 1.145 1.148a3.26 3.26 0 0 0 1.577.448H7.23a.51.51 0 0 1 .363.224.63.63 0 0 1 .112.448l-2.317 6.103 8.291-6.663c.074-.075.167-.112.279-.112h9.91c.577 0 1.107-.145 1.591-.434a3.3 3.3 0 0 0 1.159-1.162 3.06 3.06 0 0 0 .433-1.596V4.059c0-.579-.144-1.11-.433-1.596a3.3 3.3 0 0 0-1.159-1.162c-.484-.29-1.014-.434-1.591-.434L4.132.952zm7.432 7.239l-.504-.84L14.252 5h.868v8.118l-.028.644-.028.224 1.736-.056v1.092h-5.404v-.924c.093 0 .924-.019 1.092-.056s.285-.089.35-.154.126-.182.182-.35.084-.392.084-.672V7.267l-1.54.924z'/%3E%3C/svg%3E\");
                        mask-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M4.327 27.994a.36.36 0 0 1-.293-.07.54.54 0 0 1-.181-.266.52.52 0 0 1 0-.336l2.596-7.054H4.132a4 4 0 0 1-2.066-.56c-.633-.373-1.135-.878-1.508-1.512A4.02 4.02 0 0 1 0 16.124V4.143a4.02 4.02 0 0 1 .558-2.072A4.19 4.19 0 0 1 2.066.56C2.699.187 3.387 0 4.132 0h19.737a4 4 0 0 1 2.066.56c.633.373 1.135.877 1.507 1.512A4.02 4.02 0 0 1 28 4.143v12.065a4.1 4.1 0 0 1-.558 2.086 4.16 4.16 0 0 1-1.507 1.526c-.632.374-1.321.56-2.066.56h-9.715l-9.519 7.614h-.307zM4.132.952a3.26 3.26 0 0 0-1.577.448A3.22 3.22 0 0 0 1.41 2.548a3.06 3.06 0 0 0-.433 1.595v12.065c0 .579.144 1.11.433 1.596s.67.868 1.145 1.148a3.26 3.26 0 0 0 1.577.448H7.23a.51.51 0 0 1 .363.224.63.63 0 0 1 .112.448l-2.317 6.103 8.291-6.663c.074-.075.167-.112.279-.112h9.91c.577 0 1.107-.145 1.591-.434a3.3 3.3 0 0 0 1.159-1.162 3.06 3.06 0 0 0 .433-1.596V4.059c0-.579-.144-1.11-.433-1.596a3.3 3.3 0 0 0-1.159-1.162c-.484-.29-1.014-.434-1.591-.434L4.132.952zm7.432 7.239l-.504-.84L14.252 5h.868v8.118l-.028.644-.028.224 1.736-.056v1.092h-5.404v-.924c.093 0 .924-.019 1.092-.056s.285-.089.35-.154.126-.182.182-.35.084-.392.084-.672V7.267l-1.54.924z'/%3E%3C/svg%3E\");
                -webkit-mask-repeat: no-repeat;
                        mask-repeat: no-repeat;
            }
            .eicon-glsr-reviews::before {
                -webkit-mask-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M4.327 27.994a.36.36 0 0 1-.293-.07c-.084-.065-.144-.154-.181-.266a.52.52 0 0 1 0-.336l2.596-7.054H4.132c-.744 0-1.433-.187-2.066-.56S.931 18.83.558 18.196A4.02 4.02 0 0 1 0 16.124V4.143a4.02 4.02 0 0 1 .558-2.072C.931 1.437 1.433.933 2.066.56S3.387 0 4.132 0h19.737c.744 0 1.433.187 2.066.56s1.135.877 1.507 1.512A4.02 4.02 0 0 1 28 4.143v12.065c0 .747-.186 1.442-.558 2.086s-.875 1.152-1.507 1.526-1.321.56-2.066.56h-9.715l-9.519 7.614h-.307zM4.132.952a3.26 3.26 0 0 0-1.577.448c-.475.28-.856.663-1.145 1.148S.977 3.565.977 4.143v12.065c0 .579.144 1.11.433 1.596s.67.868 1.145 1.148a3.26 3.26 0 0 0 1.577.448H7.23a.51.51 0 0 1 .363.224c.093.131.13.28.112.448l-2.317 6.103 8.291-6.663c.074-.075.167-.112.279-.112h9.91c.577 0 1.107-.145 1.591-.434s.87-.677 1.159-1.162.433-1.017.433-1.596V4.059c0-.579-.144-1.11-.433-1.596s-.675-.872-1.159-1.162-1.014-.434-1.591-.434L4.132.952z' fill-rule='nonzero'/%3E%3Cpath d='M17.952 16.612c-.056 0-.14-.028-.252-.084L14 14.068l-3.7 2.459a.39.39 0 0 1-.252.084.39.39 0 0 1-.252-.084.52.52 0 0 1-.158-.21c-.035-.084-.039-.175-.011-.273l1.24-4.52-3.7-2.901a.44.44 0 0 1-.147-.221.42.42 0 0 1 .158-.473c.077-.056.165-.084.263-.084h4.52l1.64-4.099c.042-.112.119-.186.231-.221a.55.55 0 0 1 .336 0c.112.035.189.109.231.221l1.64 4.099h4.541c.084 0 .165.028.242.084a.42.42 0 0 1 .158.473.44.44 0 0 1-.147.221l-3.7 2.901 1.24 4.52c.028.098.025.189-.011.273a.52.52 0 0 1-.158.21c-.112.056-.196.084-.252.084zM14 13.101a.56.56 0 0 1 .252.063l2.964 1.997-1.009-3.7c-.014-.084-.011-.168.011-.252s.067-.154.137-.21l2.943-2.27h-3.553c-.084 0-.165-.028-.242-.084a.44.44 0 0 1-.158-.189L14 5.092l-1.345 3.342a.42.42 0 0 1-.158.21c-.077.056-.158.084-.242.084H8.703l2.943 2.27c.07.056.116.126.137.21s.025.168.011.252l-1.009 3.7 2.964-1.997a.56.56 0 0 1 .252-.063z'/%3E%3C/svg%3E\");
                        mask-image: url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M4.327 27.994a.36.36 0 0 1-.293-.07c-.084-.065-.144-.154-.181-.266a.52.52 0 0 1 0-.336l2.596-7.054H4.132c-.744 0-1.433-.187-2.066-.56S.931 18.83.558 18.196A4.02 4.02 0 0 1 0 16.124V4.143a4.02 4.02 0 0 1 .558-2.072C.931 1.437 1.433.933 2.066.56S3.387 0 4.132 0h19.737c.744 0 1.433.187 2.066.56s1.135.877 1.507 1.512A4.02 4.02 0 0 1 28 4.143v12.065c0 .747-.186 1.442-.558 2.086s-.875 1.152-1.507 1.526-1.321.56-2.066.56h-9.715l-9.519 7.614h-.307zM4.132.952a3.26 3.26 0 0 0-1.577.448c-.475.28-.856.663-1.145 1.148S.977 3.565.977 4.143v12.065c0 .579.144 1.11.433 1.596s.67.868 1.145 1.148a3.26 3.26 0 0 0 1.577.448H7.23a.51.51 0 0 1 .363.224c.093.131.13.28.112.448l-2.317 6.103 8.291-6.663c.074-.075.167-.112.279-.112h9.91c.577 0 1.107-.145 1.591-.434s.87-.677 1.159-1.162.433-1.017.433-1.596V4.059c0-.579-.144-1.11-.433-1.596s-.675-.872-1.159-1.162-1.014-.434-1.591-.434L4.132.952z' fill-rule='nonzero'/%3E%3Cpath d='M17.952 16.612c-.056 0-.14-.028-.252-.084L14 14.068l-3.7 2.459a.39.39 0 0 1-.252.084.39.39 0 0 1-.252-.084.52.52 0 0 1-.158-.21c-.035-.084-.039-.175-.011-.273l1.24-4.52-3.7-2.901a.44.44 0 0 1-.147-.221.42.42 0 0 1 .158-.473c.077-.056.165-.084.263-.084h4.52l1.64-4.099c.042-.112.119-.186.231-.221a.55.55 0 0 1 .336 0c.112.035.189.109.231.221l1.64 4.099h4.541c.084 0 .165.028.242.084a.42.42 0 0 1 .158.473.44.44 0 0 1-.147.221l-3.7 2.901 1.24 4.52c.028.098.025.189-.011.273a.52.52 0 0 1-.158.21c-.112.056-.196.084-.252.084zM14 13.101a.56.56 0 0 1 .252.063l2.964 1.997-1.009-3.7c-.014-.084-.011-.168.011-.252s.067-.154.137-.21l2.943-2.27h-3.553c-.084 0-.165-.028-.242-.084a.44.44 0 0 1-.158-.189L14 5.092l-1.345 3.342a.42.42 0 0 1-.158.21c-.077.056-.158.084-.242.084H8.703l2.943 2.27c.07.056.116.126.137.21s.025.168.011.252l-1.009 3.7 2.964-1.997a.56.56 0 0 1 .252-.063z'/%3E%3C/svg%3E\");
                -webkit-mask-repeat: no-repeat;
                        mask-repeat: no-repeat;
            }
        ";
        wp_add_inline_style('elementor-editor', $css);
        wp_add_inline_style('elementor-frontend', $css."
            .eicon-glsr-review::before,
            .eicon-glsr-reviews::before {
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
            glsr()->url('assets/elementor-editor.js'),
            [],
            glsr()->version,
            ['strategy' => 'defer']
        );
    }
}
