<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database\ShortcodeOptionManager;
use GeminiLabs\SiteReviews\Helpers\Svg;
use GeminiLabs\SiteReviews\Integrations\Elementor\Controls\MultiSwitcher;
use GeminiLabs\SiteReviews\Integrations\Elementor\Controls\Select2Ajax;
use GeminiLabs\SiteReviews\Integrations\Elementor\Defaults\QueryAjaxDefaults;
use GeminiLabs\SiteReviews\Integrations\Elementor\Widgets\ElementorSiteReview;
use GeminiLabs\SiteReviews\Integrations\Elementor\Widgets\ElementorSiteReviews;
use GeminiLabs\SiteReviews\Integrations\Elementor\Widgets\ElementorSiteReviewsForm;
use GeminiLabs\SiteReviews\Integrations\Elementor\Widgets\ElementorSiteReviewsSummary;

class Controller extends AbstractController
{
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
     * Fix Star Rating control when review form is used inside an Elementor Pro Popup.
     *
     * @filter site-reviews/enqueue/public/inline-script/after
     */
    public function filterPublicInlineScript(string $script): string
    {
        if (!defined('ELEMENTOR_VERSION')) {
            return $script;
        }
        $inlineScript = (string) file_get_contents(glsr()->path('assets/scripts/integrations/elementor-frontend.js'));
        return $script.$inlineScript;
    }

    /**
     * Fix Star Rating CSS class prefix in the Elementor editor.
     *
     * @filter site-reviews/defaults/star-rating/defaults
     */
    public function filterStarRatingDefaults(array $defaults): array
    {
        if ('elementor' === filter_input(INPUT_GET, 'action')) {
            $defaults['prefix'] = 'glsr-';
        }
        return $defaults;
    }

    /**
     * Elementor overwrites all selector values in a color control with the
     * global color variable when a global variable is being used.
     * 
     * @param \Elementor\Core\Files\CSS\Post $cssFile
     * @param \Elementor\Element_Base        $element
     *
     * @action elementor/element/parse_css
     */
    public function parseElementCss($cssFile, $element): void
    {
        $shortcode = $element->get_name();
        if (!str_starts_with($shortcode, 'site_review')) {
            return;
        }
        $color = $element->get_settings('style_rating_color') ?: ($element->get_settings('__globals__')['style_rating_color'] ?? '');
        if (empty($color)) {
            return;
        }
        $stylesheet = $cssFile->get_stylesheet();
        $wrapper = "{$cssFile->get_element_unique_selector($element)} .glsr:not([data-theme])";
        switch ($shortcode) {
            case 'site_review':
            case 'site_reviews':
                $stylesheet->add_rules(
                    "{$wrapper} .glsr-star-empty",
                    'background: var(--glsr-review-star-bg); mask-image: var(--glsr-star-empty); mask-size: 100%;'
                );
                $stylesheet->add_rules(
                    "{$wrapper} .glsr-star-full",
                    'background: var(--glsr-review-star-bg); mask-image: var(--glsr-star-full); mask-size: 100%;'
                );
                $stylesheet->add_rules(
                    "{$wrapper} .glsr-star-half",
                    'background: var(--glsr-review-star-bg); mask-image: var(--glsr-star-half); mask-size: 100%;'
                );
                break;
            case 'site_reviews_summary':
                $stylesheet->add_rules(
                    "{$wrapper} .glsr-star-empty",
                    'background: var(--glsr-summary-star-bg); mask-image: var(--glsr-star-empty); mask-size: 100%;'
                );
                $stylesheet->add_rules(
                    "{$wrapper} .glsr-star-full",
                    'background: var(--glsr-summary-star-bg); mask-image: var(--glsr-star-full); mask-size: 100%;'
                );
                $stylesheet->add_rules(
                    "{$wrapper} .glsr-star-half",
                    'background: var(--glsr-summary-star-bg); mask-image: var(--glsr-star-half); mask-size: 100%;'
                );
                break;
            case 'site_reviews_form':
                $stylesheet->add_rules(
                    "{$wrapper} .glsr-field:not(.glsr-field-is-invalid) .glsr-star-rating--stars > span",
                    'background: var(--glsr-form-star-bg); mask-image: var(--glsr-star-empty); mask-size: 100%;'
                );
                $stylesheet->add_rules(
                    "{$wrapper} .glsr-field:not(.glsr-field-is-invalid) .glsr-star-rating--stars > span:is(.gl-active,.gl-selected)",
                    'mask-image: var(--glsr-star-full);'
                );
                break;
        }
    }

    /**
     * @see static::registerAjaxActions()
     */
    public function queryAjaxControlOptions($data): array
    {
        $args = glsr(QueryAjaxDefaults::class)->restrict($data);
        $options = call_user_func([glsr(ShortcodeOptionManager::class), $args['option']], $args);
        if (!is_array($options)) {
            return [];
        }
        $callback = fn ($id, $text) => compact('id', 'text');
        $results = array_map($callback, array_keys($options), array_values($options));
        return compact('results');
    }

    /**
     * @see static::registerAjaxActions()
     */
    public function queryAjaxControlSelected($data): array
    {
        $args = glsr(QueryAjaxDefaults::class)->restrict($data);
        $results = call_user_func([glsr(ShortcodeOptionManager::class), $args['option']], $args);
        if (!is_array($results)) {
            return [];
        }
        if (!array_key_exists($args['include'], $results)) {
            return [];
        }
        return [
            $args['include'] => $results[$args['include']],
        ];
    }

    /**
     * @param \Elementor\Core\Common\Modules\Ajax\Module $manager
     *
     * @action elementor/ajax/register_actions
     */
    public function registerAjaxActions($manager): void
    {
        $manager->register_ajax_action(glsr()->prefix.'elementor_ajax_query', [$this, 'queryAjaxControlOptions']);
        $manager->register_ajax_action(glsr()->prefix.'elementor_ajax_query_selected', [$this, 'queryAjaxControlSelected']);
    }

    /**
     * @param \Elementor\Elements_Manager $manager
     *
     * @action elementor/elements/categories_registered
     */
    public function registerCategory($manager): void
    {
        $manager->add_category(glsr()->id, [
            'hideIfEmpty' => true,
            'title' => glsr()->name,
        ]);
    }

    /**
     * @param \Elementor\Controls_Manager $manager
     *
     * @action elementor/controls/register
     */
    public function registerControls($manager): void
    {
        $manager->register(new MultiSwitcher());
        $manager->register(new Select2Ajax());
    }

    /**
     * @action admin_enqueue_scripts
     * @action elementor/editor/after_enqueue_styles
     * @action elementor/preview/enqueue_styles
     */
    public function registerInlineStyles(): void
    {
        $iconForm = Svg::encoded('assets/images/icons/elementor/icon-form.svg');
        $iconReview = Svg::encoded('assets/images/icons/elementor/icon-review.svg');
        $iconReviews = Svg::encoded('assets/images/icons/elementor/icon-reviews.svg');
        $iconSummary = Svg::encoded('assets/images/icons/elementor/icon-summary.svg');
        $css = "
            [class*=\"eicon-glsr-\"]::before {
                background-color: currentColor;
                content: '.';
                display: block;
                mask-repeat: no-repeat;
                mask-size: contain;
                width: 1em;
            }
            .eicon-glsr-form::before {
                mask-image: url(\"{$iconForm}\");
            }
            .eicon-glsr-review::before {
                mask-image: url(\"{$iconReview}\");
            }
            .eicon-glsr-reviews::before {
                mask-image: url(\"{$iconReviews}\");
            }
            .eicon-glsr-summary::before {
                mask-image: url(\"{$iconSummary}\");
            }
            .elementor-nerd-box-icon[src$=\"assets/images/premium.svg\"] {
                width: 240px;
            }
        ";
        $css = preg_replace('/\s+/', ' ', $css);
        wp_add_inline_style('elementor-admin', $css);
        wp_add_inline_style('elementor-editor', $css);
        wp_add_inline_style('elementor-frontend',
            $css."[class*=\"eicon-glsr-\"]::before{font-size:28px;margin:0 auto}"
        );
    }

    /**
     * @action elementor/editor/after_enqueue_scripts
     */
    public function registerScripts(): void
    {
        wp_register_script(
            glsr()->id.'/elementor-editor',
            glsr()->url('assets/scripts/integrations/elementor-editor.js'),
            [],
            glsr()->version,
            ['strategy' => 'defer']
        );
        wp_enqueue_script(glsr()->id.'/elementor-editor');
    }

    /**
     * @param \Elementor\Widgets_Manager $manager
     *
     * @action elementor/widgets/register
     */
    public function registerWidgets($manager): void
    {
        $manager->register(new ElementorSiteReview());
        $manager->register(new ElementorSiteReviews());
        $manager->register(new ElementorSiteReviewsForm());
        $manager->register(new ElementorSiteReviewsSummary());
    }
}
