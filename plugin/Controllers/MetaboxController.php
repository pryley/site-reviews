<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Metaboxes\AssignedPostsMetabox;
use GeminiLabs\SiteReviews\Metaboxes\AssignedUsersMetabox;
use GeminiLabs\SiteReviews\Metaboxes\AuthorMetabox;
use GeminiLabs\SiteReviews\Metaboxes\DetailsMetabox;
use GeminiLabs\SiteReviews\Metaboxes\ResponseMetabox;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Review;

class MetaboxController extends AbstractController
{
    /**
     * @filter site-reviews/metabox-form/fields
     */
    public function filterFieldOrder(array $config): array
    {
        $order = array_keys($config);
        $order = glsr()->filterArray('metabox-form/fields/order', $order);
        $ordered = array_intersect_key(array_merge(array_flip($order), $config), $config);
        return $ordered;
    }

    /**
     * @action add_meta_boxes_{glsr()->post_type}
     */
    public function registerMetaBoxes(\WP_Post $post): void
    {
        glsr(AssignedPostsMetabox::class)->register($post);
        glsr(AssignedUsersMetabox::class)->register($post);
        glsr(AuthorMetabox::class)->register($post);
        glsr(DetailsMetabox::class)->register($post);
        glsr(ResponseMetabox::class)->register($post);
    }

    /**
     * @action do_meta_boxes
     */
    public function removeMetaBoxes(string $postType): void
    {
        if (glsr()->post_type !== $postType) {
            return;
        }
        remove_meta_box('authordiv', $postType, 'normal');
        remove_meta_box('slugdiv', $postType, 'normal');
    }

    /**
     * @action post_submitbox_misc_actions
     */
    public function renderMiscActions(\WP_Post $post): void
    {
        if (!Review::isReview($post)) {
            return;
        }
        $review = glsr(ReviewManager::class)->get($post->ID);
        if (!$review->isValid()) {
            return;
        }
        $this->renderPinnedAction($review);
        $this->renderVerifiedAction($review);
    }

    protected function renderPinnedAction(Review $review): void
    {
        glsr(Template::class)->render('partials/editor/pinned', [
            'is_pinned' => $review->is_pinned,
        ]);
    }

    protected function renderVerifiedAction(Review $review): void
    {
        if (!glsr()->filterBool('verification/enabled', false)) {
            return;
        }
        glsr(Template::class)->render('partials/editor/verified', [
            'is_verified' => $review->is_verified,
        ]);
    }
}
