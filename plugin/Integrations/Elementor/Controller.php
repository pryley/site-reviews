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
     * @filter site-reviews/modal_wrapped_by
     */
    public function filterModalWrappedBy(array $builders): array
    {
        $builders[] = 'elementor';
        return $builders;
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
     * @param \Elementor\Widget_Base $widget
     * 
     * @action elementor/element/after_add_attributes
     */
    public function modifyAttributes($widget): void
    {
        if (!$widget instanceof ElementorWidget) {
            return;
        }
        $settings = $widget->get_settings_for_display();
        $hasColor = !empty($settings['style_rating_color']) || !empty($settings['__globals__']['style_rating_color']);
        $hasTheme = !empty($settings['theme']);
        if ($hasColor && !$hasTheme) {
            $widget->add_render_attribute('_wrapper', 'class', 'has-custom-color');
        }
    }

    /**
     * @see static::registerAjaxActions()
     */
    public function queryAjaxControlOptions($data): array
    {
        $args = glsr(QueryAjaxDefaults::class)->restrict($data);
        $options = glsr(ShortcodeOptionManager::class)->get($args['option'], $args);
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
        $options = glsr(ShortcodeOptionManager::class)->get($args['option'], $args);
        if (!is_array($options)) {
            return [];
        }
        if (!array_key_exists($args['include'], $options)) {
            return [];
        }
        return [
            $args['include'] => $options[$args['include']],
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
        $icons = [
            'eicon-glsr-form' => Svg::encoded('assets/images/icons/elementor/icon-form.svg'),
            'eicon-glsr-review' => Svg::encoded('assets/images/icons/elementor/icon-review.svg'),
            'eicon-glsr-reviews' => Svg::encoded('assets/images/icons/elementor/icon-reviews.svg'),
            'eicon-glsr-summary' => Svg::encoded('assets/images/icons/elementor/icon-summary.svg'),
        ];
        $maskRules = '';
        foreach ($icons as $class => $url) {
            $maskRules .= "i.{$class}::before { mask-image: url(\"{$url}\"); }";
        }
        $css = <<<CSS
            i[class^="eicon-glsr-"]::before {
                background-color: currentColor;
                content: '.';
                display: block;
                mask-repeat: no-repeat;
                mask-size: contain;
                width: 1em;
            }
            {$maskRules}
            .elementor-nerd-box-icon[src$="assets/images/premium.svg"] {
                width: 240px;
            }
        CSS;
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
