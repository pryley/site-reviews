<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Defaults\CreateReviewDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Field;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Review;

class MetaboxController
{
    /**
     * @param bool $protected
     * @param string $metaKey
     * @param string $metaType
     * @return bool
     * @filter is_protected_meta
     */
    public function filterProtectedMeta($protected, $metaKey, $metaType)
    {
        if ('post' == $metaType && glsr()->post_type == get_post_type()) {
            $values = glsr(CreateReviewDefaults::class)->unguarded();
            $values = Arr::prefixKeys($values);
            if (array_key_exists($metaKey, $values)) {
                $protected = false;
            }
        }
        return $protected;
    }

    /**
     * @return void
     * @action add_meta_boxes_{glsr()->post_type}
     */
    public function registerMetaBoxes($post)
    {
        if ('local' === glsr(Query::class)->review($post->ID)->type) {
            add_meta_box(glsr()->post_type.'-responsediv', _x('Respond Publicly', 'admin-text', 'site-reviews'), [$this, 'renderResponseMetaBox'], null, 'normal', 'high');
        }
        add_meta_box(glsr()->post_type.'-detailsdiv', _x('Review Details', 'admin-text', 'site-reviews'), [$this, 'renderDetailsMetaBox'], null, 'normal', 'high');
        add_meta_box(glsr()->post_type.'-postsdiv', _x('Assigned To', 'admin-text', 'site-reviews'), [$this, 'renderAssignedPostsMetabox'], null, 'side');
        add_meta_box(glsr()->post_type.'-usersdiv', _x('Assigned Users', 'admin-text', 'site-reviews'), [$this, 'renderAssignedUsersMetabox'], null, 'side');
        if (post_type_supports(glsr()->post_type, 'author') && glsr()->can('edit_others_posts')) {
            add_meta_box(glsr()->post_type.'-authordiv', _x('Author', 'admin-text', 'site-reviews'), [$this, 'renderAuthorMetabox'], null, 'side');
        }
    }

    /**
     * @return void
     * @action do_meta_boxes
     */
    public function removeMetaBoxes()
    {
        if ($this->isReviewEditor()) {
            remove_meta_box('authordiv', glsr()->post_type, 'normal');
            remove_meta_box('slugdiv', glsr()->post_type, 'normal');
        }
    }

    /**
     * @param \WP_Post $post
     * @return void
     * @callback add_meta_box
     */
    public function renderAssignedPostsMetabox($post)
    {
        if (Review::isReview($post)) {
            $review = glsr(Query::class)->review($post->ID);
            wp_nonce_field('assigned_posts', '_nonce-assigned-posts', false);
            $templates = array_reduce($review->assigned_post_ids, function ($carry, $postId) {
                return $carry.glsr(Template::class)->build('partials/editor/assigned-entry', [
                    'context' => [
                        'data.id' => $postId,
                        'data.name' => 'post_ids[]',
                        'data.url' => (string) get_permalink($postId),
                        'data.title' => get_the_title($postId),
                    ],
                ]);
            });
            glsr()->render('partials/editor/metabox-assigned-posts', [
                'templates' => $templates,
            ]);
        }
    }

    /**
     * @param \WP_Post $post
     * @return void
     * @callback add_meta_box
     */
    public function renderAssignedUsersMetabox($post)
    {
        if (Review::isReview($post)) {
            $review = glsr(Query::class)->review($post->ID);
            wp_nonce_field('assigned_users', '_nonce-assigned-users', false);
            $templates = array_reduce($review->assigned_user_ids, function ($carry, $userId) {
                $carry .= glsr(Template::class)->build('partials/editor/assigned-entry', [
                    'context' => [
                        'data.id' => $userId,
                        'data.name' => 'user_ids[]',
                        'data.url' => esc_url(get_author_posts_url($userId)),
                        'data.title' => esc_attr(get_the_author_meta('display_name', $userId)),
                    ],
                ]);
                return $carry;
            });
            glsr()->render('partials/editor/metabox-assigned-users', [
                'templates' => $templates,
            ]);
        }
    }

    /**
     * @param \WP_Post $post
     * @return void
     * @callback add_meta_box
     */
    public function renderAuthorMetabox($post)
    {
        echo glsr(Builder::class)->label([
            'class' => 'screen-reader-text',
            'for' => 'post_author_override',
            'text' => _x('Author', 'admin-text', 'site-reviews'),
        ]);
        wp_dropdown_users([
            'include_selected' => true,
            'name' => 'post_author_override',
            'option_none_value' => 0,
            'selected' => empty($post->ID) ? get_current_user_id() : $post->post_author,
            'show' => 'display_name_with_login',
            'show_option_none' => 'Author Unknown',
            'who' => 'authors',
        ]);
    }

    /**
     * @param \WP_Post $post
     * @return void
     * @callback add_meta_box
     */
    public function renderDetailsMetaBox($post)
    {
        if (Review::isReview($post)) {
            $review = glsr(Query::class)->review($post->ID);
            glsr()->render('partials/editor/metabox-details', [
                'metabox' => $this->normalizeDetailsMetaBox($review),
            ]);
        }
    }

    /**
     * @return void
     * @action post_submitbox_misc_actions
     */
    public function renderPinnedInPublishMetaBox()
    {
        $review = glsr(Query::class)->review(get_post()->ID);
        if ($review->isValid() && glsr()->can('edit_others_posts')) {
            glsr(Template::class)->render('partials/editor/pinned', [
                'context' => [
                    'no' => _x('No', 'admin-text', 'site-reviews'),
                    'yes' => _x('Yes', 'admin-text', 'site-reviews'),
                ],
                'pinned' => $review->is_pinned,
            ]);
        }
    }

    /**
     * @param \WP_Post $post
     * @return void
     * @callback add_meta_box
     */
    public function renderResponseMetaBox($post)
    {
        if (Review::isReview($post)) {
            wp_nonce_field('response', '_nonce-response', false);
            glsr()->render('partials/editor/metabox-response', [
                'response' => glsr(Database::class)->meta($post->ID, 'response'),
            ]);
        }
    }

    /**
     * @param \WP_Post $post
     * @return void
     * @see glsr_categories_meta_box()
     * @callback register_taxonomy
     */
    public function renderTaxonomyMetabox($post)
    {
        if (Review::isReview($post)) {
            glsr()->render('partials/editor/metabox-categories', [
                'post' => $post,
                'tax_name' => glsr()->taxonomy,
                'taxonomy' => get_taxonomy(glsr()->taxonomy),
            ]);
        }
    }

    /**
     * @param int $postId
     * @return mixed
     */
    public function saveResponseMetabox($postId)
    {
        if (!wp_verify_nonce(Helper::filterInput('_nonce-response'), 'response')) {
            return;
        }
        $response = strval(Helper::filterInput('response'));
        $response = trim(wp_kses($response, [
            'a' => ['href' => [], 'title' => []],
            'em' => [],
            'strong' => [],
        ]));
        glsr(Database::class)->metaSet($postId, 'response', $response);
    }

    /**
     * @return bool
     */
    protected function isReviewEditor()
    {
        $screen = glsr_current_screen();
        return 'post' == $screen->base
            && glsr()->post_type == $screen->id
            && glsr()->post_type == $screen->post_type;
    }

    /**
     * @return array
     */
    protected function normalizeDetailsMetaBox(Review $review)
    {
        $fields = glsr()->filterArray('addon/metabox/fields', glsr()->config('forms/metabox-fields'));
        foreach ($fields as $key => &$field) {
            $field['class'] = 'glsr-input-value';
            $field['is_metabox'] = true;
            $field['name'] = $key;
            $field['disabled'] = true;
            $field['review_object'] = $review;
            $field['value'] = $review->$key;
        }
        $fields = glsr()->filterArray('metabox/fields', $fields, $review);
        array_walk($fields, function (&$field) {
            $field = new Field($field);
        });
        return array_values($fields);
    }
}
