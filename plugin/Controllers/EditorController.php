<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Controllers\EditorController\Customization;
use GeminiLabs\SiteReviews\Controllers\EditorController\Labels;
use GeminiLabs\SiteReviews\Controllers\EditorController\Metaboxes;
use GeminiLabs\SiteReviews\Controllers\ListTableController\Columns;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\CreateReviewDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Review;
use WP_Post;

class EditorController extends Controller
{
    /**
     * @param array $settings
     * @return array
     * @filter wp_editor_settings
     */
    public function filterEditorSettings($settings)
    {
        return glsr(Customization::class)->filterEditorSettings(
            Arr::consolidateArray($settings)
        );
    }

    /**
     * Modify the WP_Editor html to allow autosizing without breaking the `editor-expand` script.
     * @param string $html
     * @return string
     * @filter the_editor
     */
    public function filterEditorTextarea($html)
    {
        return glsr(Customization::class)->filterEditorTextarea($html);
    }

    /**
     * @param bool $protected
     * @param string $metaKey
     * @param string $metaType
     * @return bool
     * @filter is_protected_meta
     */
    public function filterIsProtectedMeta($protected, $metaKey, $metaType)
    {
        if ('post' == $metaType && Application::POST_TYPE == get_post_type()) {
            $values = glsr(CreateReviewDefaults::class)->unguarded();
            $values = Arr::prefixArrayKeys($values);
            if (array_key_exists($metaKey, $values)) {
                $protected = false;
            }
        }
        return $protected;
    }

    /**
     * @param array $messages
     * @return array
     * @filter post_updated_messages
     */
    public function filterUpdateMessages($messages)
    {
        return glsr(Labels::class)->filterUpdateMessages(
            Arr::consolidateArray($messages)
        );
    }

    /**
     * @return void
     * @action add_meta_boxes_{Application::POST_TYPE}
     */
    public function registerMetaBoxes($post)
    {
        add_meta_box(Application::ID.'_assigned_to', __('Assigned To', 'site-reviews'), [$this, 'renderAssignedToMetabox'], null, 'side');
        add_meta_box(Application::ID.'_review', __('Details', 'site-reviews'), [$this, 'renderDetailsMetaBox'], null, 'side');
        if ('local' != glsr(Database::class)->get($post->ID, 'review_type')) {
            return;
        }
        add_meta_box(Application::ID.'_response', __('Respond Publicly', 'site-reviews'), [$this, 'renderResponseMetaBox'], null, 'normal');
    }

    /**
     * @return void
     * @action admin_print_scripts
     */
    public function removeAutosave()
    {
        glsr(Customization::class)->removeAutosave();
    }

    /**
     * @return void
     * @action admin_menu
     */
    public function removeMetaBoxes()
    {
        glsr(Customization::class)->removeMetaBoxes();
    }

    /**
     * @return void
     */
    public function removePostTypeSupport()
    {
        glsr(Customization::class)->removePostTypeSupport();
    }

    /**
     * @param WP_Post $post
     * @return void
     * @callback add_meta_box
     */
    public function renderAssignedToMetabox($post)
    {
        if (!$this->isReviewPostType($post)) {
            return;
        }
        $assignedTo = (string) glsr(Database::class)->get($post->ID, 'assigned_to');
        wp_nonce_field('assigned_to', '_nonce-assigned-to', false);
        glsr()->render('partials/editor/metabox-assigned-to', [
            'id' => $assignedTo,
            'template' => $this->buildAssignedToTemplate($assignedTo, $post),
        ]);
    }

    /**
     * @param WP_Post $post
     * @return void
     * @callback add_meta_box
     */
    public function renderDetailsMetaBox($post)
    {
        if (!$this->isReviewPostType($post)) {
            return;
        }
        $review = glsr_get_review($post);
        glsr()->render('partials/editor/metabox-details', [
            'button' => $this->buildDetailsMetaBoxRevertButton($review, $post),
            'metabox' => $this->normalizeDetailsMetaBox($review),
        ]);
    }

    /**
     * @return void
     * @action post_submitbox_misc_actions
     */
    public function renderPinnedInPublishMetaBox()
    {
        if (!$this->isReviewPostType(get_post())
            || !glsr()->can('edit_others_posts')) {
            return;
        }
        glsr(Template::class)->render('partials/editor/pinned', [
            'context' => [
                'no' => __('No', 'site-reviews'),
                'yes' => __('Yes', 'site-reviews'),
            ],
            'pinned' => wp_validate_boolean(glsr(Database::class)->get(get_the_ID(), 'pinned')),
        ]);
    }

    /**
     * @param WP_Post $post
     * @return void
     * @callback add_meta_box
     */
    public function renderResponseMetaBox($post)
    {
        if (!$this->isReviewPostType($post)) {
            return;
        }
        wp_nonce_field('response', '_nonce-response', false);
        glsr()->render('partials/editor/metabox-response', [
            'response' => glsr(Database::class)->get($post->ID, 'response'),
        ]);
    }

    /**
     * @param WP_Post $post
     * @return void
     * @action edit_form_after_title
     */
    public function renderReviewEditor($post)
    {
        if (!$this->isReviewPostType($post) || $this->isReviewEditable($post)) {
            return;
        }
        glsr()->render('partials/editor/review', [
            'post' => $post,
            'response' => glsr(Database::class)->get($post->ID, 'response'),
        ]);
    }

    /**
     * @return void
     * @action admin_head
     */
    public function renderReviewFields()
    {
        $screen = glsr_current_screen();
        if ('post' != $screen->base || Application::POST_TYPE != $screen->post_type) {
            return;
        }
        add_action('edit_form_after_title', [$this, 'renderReviewEditor']);
        add_action('edit_form_top', [$this, 'renderReviewNotice']);
    }

    /**
     * @param WP_Post $post
     * @return void
     * @action edit_form_top
     */
    public function renderReviewNotice($post)
    {
        if (!$this->isReviewPostType($post) || $this->isReviewEditable($post)) {
            return;
        }
        glsr(Notice::class)->addWarning(sprintf(
            __('%s reviews are read-only.', 'site-reviews'),
            glsr(Columns::class)->buildColumnReviewType($post->ID)
        ));
        glsr(Template::class)->render('partials/editor/notice', [
            'context' => [
                'notices' => glsr(Notice::class)->get(),
            ],
        ]);
    }

    /**
     * @param WP_Post $post
     * @return void
     * @see glsr_categories_meta_box()
     * @callback register_taxonomy
     */
    public function renderTaxonomyMetabox($post)
    {
        if (!$this->isReviewPostType($post)) {
            return;
        }
        glsr()->render('partials/editor/metabox-categories', [
            'post' => $post,
            'tax_name' => Application::TAXONOMY,
            'taxonomy' => get_taxonomy(Application::TAXONOMY),
        ]);
    }

    /**
     * @return void
     * @see $this->filterUpdateMessages()
     * @action admin_action_revert
     */
    public function revertReview()
    {
        if (Application::ID != filter_input(INPUT_GET, 'plugin')) {
            return;
        }
        check_admin_referer('revert-review_'.($postId = $this->getPostId()));
        glsr(ReviewManager::class)->revert($postId);
        $this->redirect($postId, 52);
    }

    /**
     * @param int $postId
     * @param \WP_Post $post
     * @param bool $isUpdate
     * @return void
     * @action save_post_.Application::POST_TYPE
     */
    public function saveMetaboxes($postId, $post, $isUpdating)
    {
        glsr(Metaboxes::class)->saveAssignedToMetabox($postId);
        glsr(Metaboxes::class)->saveResponseMetabox($postId);
        if ($isUpdating) {
            do_action('site-reviews/review/saved', glsr_get_review($postId));
        }
    }

    /**
     * @param string $assignedTo
     * @return string
     */
    protected function buildAssignedToTemplate($assignedTo, WP_Post $post)
    {
        $assignedPost = glsr(Database::class)->getAssignedToPost($post->ID, $assignedTo);
        if (!($assignedPost instanceof WP_Post)) {
            return;
        }
        return glsr(Template::class)->build('partials/editor/assigned-post', [
            'context' => [
                'data.url' => (string) get_permalink($assignedPost),
                'data.title' => get_the_title($assignedPost),
            ],
        ]);
    }

    /**
     * @return string
     */
    protected function buildDetailsMetaBoxRevertButton(Review $review, WP_Post $post)
    {
        $isModified = !Arr::compareArrays(
            [$review->title, $review->content, $review->date],
            [
                glsr(Database::class)->get($post->ID, 'title'),
                glsr(Database::class)->get($post->ID, 'content'),
                glsr(Database::class)->get($post->ID, 'date'),
            ]
        );
        if ($isModified) {
            $revertUrl = wp_nonce_url(
                admin_url('post.php?post='.$post->ID.'&action=revert&plugin='.Application::ID),
                'revert-review_'.$post->ID
            );
            return glsr(Builder::class)->a(__('Revert Changes', 'site-reviews'), [
                'class' => 'button button-large',
                'href' => $revertUrl,
                'id' => 'revert',
            ]);
        }
        return glsr(Builder::class)->button(__('Nothing to Revert', 'site-reviews'), [
            'class' => 'button-large',
            'disabled' => true,
            'id' => 'revert',
        ]);
    }

    /**
     * @param object $review
     * @return string|void
     */
    protected function getReviewType($review)
    {
        if (count(glsr()->reviewTypes) < 2) {
            return;
        }
        $reviewType = array_key_exists($review->review_type, glsr()->reviewTypes)
            ? glsr()->reviewTypes[$review->review_type]
            : __('Unknown', 'site-reviews');
        if (!empty($review->url)) {
            $reviewType = glsr(Builder::class)->a($reviewType, [
                'href' => $review->url,
                'target' => '_blank',
            ]);
        }
        return $reviewType;
    }

    /**
     * @return bool
     */
    protected function isReviewEditable($post)
    {
        return $this->isReviewPostType($post)
            && post_type_supports(Application::POST_TYPE, 'title')
            && 'local' == glsr(Database::class)->get($post->ID, 'review_type');
    }

    /**
     * @param mixed $post
     * @return bool
     */
    protected function isReviewPostType($post)
    {
        return $post instanceof WP_Post && Application::POST_TYPE == $post->post_type;
    }

    /**
     * @return array
     */
    protected function normalizeDetailsMetaBox(Review $review)
    {
        $user = empty($review->user_id)
            ? __('Unregistered user', 'site-reviews')
            : glsr(Builder::class)->a(get_the_author_meta('display_name', $review->user_id), [
                'href' => get_author_posts_url($review->user_id),
            ]);
        $email = empty($review->email)
            ? '&mdash;'
            : glsr(Builder::class)->a($review->email, [
                'href' => 'mailto:'.$review->email.'?subject='.esc_attr(__('RE:', 'site-reviews').' '.$review->title),
            ]);
        $metabox = [
            __('Rating', 'site-reviews') => glsr_star_rating($review->rating),
            __('Type', 'site-reviews') => $this->getReviewType($review),
            __('Date', 'site-reviews') => get_date_from_gmt($review->date, 'F j, Y'),
            __('Name', 'site-reviews') => $review->author,
            __('Email', 'site-reviews') => $email,
            __('User', 'site-reviews') => $user,
            __('IP Address', 'site-reviews') => $review->ip_address,
            __('Avatar', 'site-reviews') => sprintf('<img src="%s" width="96">', $review->avatar),
        ];
        return array_filter(apply_filters('site-reviews/metabox/details', $metabox, $review));
    }

    /**
     * @param int $postId
     * @param int $messageIndex
     * @return void
     */
    protected function redirect($postId, $messageIndex)
    {
        $referer = wp_get_referer();
        $hasReferer = !$referer
            || Str::contains($referer, 'post.php')
            || Str::contains($referer, 'post-new.php');
        $redirectUri = $hasReferer
            ? remove_query_arg(['deleted', 'ids', 'trashed', 'untrashed'], $referer)
            : get_edit_post_link($postId);
        wp_safe_redirect(add_query_arg(['message' => $messageIndex], $redirectUri));
        exit;
    }
}
