<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\RatingDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\Metabox\MetaboxFields;
use GeminiLabs\SiteReviews\Modules\Date;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Review;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;
use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post\PostRelationSaveHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

use function Inpsyde\MultilingualPress\resolve;
use function Inpsyde\MultilingualPress\translationIds;

class RelationSaveHelper
{
    /**
     * @var ContentRelations
     */
    protected $contentRelations;

    /**
     * @var RelationshipContext
     */
    protected $context;

    /**
     * @var Review
     */
    protected $review;

    public function __construct(RelationshipContext $context)
    {
        $this->contentRelations = resolve(ContentRelations::class);
        $this->context = $context;
        $this->review = glsr_get_review($context->remotePostId());
    }

    public function relateReviews(): void
    {
        $siteToReviewIdMap = [
            $this->context->sourceSiteId() => $this->context->sourcePostId(),
            $this->context->remoteSiteId() => $this->context->remotePostId(),
        ];
        $relationshipId = $this->contentRelations->relationshipId(
            $siteToReviewIdMap,
            ContentRelations::CONTENT_TYPE_POST,
            true
        );
        foreach ($siteToReviewIdMap as $siteId => $postId) {
            if (!$this->contentRelations->saveRelation($relationshipId, $siteId, $postId)) {
                glsr_log()->error('MLP: relate reviews failed')
                    ->debug($this->context);
            }
        }
    }

    public function syncAssignedPosts(array $postIds, bool $force = false)
    {
        if (!$this->canSync()) {
            return;
        }
        if (!$force && !$this->canSyncAssignment(MetaboxFields::FIELD_COPY_ASSIGNED_POSTS)) {
            return;
        }
        if ($postIds = $this->remoteAssignedPosts($postIds)) {
            glsr()->action('review/updated/post_ids', $this->review, $postIds);
        }
    }

    public function syncAssignedTerms()
    {
        $helper = new PostRelationSaveHelper($this->contentRelations);
        $helper->syncTaxonomyTerms($this->context);
    }

    public function syncAssignedUsers(array $userIds, bool $force = false)
    {
        if (!$this->canSync()) {
            return;
        }
        if (!$force && !$this->canSyncAssignment(MetaboxFields::FIELD_COPY_ASSIGNED_USERS)) {
            return;
        }
        if ($userIds = $this->remoteAssignedUsers($userIds)) {
            glsr()->action('review/updated/user_ids', $this->review, $userIds);
        }
    }

    public function syncRating(array $data)
    {
        if (!$this->canSync()) {
            return;
        }
        if (!$this->review->isValid()) {
            $data['review_id'] = $this->context->remotePostId();
            $data = glsr(RatingDefaults::class)->restrict($data);
            if (false === glsr(Database::class)->insert('ratings', $data)) {
                glsr_log()->error('MLP: insert rating failed')
                    ->debug($data)
                    ->debug($this->context);
            }
            return;
        }
        if (-1 === glsr(ReviewManager::class)->updateRating($this->review->ID, $data)) {
            glsr_log()->error('MLP: update rating failed')
                ->debug($data)
                ->debug($this->context);
        }
    }

    public function syncVerified(int $timestamp)
    {
        if (!$this->canSync()) {
            return;
        }
        if (!$this->review->isValid()) {
            return;
        }
        if ($this->review->is_verified) {
            return;
        }
        if (!glsr(Date::class)->isTimestamp($timestamp)) {
            return;
        }
        $result = glsr(ReviewManager::class)->updateRating($this->review->ID, [
            'is_verified' => true,
        ]);
        if ($result > 0) {
            glsr(Database::class)->metaSet($this->review->ID, 'verified_on', $timestamp);
            return;
        }
        glsr_log()->error('MLP: sync review verification failed')
            ->debug(compact('timestamp'))
            ->debug($this->context);
    }

    protected function canSync(): bool
    {
        $sourceSiteId = $this->context->sourceSiteId();
        $remoteSiteId = $this->context->remoteSiteId();
        if ($sourceSiteId === $remoteSiteId) {
            return false;
        }
        if (!$this->context->hasRemotePost()) {
            return false;
        }
        if (glsr()->post_type !== $this->context->remotePost()->post_type) {
            return false;
        }
        return true;
    }

    protected function canSyncAssignment(string $key): bool
    {
        $siteId = $this->context->remoteSiteId();
        $values = resolve(ServerRequest::class)->bodyValue(MetaboxFieldsHelper::NAME_PREFIX);
        return Arr::getAs('bool', $values, "site-{$siteId}.{$key}");
    }

    protected function remoteAssignedPosts(array $postIds): array
    {
        $assignedPosts = [];
        $postIds = glsr(Sanitizer::class)->sanitizeArrayInt($postIds);
        $remoteSiteId = $this->context->remoteSiteId();
        $sourceSiteId = $this->context->sourceSiteId();
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

    protected function remoteAssignedTerms(array $termIds): array
    {
        $termIds = glsr(Sanitizer::class)->sanitizeArrayInt($termIds);
        return $termIds;
    }

    protected function remoteAssignedUsers(array $userIds): array
    {
        $userIds = glsr(Sanitizer::class)->sanitizeUserIds($userIds);
        return $userIds;
    }
}
