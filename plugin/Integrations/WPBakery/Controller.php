<?php

namespace GeminiLabs\SiteReviews\Integrations\WPBakery;

use GeminiLabs\SiteReviews\Contracts\ShortcodeContract;
use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database\ShortcodeOptionManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Sanitizer;

class Controller extends AbstractController
{
    /**
     * @action vc_frontend_editor_enqueue_js_css
     */
    public function enqueueInlineScripts(): void
    {
        $script = file_get_contents(glsr()->path('assets/scripts/integrations/wpbakery-inline.js'));
        wp_add_inline_script('vc-frontend-editor-min-js', $script);
    }

    /**
     * @action vc_backend_editor_enqueue_js_css
     * @action vc_frontend_editor_enqueue_js_css
     */
    public function enqueueInlineStyles(): void
    {
        $css = file_get_contents(glsr()->path('assets/styles/integrations/wpbakery-inline.css'));
        $keys = [
            'site_review' => 'review',
            'site_reviews' => 'reviews',
            'site_reviews_form' => 'form',
            'site_reviews_summary' => 'summary',
        ];
        foreach ($keys as $shortcode => $icon) {
            $css .= sprintf('#%s .vc_element-icon{background-image:url(%s);}',
                $shortcode,
                glsr()->url("assets/images/icons/wpbakery/icon-{$icon}.svg")
            );
        }
        wp_add_inline_style('js_composer', $css);
        wp_add_inline_style('vc_inline_css', $css);
    }

    /**
     * @filter vc_autocomplete_site_reviews_assigned_posts_callback
     * @filter vc_autocomplete_site_reviews_field_assigned_posts_callback
     * @filter vc_autocomplete_site_reviews_form_assigned_posts_callback
     * @filter vc_autocomplete_site_reviews_images_assigned_posts_callback
     * @filter vc_autocomplete_site_reviews_summary_assigned_posts_callback
     */
    public function filterAssignedPostsCallback(string $query, string $shortcodeTag): array
    {
        $args = is_numeric($query) ? ['include' => $query] : ['search' => $query];
        $options = glsr(ShortcodeOptionManager::class)->assigned_posts(wp_parse_args($args, [
            'shortcode' => stripslashes($shortcodeTag),
        ]));
        return array_map(
            fn ($value, $label) => [
                'label' => is_numeric($value) ? sprintf('ID: %s - %s', $value, $label) : $label,
                'value' => $value,
            ],
            array_keys($options),
            array_values($options)
        );
    }

    /**
     * @filter vc_autocomplete_site_reviews_assigned_posts_render
     * @filter vc_autocomplete_site_reviews_field_assigned_posts_render
     * @filter vc_autocomplete_site_reviews_form_assigned_posts_render
     * @filter vc_autocomplete_site_reviews_images_assigned_posts_render
     * @filter vc_autocomplete_site_reviews_summary_assigned_posts_render
     */
    public function filterAssignedPostsRender(array $query, array $settings, string $shortcodeTag): ?array
    {
        $value = $query['value'] ?? '';
        if (empty($value)) {
            return null;
        }
        $options = glsr(ShortcodeOptionManager::class)->assigned_posts([
            'per_page' => 1,
            'include' => $value,
            'shortcode' => stripslashes($shortcodeTag),
        ]);
        if (!isset($options[$value])) {
            return $query;
        }
        return [
            'label' => is_numeric($value) ? sprintf('ID: %s - %s', $value, $options[$value]) : $options[$value],
            'value' => $value,
        ];
    }

    /**
     * @filter vc_autocomplete_site_reviews_assigned_term_callback
     * @filter vc_autocomplete_site_reviews_field_assigned_term_callback
     * @filter vc_autocomplete_site_reviews_form_assigned_term_callback
     * @filter vc_autocomplete_site_reviews_images_assigned_term_callback
     * @filter vc_autocomplete_site_reviews_summary_assigned_term_callback
     */
    public function filterAssignedTermsCallback(string $query, string $shortcodeTag): array
    {
        $options = glsr(ShortcodeOptionManager::class)->assigned_terms([
            'search' => $query,
            'shortcode' => stripslashes($shortcodeTag),
        ]);
        return array_map(
            fn ($value, $label) => [
                'label' => sprintf('ID: %s - %s', $value, $label),
                'value' => $value,
            ],
            array_keys($options),
            array_values($options)
        );
    }

    /**
     * @filter vc_autocomplete_site_reviews_assigned_term_render
     * @filter vc_autocomplete_site_reviews_field_assigned_term_render
     * @filter vc_autocomplete_site_reviews_form_assigned_term_render
     * @filter vc_autocomplete_site_reviews_images_assigned_term_render
     * @filter vc_autocomplete_site_reviews_summary_assigned_term_render
     */
    public function filterAssignedTermsRender(array $query, array $settings, string $shortcodeTag): ?array
    {
        $value = $query['value'] ?? '';
        if (empty($value)) {
            return null;
        }
        $options = glsr(ShortcodeOptionManager::class)->assigned_terms([
            'per_page' => 1,
            'search' => $value,
            'shortcode' => stripslashes($shortcodeTag),
        ]);
        if (!isset($options[$value])) {
            return $query;
        }
        return [
            'label' => sprintf('ID: %s - %s', $value, $options[$value]),
            'value' => $value,
        ];
    }

    /**
     * @filter vc_autocomplete_site_reviews_assigned_users_callback
     * @filter vc_autocomplete_site_reviews_form_assigned_users_callback
     * @filter vc_autocomplete_site_reviews_summary_assigned_users_callback
     */
    public function filterAssignedUsersCallback(string $query, string $shortcodeTag): array
    {
        $options = glsr(ShortcodeOptionManager::class)->assigned_users([
            'search' => $query,
            'shortcode' => stripslashes($shortcodeTag),
        ]);
        return array_map(
            fn ($value, $label) => [
                'label' => is_numeric($value) ? sprintf('ID: %s - %s', $value, $label) : $label,
                'value' => $value,
            ],
            array_keys($options),
            array_values($options)
        );
    }

    /**
     * @filter vc_autocomplete_site_reviews_assigned_users_render
     * @filter vc_autocomplete_site_reviews_field_assigned_users_render
     * @filter vc_autocomplete_site_reviews_form_assigned_users_render
     * @filter vc_autocomplete_site_reviews_images_assigned_users_render
     * @filter vc_autocomplete_site_reviews_summary_assigned_users_render
     */
    public function filterAssignedUsersRender(array $query, array $settings, string $shortcodeTag): ?array
    {
        $value = $query['value'] ?? '';
        if (empty($value)) {
            return null;
        }
        $options = glsr(ShortcodeOptionManager::class)->assigned_users([
            'per_page' => 1,
            'include' => $value,
            'shortcode' => stripslashes($shortcodeTag),
        ]);
        if (!isset($options[$value])) {
            return $query;
        }
        return [
            'label' => is_numeric($value) ? sprintf('ID: %s - %s', $value, $options[$value]) : $options[$value],
            'value' => $value,
        ];
    }

    /**
     * @filter vc_autocomplete_site_reviews_assigned_users_callback
     * @filter vc_autocomplete_site_reviews_form_assigned_users_callback
     * @filter vc_autocomplete_site_reviews_summary_assigned_users_callback
     */
    public function filterAuthorCallback(string $query, string $shortcodeTag): array
    {
        $options = glsr(ShortcodeOptionManager::class)->author([
            'search' => $query,
            'shortcode' => stripslashes($shortcodeTag),
        ]);
        return array_map(
            fn ($value, $label) => [
                'label' => is_numeric($value) ? sprintf('ID: %s - %s', $value, $label) : $label,
                'value' => $value,
            ],
            array_keys($options),
            array_values($options)
        );
    }

    /**
     * @filter vc_autocomplete_site_reviews_author_render
     * @filter vc_autocomplete_site_reviews_field_author_render
     * @filter vc_autocomplete_site_reviews_form_author_render
     * @filter vc_autocomplete_site_reviews_images_author_render
     * @filter vc_autocomplete_site_reviews_summary_author_render
     */
    public function filterAuthorRender(array $query, array $settings, string $shortcodeTag): ?array
    {
        $value = $query['value'] ?? '';
        if (empty($value)) {
            return null;
        }
        $options = glsr(ShortcodeOptionManager::class)->author([
            'per_page' => 1,
            'include' => $value,
            'shortcode' => stripslashes($shortcodeTag),
        ]);
        if (!isset($options[$value])) {
            return $query;
        }
        return [
            'label' => is_numeric($value) ? sprintf('ID: %s - %s', $value, $options[$value]) : $options[$value],
            'value' => $value,
        ];
    }

    /**
     * @filter site-reviews/modal_wrapped_by
     */
    public function filterModalWrappedBy(array $builders): array
    {
        $builders[] = 'wpbakery';
        return $builders;
    }

    /**
     * @filter vc_autocomplete_site_review_post_id_callback
     */
    public function filterPostIdCallback(string $query, string $shortcodeTag): array
    {
        $args = is_numeric($query) ? ['include' => $query] : ['search' => $query];
        $options = glsr(ShortcodeOptionManager::class)->post_id(wp_parse_args($args, [
            'shortcode' => stripslashes($shortcodeTag),
        ]));
        return array_map(
            fn ($value, $label) => [
                'label' => sprintf('ID: %s - %s', $value, $label),
                'value' => $value,
            ],
            array_keys($options),
            array_values($options)
        );
    }

    /**
     * @filter vc_autocomplete_site_review_post_id_render
     */
    public function filterPostIdRender(array $query, array $settings, string $shortcodeTag): ?array
    {
        $value = $query['value'] ?? '';
        if (empty($value)) {
            return null;
        }
        $options = glsr(ShortcodeOptionManager::class)->post_id([
            'per_page' => 1,
            'include' => $value,
            'shortcode' => stripslashes($shortcodeTag),
        ]);
        if (!isset($options[$value])) {
            return $query;
        }
        return [
            'label' => sprintf('ID: %s - %s', $value, $options[$value]),
            'value' => $value,
        ];
    }

    /**
     * @filter site-reviews/shortcode/wrap/attributes
     */
    public function filterWrapAttrClass(array $attributes, array $args, ShortcodeContract $shortcode): array
    {
        if ('wpbakery' !== $shortcode->from) {
            return $attributes;
        }
        $classes = [
            $attributes['class'] ?? '',
        ];
        if (!empty($args['style_align'])) {
            $classes[] = "items-justified-{$args['style_align']}";
        }
        if (!empty($args['style_rating_color'])) {
            $classes[] = 'has-rating-color';
        }
        if (!empty($args['style_text_align'])) {
            $classes[] = "has-text-align-{$args['style_text_align']}";
        }
        $attributes['class'] = glsr(Sanitizer::class)->sanitizeAttrClass(implode(' ', $classes));
        return $attributes;
    }

    /**
     * @filter site-reviews/shortcode/wrap/attributes
     */
    public function filterWrapAttrStyle(array $attributes, array $args, ShortcodeContract $shortcode): array
    {
        if ('wpbakery' !== $shortcode->from) {
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
                '--glsr-bar-bg' => 'style_bar_color',
                '--glsr-max-w' => 'style_max_width',
                '--glsr-summary-star-bg' => 'style_rating_color',
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
     * @action vc_before_init
     */
    public function registerShortcodes(): void
    {
        Shortcodes\VcSiteReview::vcRegister();
        Shortcodes\VcSiteReviews::vcRegister();
        Shortcodes\VcSiteReviewsForm::vcRegister();
        Shortcodes\VcSiteReviewsSummary::vcRegister();
    }

    /**
     * @action vc_load_default_params
     */
    public function registerParameters(): void
    {
        vc_add_shortcode_param('glsr_type_range', [$this, 'renderFieldRange'], glsr()->url('assets/scripts/integrations/wpbakery-editor.js'));
    }

    /**
     * @param mixed  $settings
     * @param string $value
     *
     * @callback vc_add_shortcode_param
     */
    public function renderFieldRange($settings, $value): string
    {
        $max = Arr::get($settings, 'max');
        $min = Arr::get($settings, 'min');
        $step = Arr::get($settings, 'step');
        if ('' !== $max) {
            $max = Cast::toInt($max);
        }
        if ('' !== $min) {
            $min = Cast::toInt($min);
        }
        if ('' !== $step) {
            $step = Cast::toInt($step);
        }
        $input = glsr(Builder::class)->input([
            'class' => "wpb_vc_param_value wpb-textinput",
            'max' => $max,
            'min' => $min,
            'name' => $settings['param_name'],
            'step' => $step,
            'type' => 'range',
            'value' => sanitize_text_field(Cast::toString($value)),
        ]);
        $info = glsr(Builder::class)->input([
            'class' => 'wpb_vc_param_infobox',
            'max' => $max,
            'min' => $min,
            'step' => $step,
            'type' => 'number',
            'value' => sanitize_text_field(Cast::toString($value)),
        ]);
        return glsr(Builder::class)->div([
            'class' => "{$settings['type']}-wrapper",
            'text' => $info.$input,
        ]);
    }
}
