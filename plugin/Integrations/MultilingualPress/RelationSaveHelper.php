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
     * @var ServerRequest
     */
    protected $request;

    /**
     * @var Review
     */
    protected $review;

    public function __construct(RelationshipContext $context)
    {
        $this->contentRelations = resolve(ContentRelations::class);
        $this->context = $context;
        $this->request = resolve(ServerRequest::class);
        $this->review = glsr_get_review($context->remotePostId());
    }

    public function syncAssignedPosts(array $postIds)
    {
        if (!$this->canSync()) {
            return;
        }
        if (!$this->canSyncAssignment(MetaboxFields::FIELD_COPY_ASSIGNED_POSTS)) {
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

    public function syncAssignedUsers(array $userIds, $bulkEdit = false)
    {
        if (!$this->canSync()) {
            return;
        }
        if (!$this->canSyncAssignment(MetaboxFields::FIELD_COPY_ASSIGNED_USERS)) {
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
        if ($sourceSiteId === $remoteSiteId || !$this->context->hasRemotePost()) {
            return false;
        }
        if (!Review::isReview($this->context->remotePostId())) {
            return false;
        }
        return true;
    }

    protected function canSyncAssignment(string $key): bool
    {
        if (!empty($this->request->bodyValue('bulk_edit'))) {
            return true;
        }
        $siteId = $this->context->remoteSiteId();
        $values = $this->request->bodyValue(MetaboxFieldsHelper::NAME_PREFIX);
        return Arr::getAs('bool', $values, "site-{$siteId}.{$key}");
    }

    protected function maybeRestoreSite(int $originalSiteId): bool
    {
        if ($originalSiteId < 0) {
            return false;
        }
        restore_current_blog();
        $currentSite = get_current_blog_id();
        if ($currentSite !== $originalSiteId) {
            switch_to_blog($originalSiteId);
        }
        return true;
    }

    protected function maybeSwitchSite(int $remoteSiteId): int
    {
        $currentSite = get_current_blog_id();
        if ($currentSite !== $remoteSiteId) {
            switch_to_blog($remoteSiteId);
            return $currentSite;
        }
        return -1;
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
