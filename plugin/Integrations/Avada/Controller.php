<?php

namespace GeminiLabs\SiteReviews\Integrations\Avada;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database\ShortcodeOptionManager;
use GeminiLabs\SiteReviews\Helpers\Svg;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class Controller extends AbstractController
{
    /**
     * @action fusion_builder_admin_scripts_hook
     * @action fusion_builder_enqueue_live_scripts
     */
    public function enqueueBuilderStyles(): void
    {
        $inlineFile = glsr()->path('assets/styles/integrations/fusion-inline.css');
        if (!file_exists($inlineFile)) {
            glsr_log()->error("Inline stylesheet is missing: {$inlineFile}");
            return;
        }
        $icons = [
            ':icon-0' => Svg::encoded('assets/images/icon-static.svg'),
            ':icon-1' => Svg::encoded('assets/images/icons/fusion/icon-form.svg'),
            ':icon-2' => Svg::encoded('assets/images/icons/fusion/icon-review.svg'),
            ':icon-3' => Svg::encoded('assets/images/icons/fusion/icon-reviews.svg'),
            ':icon-4' => Svg::encoded('assets/images/icons/fusion/icon-summary.svg'),
        ];
        $css = str_replace(
            array_keys($icons),
            array_values($icons),
            file_get_contents($inlineFile)
        );
        wp_add_inline_style('fusion_builder_css', $css);
        wp_add_inline_style('fusion-builder-frame-builder-css', $css);
    }

    /**
     * @filter fusion_builder_plugin_elements
     */
    public function filterBuilderPluginElements(array $elements): array
    {
        global $all_fusion_builder_elements;
        if (!is_array($all_fusion_builder_elements)) {
            return $elements;
        }
        $icon = Svg::get('assets/images/icon-static.svg', [
            'height' => 20,
            'style' => 'position:absolute;',
            'width' => 20,
        ]);
        foreach ($all_fusion_builder_elements as $key => $element) {
            if (!str_starts_with($key, 'site_review')) {
                continue;
            }
            if (empty($element['name'])) {
                continue;
            }
            $element['name'] = "{$icon} <span style=\"margin-left:20px;\">{$element['name']}</span>";
            $elements[$key] = $element;
        }
        return $elements;
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
     * @filter site-reviews/modal_wrapped_by
     */
    public function filterModalWrappedBy(array $builders): array
    {
        $builders[] = 'avada';
        return $builders;
    }

    /**
     * @filter site-reviews/enqueue/public/inline-script/after
     */
    public function filterPublicInlineScript(string $script): string
    {
        $inlineFile = glsr()->path('assets/scripts/integrations/fusion-inline.js');
        if (!file_exists($inlineFile)) {
            glsr_log()->error("Inline javascript is missing: {$inlineFile}");
            return $script;
        }
        return $script.file_get_contents($inlineFile);
    }

    /**
     * @filter site-reviews/register/widgets
     */
    public function filterRegisterWidgets(bool $bool): bool
    {
        if (function_exists('is_fusion_editor') && !is_fusion_editor()) {
            return false;
        }
        return $bool;
    }

    /**
     * @filter site-reviews/shortcode/wrap/attributes
     */
    public function filterWrapAttrClass(array $attributes, array $args, ShortcodeContract $shortcode): array
    {
        if ('avada' !== $shortcode->from) {
            return $attributes;
        }
        $classes = [
            $attributes['class'] ?? '',
        ];
        if (!empty($args['style_rating_color'])) {
            $classes[] = 'has-custom-color';
        }
        $attributes['class'] = glsr(Sanitizer::class)->sanitizeAttrClass(implode(' ', $classes));
        return $attributes;
    }

    /**
     * @filter site-reviews/shortcode/wrap/attributes
     */
    public function filterWrapAttrStyle(array $attributes, array $args, ShortcodeContract $shortcode): array
    {
        if ('avada' !== $shortcode->from) {
            return $attributes;
        }
        $map = [
            'site_review' => [
                '--glsr-review-star-bg' => 'style_rating_color',
            ],
            'site_reviews' => [
                '--glsr-review-star-bg' => 'style_rating_color',
            ],
            'site_reviews_form' => [
                '--glsr-form-star-bg' => 'style_rating_color',
            ],
            'site_reviews_summary' => [
                '--glsr-summary-star-bg' => 'style_rating_color',
                '--glsr-bar-bg' => 'style_bar_color',
            ],
        ];
        $vars = $map[$shortcode->tag] ?? null;
        if (!$vars) {
            return $attributes;
        }
        $style = [
            $attributes['style'] ?? '',
        ];
        foreach ($vars as $cssVar => $argKey) {
            $value = $args[$argKey] ?? '';
            $style[] = "{$cssVar}:{$value}"; // sanitization removes empty properties
        }
        $attributes['style'] = glsr(Sanitizer::class)->sanitizeAttrStyle(implode(';', $style));
        return $attributes;
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
     * @action fusion_builder_shortcodes_init
     */
    public function registerElements(): void
    {
        Elements\FusionSiteReview::registerElement();
        Elements\FusionSiteReviews::registerElement();
        Elements\FusionSiteReviewsForm::registerElement();
        Elements\FusionSiteReviewsSummary::registerElement();
    }

    /**
     * @action wp_ajax_glsr_fusion_search_query
     */
    public function runSearchQuery(): void
    {
        $reqMethod = 'POST' === filter_input(INPUT_SERVER, 'REQUEST_METHOD') ? \INPUT_POST : \INPUT_GET;
        if (filter_input($reqMethod, 'fusion_load_nonce')) {
            check_ajax_referer('fusion_load_nonce', 'fusion_load_nonce');
        } else {
            check_ajax_referer('fusion-page-options-nonce', 'fusion_po_nonce');
        }
        $data = array_fill_keys(['labels', 'results'], []);
        $params = filter_input($reqMethod, 'params', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $labels = filter_input($reqMethod, 'labels', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $search = filter_input($reqMethod, 'search');
        $option = $params['option'] ?? '';
        if (!is_null($search)) {
            $params['search'] = $search;
            $items = glsr(ShortcodeOptionManager::class)->get($option, $params);
            $callback = fn ($id, $text) => compact('id', 'text');
            $data['results'] = array_map($callback, array_keys($items), array_values($items));
        } elseif (!is_null($labels)) {
            $params['include'] = $labels;
            $items = glsr(ShortcodeOptionManager::class)->get($option, $params);
            $items = array_filter($items, fn ($id) => in_array($id, $labels), \ARRAY_FILTER_USE_KEY);
            $callback = fn ($id, $text) => compact('id', 'text');
            $data['labels'] = array_map($callback, array_keys($items), array_values($items));
        }
        echo wp_json_encode($data);
        wp_die();
    }
}
