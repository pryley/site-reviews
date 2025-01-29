<?php

namespace GeminiLabs\SiteReviews\Integrations\WPBakery;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

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
     * @filter vc_autocomplete_site_reviews_form_assigned_posts_callback
     * @filter vc_autocomplete_site_reviews_summary_assigned_posts_callback
     */
    public function filterAutocompleteAssignedPosts(?string $query = ''): array
    {
        $args = [
            'post__in' => [],
            'posts_per_page' => 25,
        ];
        if (is_numeric($query)) {
            $args['post__in'][] = (int) $query;
        } else {
            $args['s'] = (string) $query;
        }
        $posts = glsr(Database::class)->posts($args);
        $callback = fn ($value, $label) => compact('value', 'label');
        $results = array_map($callback, array_keys($posts), array_values($posts));
        array_unshift($results,
            [
                'label' => _x('The Current Page', 'admin-text', 'site-reviews'),
                'value' => 'post_id',
            ],
            [
                'label' => _x('The Parent Page', 'admin-text', 'site-reviews'),
                'value' => 'parent_id',
            ]
        );
        return $results;
    }

    /**
     * @filter vc_autocomplete_site_reviews_assigned_terms_callback
     * @filter vc_autocomplete_site_reviews_form_assigned_terms_callback
     * @filter vc_autocomplete_site_reviews_summary_assigned_terms_callback
     */
    public function filterAutocompleteAssignedTerms(?string $query = ''): array
    {
        $users = glsr(Database::class)->terms([
            'number' => 25,
            'search' => (string) $query,
        ]);
        $callback = fn ($value, $label) => compact('value', 'label');
        $results = array_map($callback, array_keys($users), array_values($users));
        return $results;
    }

    /**
     * @filter vc_autocomplete_site_reviews_assigned_users_callback
     * @filter vc_autocomplete_site_reviews_form_assigned_users_callback
     * @filter vc_autocomplete_site_reviews_summary_assigned_users_callback
     */
    public function filterAutocompleteAssignedUsers(?string $query = ''): array
    {
        $users = glsr(Database::class)->users([
            'number' => 25,
            'search_wild' => $query,
        ]);
        $callback = fn ($value, $label) => compact('value', 'label');
        $results = array_map($callback, array_keys($users), array_values($users));
        array_unshift($results,
            [
                'label' => _x('The Logged-in user', 'admin-text', 'site-reviews'),
                'value' => 'user_id',
            ],
            [
                'label' => _x('The Page author', 'admin-text', 'site-reviews'),
                'value' => 'author_id',
            ],
            [
                'label' => _x('The Profile user (BuddyPress/Ultimate Member)', 'admin-text', 'site-reviews'),
                'value' => 'profile_id',
            ]
        );
        return $results;
    }

    /**
     * @filter vc_autocomplete_site_review_post_id_callback
     */
    public function filterAutocompletePostId(?string $query = ''): array
    {
        $args = [
            'post__in' => [],
            'post_type' => glsr()->post_type,
            'posts_per_page' => 25,
        ];
        if (is_numeric($query)) {
            $args['post__in'][] = (int) $query;
        } else {
            $args['s'] = (string) $query;
        }
        $posts = glsr(Database::class)->posts($args);
        $callback = fn ($value, $label) => compact('value', 'label');
        $results = array_map($callback, array_keys($posts), array_values($posts));
        return $results;
    }

    /**
     * @filter vc_single_param_edit_holder_output
     */
    public function filterSettingOutput($output, $param, $value, $settings): string
    {
        if (!str_starts_with(Arr::get($settings, 'base'), 'site_review')) {
            return $output;
        }
        if ('checkbox' !== Arr::get($param, 'type')) {
            return $output;
        }
        $output = str_replace('label> <label', 'label><br><label', $output);
        return $output;
    }

    /**
     * @action vc_before_init
     */
    public function registerShortcodes(): void
    {
        VcSiteReview::vcRegister();
        VcSiteReviews::vcRegister();
        VcSiteReviewsForm::vcRegister();
        VcSiteReviewsSummary::vcRegister();
    }

    /**
     * @action vc_load_default_params
     */
    public function registerParameters(): void
    {
        vc_add_shortcode_param('glsr_type_range', [$this, 'renderFieldRange'], glsr()->url('assets/scripts/integrations/wpbakery-editor.js'));
    }

    /**
     * @param mixed $settings
     * @param string $value
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
