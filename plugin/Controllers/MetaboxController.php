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
    public function renderPinnedAction(\WP_Post $post): void
    {
        if (!Review::isReview($post)) {
            return;
        }
        $review = glsr(ReviewManager::class)->get($post->ID);
        if (!$review->isValid()) {
            return;
        }
        glsr(Template::class)->render('partials/editor/pinned', [
            'is_pinned' => $review->is_pinned,
        ]);
    }
}
