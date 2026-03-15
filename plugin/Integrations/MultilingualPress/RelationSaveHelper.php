<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\PostMeta;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\RatingDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\Defaults\SyncReviewDefaults;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\MetaboxFields\AssignedPostsField;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\MetaboxFields\AssignedUsersField;
use GeminiLabs\SiteReviews\Modules\Date;
use GeminiLabs\SiteReviews\Modules\Html\MetaboxForm;
use GeminiLabs\SiteReviews\Modules\Sanitizer;
use GeminiLabs\SiteReviews\Review;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;
use Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
use Inpsyde\MultilingualPress\TranslationUi\MetaboxFieldsHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post\PostRelationSaveHelper;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

use function Inpsyde\MultilingualPress\resolve;
use function Inpsyde\MultilingualPress\translationIds;

class RelationSaveHelper
{
    use SwitchSiteTrait;

    protected ContentRelations $contentRelations;
    protected RelationshipContext $context;

    public function __construct(RelationshipContext $context)
    {
        $this->contentRelations = resolve(ContentRelations::class);
        $this->context = $context;
    }

    public function relateReviews(): void
    {
        $siteToPostIdMap = [
            $this->context->sourceSiteId() => $this->context->sourcePostId(),
            $this->context->remoteSiteId() => $this->context->remotePostId(),
        ];
        $relationshipId = $this->contentRelations->relationshipId(
            $siteToPostIdMap,
            ContentRelations::CONTENT_TYPE_POST,
            true
        );
        foreach ($siteToPostIdMap as $siteId => $postId) {
            if (!$this->contentRelations->saveRelation($relationshipId, $siteId, $postId)) {
                glsr_log()->error('MLP: relate reviews failed')
                          ->debug($this->context);
            }
        }
    }

    /**
     * @todo Should all assigned_posts be removed from remote if $sourcePostIds is empty?
     */
    public function syncAssignedPosts(array $sourcePostIds, bool $force = false): void
    {
        if (!$this->canSync()) {
            return;
        }
        if (!$force && !$this->canSyncAssignment(AssignedPostsField::FIELD_COPY_ASSIGNED_POSTS)) {
            return;
        }
        $review = $this->remoteReview();
        if (!$review->isValid()) {
            return;
        }
        if ($remotePostIds = $this->remoteAssignedPosts($sourcePostIds)) {
            $originalSiteId = $this->maybeSwitchSite($this->context->remoteSiteId());
            glsr()->action('review/updated/post_ids', $review, $remotePostIds);
            $this->maybeRestoreSite($originalSiteId);
        }
    }

    public function syncAssignedTerms(): void
    {
        $helper = new PostRelationSaveHelper($this->contentRelations);
        $helper->syncTaxonomyTerms($this->context);
    }

    /**
     * @todo Should all assigned_users be removed from remote if $sourceUserIds is empty?
     */
    public function syncAssignedUsers(array $sourceUserIds, bool $force = false): void
    {
        if (!$this->canSync()) {
            return;
        }
        if (!$force && !$this->canSyncAssignment(AssignedUsersField::FIELD_COPY_ASSIGNED_USERS)) {
            return;
        }
        $review = $this->remoteReview();
        if (!$review->isValid()) {
            return;
        }
        if ($remoteUserIds = $this->remoteAssignedUsers($sourceUserIds)) {
            $originalSiteId = $this->maybeSwitchSite($this->context->remoteSiteId());
            glsr()->action('review/updated/user_ids', $review, $remoteUserIds);
            $this->maybeRestoreSite($originalSiteId);
        }
    }

    public function syncAuthor(int $userId): void
    {
        if ($userId === (int) $this->context->remotePost()->post_author) {
            return;
        }
        $originalSiteId = $this->maybeSwitchSite($this->context->remoteSiteId());
        glsr(Database::class)->update('posts',
            ['post_author' => $userId],
            ['ID' => $this->context->remotePostId()]
        );
        $this->maybeRestoreSite($originalSiteId);
    }

    public function syncGeolocation(array $data): void
    {
        if (!$this->canSync()) {
            return;
        }
        $review = $this->remoteReview();
        if (!$review->isValid()) {
            return;
        }
        $originalSiteId = $this->maybeSwitchSite($this->context->remoteSiteId());
        $data['rating_id'] = $review->rating_id;
        if (false !== glsr(Database::class)->insert('stats', $data)) {
            unset($data['rating_id']);
            glsr(PostMeta::class)->set($review->ID, 'geolocation', $data);
        } else {
            glsr_log()->error('MLP: insert geolocation failed on remote site')
                      ->debug($data)
                      ->debug($this->context);
        }
        $this->maybeRestoreSite($originalSiteId);
    }

    public function syncMeta(): void
    {
        // @todo
    }

    public function syncRating(array $sourceData): void
    {
        if (!$this->canSync()) {
            return;
        }
        $review = $this->remoteReview();
        $originalSiteId = $this->maybeSwitchSite($this->context->remoteSiteId());
        if (!$review->isValid()) {
            // If review details are not toggled on for editing,
            // then we need to fetch the values from the source review.
            if ('on' !== ($sourceData['is_editing_review'] ?? null)) {
                $sourceReview = $this->sourceReview();
                $metaboxConfig = (new MetaboxForm($sourceReview))->config();
                $sourceData = wp_parse_args($sourceData,
                    array_intersect_key($sourceReview->toArray(), $metaboxConfig)
                );
            }
            $sourceData['review_id'] = $this->context->remotePostId();
            $sourceData = glsr(RatingDefaults::class)->restrict($sourceData);
            if (false === glsr(Database::class)->insert('ratings', $sourceData)) {
                glsr_log()->error('MLP: insert rating failed on remote site')
                          ->debug($sourceData)
                          ->debug($this->context);
            }
        } else {
            if (-1 === glsr(ReviewManager::class)->updateRating($review->ID, $sourceData)) {
                glsr_log()->error('MLP: update rating failed on remote site')
                          ->debug($sourceData)
                          ->debug($this->context);
            }
        }
        $this->maybeRestoreSite($originalSiteId);
    }

    public function syncResponse(string $response): void
    {
        if (!$this->canSync()) {
            return;
        }
        $review = $this->remoteReview();
        if (!$review->isValid()) {
            return;
        }
        $originalSiteId = $this->maybeSwitchSite($this->context->remoteSiteId());
        if (empty($review->response)) {
            glsr(PostMeta::class)->set($review->ID, 'response', $response);
            glsr(PostMeta::class)->set($review->ID, 'response_by', get_current_user_id());
        }
        $this->maybeRestoreSite($originalSiteId);
    }

    public function syncUpdate(array $data, bool $force = false): void
    {
        if (!$this->canSync()) {
            return;
        }
        $guarded = glsr(SyncReviewDefaults::class)->merge($data);
        if ($force || $this->canSyncAssignment(AssignedPostsField::FIELD_COPY_ASSIGNED_POSTS)) {
            $sourcePostIds = Arr::consolidate($data['assigned_posts'] ?? []);
            $guarded['assigned_posts'] = $this->remoteAssignedPosts($sourcePostIds);
        }
        if ($force || $this->canSyncAssignment(AssignedUsersField::FIELD_COPY_ASSIGNED_USERS)) {
            $sourceUserIds = Arr::consolidate($data['assigned_users'] ?? []);
            $guarded['assigned_users'] = $this->remoteAssignedUsers($sourceUserIds);
        }
        $originalSiteId = $this->maybeSwitchSite($this->context->remoteSiteId());
        glsr_update_review($this->context->remotePostId(), $guarded);
        $this->syncAssignedTerms();
        $this->maybeRestoreSite($originalSiteId);
    }

    public function syncVerified(int $timestamp): void
    {
        if (!$this->canSync()) {
            return;
        }
        if (!glsr(Date::class)->isTimestamp($timestamp)) {
            return;
        }
        $review = $this->remoteReview();
        if (!$review->isValid() || $review->is_verified) {
            return;
        }
        $originalSiteId = $this->maybeSwitchSite($this->context->remoteSiteId());
        $result = glsr(ReviewManager::class)->updateRating($review->ID, [
            'is_verified' => true,
        ]);
        if ($result > 0) {
            glsr(PostMeta::class)->set($review->ID, 'verified_on', $timestamp);
        } else {
            glsr_log()->error('MLP: sync review verification failed on remote site')
                ->debug(compact('timestamp'))
                ->debug($this->context);
        }
        $this->maybeRestoreSite($originalSiteId);
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

    protected function remoteAssignedPosts(array $sourcePostIds): array
    {
        $postIds = [];
        $remoteSiteId = $this->context->remoteSiteId();
        $sourceSiteId = $this->context->sourceSiteId();
        $sourcePostIds = glsr(Sanitizer::class)->sanitizeArrayInt($sourcePostIds);
        foreach ($sourcePostIds as $sourcePostId) {
            $translations = translationIds($sourcePostId, ContentRelations::CONTENT_TYPE_POST, $sourceSiteId);
            $remotePostId = $translations[$remoteSiteId] ?? 0;
            if (!$remotePostId > 0) {
                continue;
            }
            $postIds[] = $remotePostId;
        }
        return $postIds;
    }

    protected function remoteAssignedUsers(array $sourceUserIds): array
    {
        $userIds = [];
        foreach ($sourceUserIds as $userId) {
            if (is_user_member_of_blog($userId, $this->context->remoteSiteId())) {
                $userIds[] = (int) $userId;
            }
        }
        return $userIds;
    }

    protected function remoteReview(): Review
    {
        $originalSiteId = $this->maybeSwitchSite($this->context->remoteSiteId());
        $review = glsr_get_review($this->context->remotePostId());
        $this->maybeRestoreSite($originalSiteId);
        return $review;
    }

    protected function sourceReview(): Review
    {
        $originalSiteId = $this->maybeSwitchSite($this->context->sourceSiteId());
        $review = glsr_get_review($this->context->sourcePostId());
        $this->maybeRestoreSite($originalSiteId);
        return $review;
    }
}
