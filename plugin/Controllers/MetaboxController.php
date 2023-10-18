<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Metaboxes\AssignedPostsMetabox;
use GeminiLabs\SiteReviews\Metaboxes\AssignedUsersMetabox;
use GeminiLabs\SiteReviews\Metaboxes\AuthorMetabox;
use GeminiLabs\SiteReviews\Metaboxes\DetailsMetabox;
use GeminiLabs\SiteReviews\Metaboxes\ResponseMetabox;
use GeminiLabs\SiteReviews\Modules\Html\Template;

class MetaboxController extends Controller
{
    /**
     * @return array
     * @filter site-reviews/config/forms/metabox-fields
     */
    public function filterFieldOrder(array $config)
    {
        $order = array_keys($config);
        $order = glsr()->filterArray('metabox/fields/order', $order);
        return array_intersect_key(array_merge(array_flip($order), $config), $config);
    }

    /**
     * @param \WP_Post $post
     * @return void
     * @action add_meta_boxes_{glsr()->post_type}
     */
    public function registerMetaBoxes($post)
    {
        glsr(AssignedPostsMetabox::class)->register($post);
        glsr(AssignedUsersMetabox::class)->register($post);
        glsr(AuthorMetabox::class)->register($post);
        glsr(DetailsMetabox::class)->register($post);
        glsr(ResponseMetabox::class)->register($post);
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
     * @return void
     * @action post_submitbox_misc_actions
     */
    public function renderPinnedInPublishMetaBox()
    {
        $review = glsr(Query::class)->review(get_post()->ID ?? 0);
        if ($review->isValid() && glsr()->can('edit_others_posts')) {
            $context = [
                'no' => _x('No', 'admin-text', 'site-reviews'),
                'yes' => _x('Yes', 'admin-text', 'site-reviews'),
            ];
            glsr(Template::class)->render('partials/editor/pinned', [
                'context' => $context,
                'pinned' => $review->is_pinned,
            ]);
        }
    }

    /**
     * @return void
     * @action post_submitbox_misc_actions
     */
    public function renderVerifiedInPublishMetaBox()
    {
        $review = glsr(Query::class)->review(get_post()->ID ?? 0);
        if ($review->isValid()
            && glsr()->can('edit_others_posts')
            && glsr()->filterBool('verification/enabled', false)) {
            $context = [
                'no' => _x('No', 'admin-text', 'site-reviews'),
                'yes' => _x('Yes', 'admin-text', 'site-reviews'),
            ];
            glsr(Template::class)->render('partials/editor/verified', [
                'context' => $context,
                'verified' => $review->is_verified,
            ]);
        }
    }
}
