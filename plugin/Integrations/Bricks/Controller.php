<?php

namespace GeminiLabs\SiteReviews\Integrations\Bricks;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class Controller extends AbstractController
{
    /**
     * @param array $i18n
     *
     * @filter bricks/builder/i18n
     */
    public function filterBuilderI18n($i18n): array
    {
        $i18n = Arr::consolidate($i18n);
        $i18n[glsr()->id] = glsr()->name;
        return $i18n;
    }

    /**
     * Consolidate multi-checkbox values because Bricks does not allow them.
     *
     * @param \Bricks\Element $element
     *
     * @filter bricks/element/settings
     */
    public function filterSettingsMultiCheckbox($settings, $element): array
    {
        $settings = Arr::consolidate($settings);
        if (!is_a($element, BricksElement::class)) {
            return $settings;
        }
        foreach ($element->elementConfig() as $key => $control) {
            $type = $control['type'] ?? '';
            $options = Arr::getAs('array', $control, 'options');
            if ('checkbox' !== $type || empty($options)) {
                continue;
            }
            $values = array_filter(
                array_keys($settings),
                fn ($k) => !empty($settings[$k]) && str_starts_with($k, "{$key}_")
            );
            $settings[$key] = array_map(fn ($k) => Str::removePrefix($k, "{$key}_"), $values);
        }
        return $settings;
    }

    /**
     * Remove the "id::" prefix used to maintain javascript sorting.
     *
     * @param \Bricks\Element $element
     *
     * @filter bricks/element/settings
     */
    public function filterSettingsPrefixedId($settings, $element): array
    {
        $settings = Arr::consolidate($settings);
        if (!is_a($element, BricksElement::class)) {
            return $settings;
        }
        foreach ($settings as $key => $value) {
            if (is_string($value) && str_starts_with($value, 'id::')) {
                $settings[$key] = Str::removePrefix($value, 'id::');
                continue;
            }
            if (is_array($value)) {
                $settings[$key] = array_map(
                    fn ($val) => Str::removePrefix((string) $val, 'id::'),
                    $value
                );
            }
        }
        return $settings;
    }

    /**
     * @action wp_enqueue_scripts
     */
    public function printInlineStyles(): void
    {
        if (!function_exists('bricks_is_builder')) {
            return;
        }
        if (!bricks_is_builder()) {
            return;
        }
        $iconForm = Helper::svg('assets/images/icons/bricks/icon-form.svg', true);
        $iconReview = Helper::svg('assets/images/icons/bricks/icon-review.svg', true);
        $iconReviews = Helper::svg('assets/images/icons/bricks/icon-reviews.svg', true);
        $iconSummary = Helper::svg('assets/images/icons/bricks/icon-summary.svg', true);
        $css = "
            i[class^='ti-site_review']::before {
                background-color: currentColor;
                content: '';
                display: inline-block;
                height: 1em;
                mask-position: center;
                mask-repeat: no-repeat;
                mask-size: 100%;
                width: 1em;
            }
            i.ti-site_reviews_form::before {
                mask-image: url(\"{$iconForm}\");
            }
            i.ti-site_review::before {
                mask-image: url(\"{$iconReview}\");
            }
            i.ti-site_reviews::before {
                mask-image: url(\"{$iconReviews}\");
            }
            i.ti-site_reviews_summary::before {
                mask-image: url(\"{$iconSummary}\");
            }
            .glsr :is(a,button,input,textarea,select,.dz-clickable) {
                pointer-events: none !important;
            }
        ";
        wp_add_inline_style('bricks-builder', $css);
    }

    /**
     * @action init
     */
    public function registerElements(): void
    {
        if (class_exists('Bricks\Element')) {
            BricksSiteReview::registerElement();
            BricksSiteReviews::registerElement();
            BricksSiteReviewsForm::registerElement();
            BricksSiteReviewsSummary::registerElement();
        }
    }

    /**
     * @action wp_ajax_bricks_glsr_assigned_posts
     */
    public function searchAssignedPosts(): void
    {
        $this->verifyAjaxRequest();
        $query = stripslashes_deep(sanitize_text_field((string) filter_input(INPUT_GET, 'search')));
        $args = [
            'post__in' => [],
            'posts_per_page' => 25,
        ];
        if (is_numeric($query)) {
            $args['post__in'][] = (int) $query;
        } else {
            $args['s'] = $query;
        }
        $posts = glsr(Database::class)->posts($args);
        if ($include = $this->includedIds($posts)) {
            $posts += glsr(Database::class)->posts([
                'post__in' => $include,
            ]);
        }
        $results = $this->prefixedResultIds($posts);
        if (empty($query)) {
            $results = [
                'post_id' => esc_html_x('The Current Page', 'admin-text', 'site-reviews').' (post_id)',
                'parent_id' => esc_html_x('The Parent Page', 'admin-text', 'site-reviews').' (parent_id)',
            ] + $results;
        }
        wp_send_json_success($results);
    }

    /**
     * @action wp_ajax_bricks_glsr_assigned_terms
     */
    public function searchAssignedTerms(): void
    {
        $this->verifyAjaxRequest();
        $query = stripslashes_deep(sanitize_text_field((string) filter_input(INPUT_GET, 'search')));
        $terms = glsr(Database::class)->terms([
            'number' => 25,
            'search' => $query,
        ]);
        if ($include = $this->includedIds($terms)) {
            $terms += glsr(Database::class)->terms([
                'term_taxonomy_id' => $include,
            ]);
        }
        $results = $this->prefixedResultIds($terms);
        wp_send_json_success($results);
    }

    /**
     * @action wp_ajax_bricks_glsr_assigned_users
     */
    public function searchAssignedUsers(): void
    {
        $this->verifyAjaxRequest();
        $query = stripslashes_deep(sanitize_text_field((string) filter_input(INPUT_GET, 'search')));
        $users = glsr(Database::class)->users([
            'number' => 25,
            'search_wild' => $query,
        ]);
        if ($include = $this->includedIds($users)) {
            $users += glsr(Database::class)->users([
                'include' => $include,
            ]);
        }
        $results = $this->prefixedResultIds($users);
        if (empty($query)) {
            $results = [
                'user_id' => esc_html_x('The Logged In User', 'admin-text', 'site-reviews').' (user_id)',
                'author_id' => esc_html_x('The Page Author', 'admin-text', 'site-reviews').' (author_id)',
                'profile_id' => esc_html_x('The Profile User', 'admin-text', 'site-reviews').' (profile_id)',
            ] + $results;
        }
        wp_send_json_success($results);
    }

    /**
     * @action wp_ajax_bricks_glsr_post_id
     */
    public function searchPostId(): void
    {
        $this->verifyAjaxRequest();
        $query = stripslashes_deep(sanitize_text_field((string) filter_input(INPUT_GET, 'search')));
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
        if ($include = $this->includedIds($posts)) {
            $posts += glsr(Database::class)->posts([
                'post__in' => $include,
                'post_type' => glsr()->post_type,
            ]);
        }
        $results = $this->prefixedResultIds($posts);
        wp_send_json_success($results);
    }

    protected function includedIds(array $results): array
    {
        $ids = filter_input(INPUT_GET, 'include', FILTER_DEFAULT, FILTER_FORCE_ARRAY) ?? [];
        $ids = array_map(fn ($id) => Str::removePrefix((string) $id, 'id::'), $ids);
        $ids = Arr::uniqueInt($ids);
        $ids = array_filter($ids, fn ($id) => !array_key_exists($id, $results));
        return $ids;
    }

    protected function prefixedResultIds(array $results): array
    {
        return array_combine(
            array_map(fn ($key) => "id::{$key}", array_keys($results)),
            $results
        );
    }

    protected function verifyAjaxRequest(): void
    {
        if (method_exists('Bricks\Ajax', 'verify_request')) { // @phpstan-ignore-line
            \Bricks\Ajax::verify_request('bricks-nonce-builder');
        } elseif (!check_ajax_referer('bricks-nonce-builder', 'nonce', false)) {
            wp_send_json_error('verify_nonce: "bricks-nonce-builder" is invalid.');
        }
    }
}
