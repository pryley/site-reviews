<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Review;
use Inpsyde\MultilingualPress\Core\TaxonomyRepository;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

use function Inpsyde\MultilingualPress\assignedLanguageTags;
use function Inpsyde\MultilingualPress\resolve;
use function Inpsyde\MultilingualPress\translationIds;

class ReviewConnector
{
    /**
     * @var ContentRelations
     */
    protected $contentRelations;

    public function __construct()
    {
        $this->contentRelations = resolve(ContentRelations::class);
    }

    /**
     * @throws \RuntimeException
     */
    public function copyReview(int $reviewId, CreateReview $command): void
    {
        $review = glsr_get_review($reviewId); // get a fresh copy of the review
        $siteIds = assignedLanguageTags(true, false);
        $sourceSiteId = get_current_blog_id();
        glsr()->store('multilingualpress', true);
        foreach ($siteIds as $remoteSiteId => $tag) {
            if ($this->connectionExistsInSite($review->ID, $sourceSiteId, $remoteSiteId)) {
                continue;
            }
            try {
                $insertedReviewId = $this->insertReview($review, $sourceSiteId, $remoteSiteId);
                $this->relateReviews(new RelationshipContext([
                    RelationshipContext::REMOTE_POST_ID => $insertedReviewId,
                    RelationshipContext::REMOTE_SITE_ID => $remoteSiteId,
                    RelationshipContext::SOURCE_POST_ID => $review->ID,
                    RelationshipContext::SOURCE_SITE_ID => $sourceSiteId,
                ]));
            } catch (\RuntimeException $exception) {
                glsr()->discard('multilingualpress');
                throw $exception;
            }
        }
        glsr()->discard('multilingualpress');
    }

    public function updateAssignedPosts(Review $review, array $postIds): void
    {
        $sourceSiteId = get_current_blog_id();
        $translatedIds = translationIds($review->ID, ContentRelations::CONTENT_TYPE_POST, $sourceSiteId);
        glsr()->store('multilingualpress.assigned_posts', true);
        foreach ($translatedIds as $remoteSiteId => $remotePostId) {
            if ($sourceSiteId === $remoteSiteId) {
                continue;
            }
            $remotePostIds = $this->remoteAssignedPosts($postIds, $sourceSiteId, $remoteSiteId);
            switch_to_blog($remoteSiteId);
            $remoteReview = glsr_get_review($remotePostId);
            glsr()->action('review/updated/post_ids', $remoteReview, $remotePostIds);
            restore_current_blog();
        }
        glsr()->discard('multilingualpress.assigned_posts');
    }

    public function updateAssignedUsers(Review $review, array $userIds): void
    {
        $sourceSiteId = get_current_blog_id();
        $translatedIds = translationIds($review->ID, ContentRelations::CONTENT_TYPE_POST, $sourceSiteId);
        glsr()->store('multilingualpress.assigned_users', true);
        foreach ($translatedIds as $remoteSiteId => $remotePostId) {
            if ($sourceSiteId === $remoteSiteId) {
                continue;
            }
            switch_to_blog($remoteSiteId);
            $remoteReview = glsr_get_review($remotePostId);
            glsr()->action('review/updated/user_ids', $remoteReview, $userIds);
            restore_current_blog();
        }
        glsr()->discard('multilingualpress.assigned_users');
    }

    protected function connectionExistsInSite(int $reviewId, int $sourceSiteId, int $remoteSiteId): bool
    {
        $translations = translationIds($reviewId, ContentRelations::CONTENT_TYPE_POST, $sourceSiteId);
        return array_key_exists($remoteSiteId, $translations);
    }

    /**
     * @throws \RuntimeException
     */
    protected function insertReview(Review $review, int $sourceSiteId, int $remoteSiteId): int
    {
        $data = $review->toArray();
        $data['assigned_posts'] = $this->remoteAssignedPosts(
            $data['assigned_posts'],
            $sourceSiteId,
            $remoteSiteId
        );
        $data['assigned_terms'] = $this->remoteAssignedTerms(
            $data['assigned_terms'],
            $sourceSiteId,
            $remoteSiteId
        );
        switch_to_blog($remoteSiteId);
        $copiedReview = glsr_create_review($data);
        restore_current_blog();
        if (false === $copiedReview) {
            throw new \RuntimeException('The review was not inserted.');
        }
        return $copiedReview->ID;
    }

    /**
     * @throws \RuntimeException
     */
    protected function relateReviews(RelationshipContext $context): void
    {
        $siteToReviewIdMap = [
            $context->sourceSiteId() => $context->sourcePostId(),
            $context->remoteSiteId() => $context->remotePostId(),
        ];
        $relationshipId = $this->contentRelations->relationshipId(
            $siteToReviewIdMap,
            ContentRelations::CONTENT_TYPE_POST,
            true
        );
        foreach ($siteToReviewIdMap as $siteId => $postId) {
            if (!$this->contentRelations->saveRelation($relationshipId, $siteId, $postId)) {
                throw new \RuntimeException("Couldn't save the relationship.");
            }
        }
    }

    protected function remoteAssignedPosts(array $postIds, int $sourceSiteId, int $remoteSiteId): array
    {
        $assignedPosts = [];
        foreach ($postIds as $postId) {
            $translations = translationIds($postId, ContentRelations::CONTENT_TYPE_POST, $sourceSiteId);
            $remotePostId = $translations[$remoteSiteId] ?? 0;
            if (!$remotePostId > 0) {
                continue;
            }
            $assignedPosts[] = $remotePostId;
        }
        return $assignedPosts;
    }

    protected function remoteAssignedTerms(array $termIds, int $sourceSiteId, int $remoteSiteId): array
    {
        if (!resolve(TaxonomyRepository::class)->isTaxonomyActive(glsr()->taxonomy)) {
            return [];
        }
        $assignedTerms = [];
        foreach ($termIds as $termId) {
            $translations = translationIds($termId, ContentRelations::CONTENT_TYPE_TERM, $sourceSiteId);
            $remoteTermId = $translations[$remoteSiteId] ?? 0;
            if (!$remoteTermId > 0) {
                continue;
            }
            $assignedTerms[] = $remoteTermId;
        }
        return $assignedTerms;
    }
}
