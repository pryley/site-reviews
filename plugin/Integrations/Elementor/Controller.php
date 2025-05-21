<?php

namespace GeminiLabs\SiteReviews\Integrations\Elementor;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database\ShortcodeOptionManager;
use GeminiLabs\SiteReviews\Helpers\Svg;
use GeminiLabs\SiteReviews\Integrations\Elementor\Controls\MultiSwitcher;
use GeminiLabs\SiteReviews\Integrations\Elementor\Controls\Select2Ajax;
use GeminiLabs\SiteReviews\Integrations\Elementor\Defaults\QueryAjaxDefaults;

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
            'title' => glsr()->name,
            'icon' => 'eicon-star-o', // default icon
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
        ";
        wp_add_inline_style('elementor-admin', $css);
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
            glsr()->id.'/elementor-editor',
            glsr()->url('assets/scripts/integrations/elementor-editor.js'),
            [],
            glsr()->version,
            ['strategy' => 'defer']
        );
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
