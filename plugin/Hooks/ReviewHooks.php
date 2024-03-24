<?php

namespace GeminiLabs\SiteReviews\Hooks;

use GeminiLabs\SiteReviews\Controllers\ReviewController;
use GeminiLabs\SiteReviews\Database\Tables;

class ReviewHooks extends AbstractHooks
{
    public function hasPluginsLoaded(): bool
    {
        return true;
    }

    /**
     * MyISAM table fallback.
     */
    public function onPluginsLoaded(): void
    {
        if (!glsr(Tables::class)->isInnodb('posts')) {
            $this->hook(ReviewController::class, [
                ['onDeletePost', 'deleted_post', 10, 2],
            ]);
        }
        if (!glsr(Tables::class)->isInnodb('users')) {
            $this->hook(ReviewController::class, [
                ['onDeleteUser', 'deleted_user'],
            ]);
        }
    }

    public function run(): void
    {
        $this->hook(ReviewController::class, [
            ['filterPostsToCacheReviews', 'the_posts'],
            ['filterReviewPostData', 'wp_insert_post_data', 10, 2],
            ['filterReviewTemplate', 'site-reviews/rendered/template/review', 10, 2],
            ['filterSqlClauseOperator', 'site-reviews/query/sql/clause/operator', 1],
            ['filterTemplateTags', 'site-reviews/review/build/after', 10, 3],
            ['onAfterChangeAssignedTerms', 'set_object_terms', 10, 6],
            ['onAfterChangeStatus', 'transition_post_status', 10, 3],
            ['onApprove', 'admin_action_approve'], // non-ajax fallback
            ['onChangeAssignedPosts', 'site-reviews/review/updated/post_ids', 10, 2],
            ['onChangeAssignedUsers', 'site-reviews/review/updated/user_ids', 10, 2],
            ['onCreatedReview', 'site-reviews/review/created', 10, 2],
            ['onCreateReview', 'site-reviews/review/create', 10, 2],
            ['onEditReview', 'post_updated', 10, 3],
            ['onUnapprove', 'admin_action_unapprove'], // non-ajax fallback
            ['sendNotification', 'site-reviews/review/created', 50],
        ]);
    }
}
