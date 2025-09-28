<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\PostMeta;
use GeminiLabs\SiteReviews\Defaults\AdditionalFieldsDefaults;
use GeminiLabs\SiteReviews\Defaults\StatDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\RelationSaveHelper;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\ReviewCopier;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Review;
use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

class RelationController extends AbstractController
{
    /**
     * Field keys from `AdditionalFieldsDefaults::class` should not be synced
     * automatically because we need to prevent translations with empty field
     * values from overwriting a source review containing filled field values.
     *
     * @filter multilingualpress.sync_post_meta_keys
     *
     * @filter-location \Inpsyde\MultilingualPress\TranslationUi\Post\PostRelationSaveHelper
     */
    public function filterSyncedMetaKeys(array $keys, RelationshipContext $context): array
    {
        $sourcePostId = $context->sourcePostId();
        if (!Review::isReview($sourcePostId)) {
            return $keys;
        }
        $metaKeys = [
            '_submitted_hash', // sync this because it's used when importing reviews
        ];
        foreach ($metaKeys as $key) {
            if (metadata_exists('post', $sourcePostId, $key)) {
                $keys[] = $key;
            }
        }
        return $keys;
    }

    /**
     * @action bulk_edit_posts
     */
    public function onBulkEditReviews(array $updatedPostIds, array $data): void
    {
        if (!$this->isReviewListTable()) {
            return;
        }
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
     * @action site-reviews/review/created
     */
    public function onCreated(Review $review): void
    {
        if ($this->isReviewEditor()) {
            return; // review was created on the wp_admin side
        }
        if (glsr()->retrieve('glsr_create_review', false)) {
            return; // review was created with the helper function
        }
        update_post_meta($review->ID, '_trash_the_other_posts', 1); // sync review deletion
        $copier = new ReviewCopier($review->ID, get_current_blog_id());
        $copier->copy();
    }

    /**
     * @action site-reviews/review/geolocated
     */
    public function onGeolocated(Review $review, array $data): void
    {
        $data = glsr(StatDefaults::class)->restrict($data);
        $copier = new ReviewCopier($review->ID, get_current_blog_id());
        $copier->run(function ($context) use ($data) {
            $relationHelper = new RelationSaveHelper($context);
            $relationHelper->syncGeolocation($data);
        });
    }

    /**
     * @action site-reviews/review/pinned
     */
    public function onPinned(Review $review, bool $isPinned): void
    {
        $copier = new ReviewCopier($review->ID, get_current_blog_id());
        $copier->run(function ($context) use ($isPinned) {
            $relationHelper = new RelationSaveHelper($context);
            $relationHelper->syncRating([
                'is_pinned' => $isPinned,
            ]);
        });
    }

    /**
     * @action site-reviews/review/responded
     */
    public function onResponded(Review $review, string $response): void
    {
        $response = glsr(Sanitizer::class)->sanitizeTextHtml($response);
        $copier = new ReviewCopier($review->ID, get_current_blog_id());
        $copier->run(function ($context) use ($response) {
            $relationHelper = new RelationSaveHelper($context);
            $relationHelper->syncResponse($response);
        });
    }

    /**
     * The Remote site is the current site when using this hook.
     *
     * @action multilingualpress.metabox_after_relate_posts
     *
     * @filter-location \Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxAction
     */
    public function onSyncReview(RelationshipContext $context, ServerRequest $request): void
    {
        if (glsr()->post_type !== $context->sourcePost()->post_type) {
            return;
        }
        $sourceData = Arr::consolidate($request->bodyValue(glsr()->id));
        $sourcePostIds = Arr::consolidate($request->bodyValue('post_ids'));
        $sourceUserIds = Arr::consolidate($request->bodyValue('user_ids'));
        $relationHelper = new RelationSaveHelper($context);
        $relationHelper->syncRating($sourceData); // do this first in case it's a new review
        $relationHelper->syncAssignedPosts($sourcePostIds);
        $relationHelper->syncAssignedUsers($sourceUserIds);
    }

    /**
     * @action site-reviews/review/transitioned
     */
    public function onTransitioned(Review $review, string $status, string $prevStatus): void
    {
        if ($this->isReviewEditor()) {
            return; // review status was updated on the wp_admin side
        }
        if (!empty(array_diff([$status, $prevStatus], ['pending', 'publish']))) {
            return; // only sync the pending|publish (unapproved|approved) statuses
        }
        $copier = new ReviewCopier($review->ID, get_current_blog_id());
        $copier->run(function ($context) use ($prevStatus, $status) {
            if ($prevStatus !== get_post_status($context->remotePostId())) {
                return; // only sync the status if it was previously in sync
            }
            wp_update_post([
                'ID' => $context->remotePostId(),
                'post_status' => $status,
            ]);
        });
    }

    /**
     * @action site-reviews/review/updated
     */
    public function onUpdated(Review $review): void
    {
        if ($this->isReviewEditor()) {
            return; // review was updated on the wp_admin side
        }
        if (glsr()->retrieve('glsr_update_review', false)) {
            return; // review was updated with the helper function
        }
        $copier = new ReviewCopier($review->ID, get_current_blog_id());
        $copier->sync();
    }

    /**
     * @action site-reviews/review/verified
     */
    public function onVerified(Review $review): void
    {
        $timestamp = glsr(PostMeta::class)->get($review->ID, 'verified_on', 'int');
        $copier = new ReviewCopier($review->ID, get_current_blog_id());
        $copier->run(function ($context) use ($timestamp) {
            $relationHelper = new RelationSaveHelper($context);
            $relationHelper->syncVerified($timestamp);
        });
    }
}
