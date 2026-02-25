<?php

namespace GeminiLabs\SiteReviews\Integrations\Flatsome;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database\ShortcodeOptionManager;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class Controller extends AbstractController
{
    /**
     * @filter site-reviews/modal_wrapped_by
     */
    public function filterModalWrappedBy(array $builders): array
    {
        $builders[] = 'flatsome';
        return $builders;
    }

    /**
     * @filter site-reviews/shortcode/wrap/attributes
     */
    public function filterWrapAttrClass(array $attributes, array $args, ShortcodeContract $shortcode): array
    {
        if ('flatsome' !== $shortcode->from) {
            return $attributes;
        }
        $classes = [$attributes['class'] ?? ''];
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
        if ('flatsome' !== $shortcode->from) {
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
                '--glsr-max-w' => 'style_max_width',
                '--glsr-summary-star-bg' => 'style_rating_color',
                '--glsr-bar-bg' => 'style_bar_color',
            ],
        ];
        if (!array_key_exists($shortcode->tag, $map)) {
            return $attributes;
        }
        $style = [
            $attributes['style'] ?? '',
        ];
        foreach ($map[$shortcode->tag] as $property => $styleKey) {
            $value = $args[$styleKey] ?? '';
            $style[] = "{$property}:{$value}"; // sanitization removes empty properties
        }
        $attributes['style'] = glsr(Sanitizer::class)->sanitizeAttrStyle(implode(';', $style));
        return $attributes;
    }

    /**
     * @action wp_ajax_ux_builder_get_posts:1
     */
    public function interceptGetPostsQuery(): void
    {
        $option = filter_input(INPUT_GET, 'option');
        if (!str_starts_with($option, glsr()->prefix)) {
            return;
        }
        $option = Str::removePrefix($option, glsr()->prefix);
        $postId = (string) filter_input(INPUT_GET, 'id');
        check_ajax_referer("ux-builder-{$postId}", 'security');
        $values = filter_input(INPUT_GET, 'values', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        if (!is_array($values)) {
            $values = [filter_input(INPUT_GET, 'values')];
        }
        $items = glsr(ShortcodeOptionManager::class)->get($option, [
            'include' => array_filter($values),
        ]);
        $items = array_filter($items, fn ($id) => in_array($id, $values), \ARRAY_FILTER_USE_KEY);
        $callback = fn ($id, $title) => compact('id', 'title');
        $results = array_map($callback, array_keys($items), array_values($items));
        wp_send_json_success($results);
    }

    /**
     * @action wp_ajax_ux_builder_search_posts:1
     */
    public function interceptSearchPostsQuery(): void
    {
        $option = filter_input(INPUT_GET, 'option');
        if (!str_starts_with($option, glsr()->prefix)) {
            return;
        }
        $option = Str::removePrefix($option, glsr()->prefix);
        $postId = (string) filter_input(INPUT_GET, 'id');
        check_ajax_referer("ux-builder-{$postId}", 'security');
        $query = filter_input(INPUT_GET, 'query');
        $items = glsr(ShortcodeOptionManager::class)->get($option, [
            'search' => $query,
        ]);
        $callback = fn ($id, $title) => compact('id', 'title');
        $results = array_map($callback, array_keys($items), array_values($items));
        wp_send_json_success($results);
    }

    /**
     * @action ux_builder_enqueue_scripts
     */
    public function printInlineScripts(string $page = ''): void
    {
        if ('editor' === $page) {
            $script = file_get_contents(glsr()->path('assets/scripts/integrations/flatsome-inline.js'));
            wp_add_inline_script('ux-builder-core', $script);
        }
    }

    /**
     * @action ux_builder_enqueue_scripts
     */
    public function printInlineStyles(string $page = ''): void
    {
        if ('editor' === $page) {
            $css = file_get_contents(glsr()->path('assets/styles/integrations/flatsome-inline.css'));
            wp_add_inline_style('ux-builder-core', $css);
        } else {
            // This fixes JS errors on mouseover events
            wp_add_inline_style('ux-builder-core', '.uxb-draggable>.glsr{pointer-events:none;}');
        }
    }

    /**
     * @action init
     */
    public function registerShortcodes(): void
    {
        glsr(Shortcodes\FlatsomeSiteReview::class)->register();
        glsr(Shortcodes\FlatsomeSiteReviews::class)->register();
        glsr(Shortcodes\FlatsomeSiteReviewsForm::class)->register();
        glsr(Shortcodes\FlatsomeSiteReviewsSummary::class)->register();
    }
}
