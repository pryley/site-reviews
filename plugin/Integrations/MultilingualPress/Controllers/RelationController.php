<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\RelationSaveHelper;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\ReviewCopier;
use GeminiLabs\SiteReviews\Review;
use Inpsyde\MultilingualPress\Framework\Admin\PersistentAdminNotices;
use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

class RelationController extends AbstractController
{
    /**
     * @param int[] $updatedPostIds
     * @action bulk_edit_posts
     */
    public function onBulkEditReviews(array $updatedPostIds, array $data): void
    {
        if (!$this->isReviewListTable()) {
            return;
        }
        error_log(print_r('onBulkEditReviews', true));
        $postIds = Arr::getAs('array', $data, 'post_ids');
        $userIds = Arr::getAs('array', $data, 'user_ids');
        $sourceSiteId = get_current_blog_id();
        foreach ($updatedPostIds as $sourcePostId) {
            $copier = new ReviewCopier($sourcePostId, $sourceSiteId);
            $copier->run(function ($context) use ($postIds, $userIds) {
                $relationHelper = new RelationSaveHelper($context);
                $relationHelper->syncAssignedPosts($postIds, true);
                $relationHelper->syncAssignedTerms();
                $relationHelper->syncAssignedUsers($userIds, true);
            });
        }
    }

    /**
     * @action site-reviews/review/pinned
     */
    public function onPinned(int $sourcePostId, bool $isPinned): void
    {
        if (!Review::isReview($sourcePostId)) {
            return;
        }
        error_log(print_r('onPinned', true));
        $sourceSiteId = get_current_blog_id();
        $copier = new ReviewCopier($sourcePostId, $sourceSiteId);
        $copier->run(function ($context) use ($isPinned) {
            $relationHelper = new RelationSaveHelper($context);
            $relationHelper->syncRating([
                'is_pinned' => $isPinned,
            ]);
        });
    }

    /**
     * The Remote site is the current site when using this hook.
     *
     * @action multilingualpress.metabox_after_relate_posts
     */
    public function onRelateReview(RelationshipContext $context, ServerRequest $request): void {
        if (glsr()->post_type !== $context->sourcePost()->post_type) {
            return;
        }
        error_log(print_r('onRelateReview', true));
        $sourceData = Arr::consolidate($request->bodyValue(glsr()->id));
        $sourcePostIds = Arr::consolidate($request->bodyValue('post_ids'));
        $sourceUserIds = Arr::consolidate($request->bodyValue('user_ids'));
        $relationHelper = new RelationSaveHelper($context);
        $relationHelper->syncRating($sourceData); // do this first in case it's a new review
        $relationHelper->syncAssignedPosts($sourcePostIds);
        $relationHelper->syncAssignedUsers($sourceUserIds);
    }

    /**
     * @action site-reviews/review/verified
     */
    public function onVerified(int $sourcePostId): void
    {
        if (!Review::isReview($sourcePostId)) {
            return;
        }
        error_log(print_r('onVerified', true));
        $sourceSiteId = get_current_blog_id();
        $timestamp = Cast::toInt(glsr(Database::class)->meta($sourcePostId, 'verified_on'));
        $copier = new ReviewCopier($sourcePostId, $sourceSiteId);
        $copier->run(function ($context) use ($timestamp) {
            $relationHelper = new RelationSaveHelper($context);
            $relationHelper->syncVerified($timestamp);
        });
    }
}
