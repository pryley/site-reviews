<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress;

use GeminiLabs\SiteReviews\Integrations\MultilingualPress\Defaults\SyncReviewDefaults;
use GeminiLabs\SiteReviews\Review;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\SwitchSiteTrait;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

use function Inpsyde\MultilingualPress\assignedLanguageTags;
use function Inpsyde\MultilingualPress\translationIds;

class ReviewCopier
{
    use SwitchSiteTrait;

    protected int $sourcePostId;
    protected int $sourceSiteId;

    public function __construct(int $sourcePostId, int $sourceSiteId)
    {
        $this->sourcePostId = $sourcePostId;
        $this->sourceSiteId = $sourceSiteId;
    }

    public function copy(): void
    {
        glsr(SyncState::class)->during(function () {
            $review = glsr_get_review($this->sourcePostId);
            $data = $this->copyData($review);
            $siteIds = assignedLanguageTags();
            foreach ($siteIds as $remoteSiteId => $tag) {
                if ($this->sourceSiteId === $remoteSiteId) {
                    continue;
                }
                if ($this->relationExists($remoteSiteId)) {
                    continue;
                }
                $originalSiteId = $this->maybeSwitchSite($remoteSiteId);
                if ($remoteReview = glsr_create_review($data)) {
                    $context = new RelationshipContext([
                        RelationshipContext::REMOTE_POST_ID => $remoteReview->ID,
                        RelationshipContext::REMOTE_SITE_ID => $remoteSiteId,
                        RelationshipContext::SOURCE_POST_ID => $this->sourcePostId,
                        RelationshipContext::SOURCE_SITE_ID => $this->sourceSiteId,
                    ]);
                    $helper = new RelationSaveHelper($context);
                    $helper->relateReviews();
                    $helper->syncMetadata();
                    $helper->syncAssignedPosts($review->assigned_posts, true);
                    $helper->syncAssignedUsers($review->assigned_users, true);
                    $helper->syncAssignedTerms();
                } else {
                    glsr_log()->error('MLP: unable to copy remote review')
                      ->debug($data);
                }
                $this->maybeRestoreSite($originalSiteId);
            }
        });
    }

    /**
     * Return the values a submission's copy is created from.
     */
    public function copyData(Review $review): array
    {
        $data = $review->toArray([
            'assigned_posts', // exclude
            'assigned_terms', // exclude
        ]);
        if (empty($data['terms'])) {
            unset($data['terms']); // see ReviewManager::duplicate()
        }
        // The custom field values under their own keys: CreateReview reads the top level and guards "custom".
        return wp_parse_args($data, $review->custom()->toArray());
    }

    public function run(\Closure $func): void
    {
        glsr(SyncState::class)->during(function () use ($func) {
            $translations = translationIds(
                $this->sourcePostId,
                ContentRelations::CONTENT_TYPE_POST,
                $this->sourceSiteId
            );
            foreach ($translations as $remoteSiteId => $remotePostId) {
                if ($this->sourceSiteId === $remoteSiteId) {
                    continue;
                }
                if (!$remotePostId > 0) {
                    continue;
                }
                $context = new RelationshipContext([
                    RelationshipContext::REMOTE_POST_ID => $remotePostId,
                    RelationshipContext::REMOTE_SITE_ID => $remoteSiteId,
                    RelationshipContext::SOURCE_POST_ID => $this->sourcePostId,
                    RelationshipContext::SOURCE_SITE_ID => $this->sourceSiteId,
                ]);
                $originalSiteId = $this->maybeSwitchSite($remoteSiteId);
                call_user_func($func, $context);
                $this->maybeRestoreSite($originalSiteId);
            }
        });
    }

    public function sync(): void
    {
        $data = $this->syncData(glsr_get_review($this->sourcePostId));
        $this->run(function ($context) use ($data) {
            $helper = new RelationSaveHelper($context);
            $helper->syncUpdate($data, true);
        });
    }

    /**
     * Return the values a front-end edit writes on the related copies.
     */
    public function syncData(Review $review): array
    {
        $data = wp_parse_args($review->toArray(), $review->custom()->toArray());
        // Guarded on the source site: the site-reviews/defaults/sync-review filter reads the review's form here.
        $data = glsr(SyncReviewDefaults::class)->merge($data);
        $data['assigned_posts'] = $review->assigned_posts; // the source ids mapped by syncUpdate() on each remote site
        $data['assigned_users'] = $review->assigned_users;
        return $data;
    }

    protected function relationExists(int $remoteSiteId): bool
    {
        $translations = translationIds($this->sourcePostId, ContentRelations::CONTENT_TYPE_POST, $this->sourceSiteId);
        return array_key_exists($remoteSiteId, $translations);
    }
}
