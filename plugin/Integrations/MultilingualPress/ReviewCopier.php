<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\Defaults\UpdateReviewDefaults;
use GeminiLabs\SiteReviews\Review;
use Inpsyde\MultilingualPress\Core\TaxonomyRepository;
use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;
use function Inpsyde\MultilingualPress\assignedLanguageTags;
use function Inpsyde\MultilingualPress\resolve;
use function Inpsyde\MultilingualPress\translationIds;

class ReviewCopier
{
    protected int $sourcePostId;
    protected int $sourceSiteId;

    public function __construct(int $sourcePostId, int $sourceSiteId)
    {
        $this->sourcePostId = $sourcePostId;
        $this->sourceSiteId = $sourceSiteId;
    }

    public function copy(): void
    {
        $review = glsr_get_review($this->sourcePostId);
        $data = $review->toArray();
        unset($data['assigned_posts']);
        unset($data['assigned_terms']);
        $siteIds = assignedLanguageTags();
        foreach ($siteIds as $remoteSiteId => $tag) {
            if ($this->sourceSiteId === $remoteSiteId) {
                continue;
            }
            if ($this->relationExists($remoteSiteId)) {
                continue;
            }
            $originalSiteId = $this->maybeSwitchSite($remoteSiteId);
            $remoteReview = glsr_create_review($data);
            $context = new RelationshipContext([
                RelationshipContext::REMOTE_POST_ID => $remoteReview->ID,
                RelationshipContext::REMOTE_SITE_ID => $remoteSiteId,
                RelationshipContext::SOURCE_POST_ID => $this->sourcePostId,
                RelationshipContext::SOURCE_SITE_ID => $this->sourceSiteId,
            ]);
            $helper = new RelationSaveHelper($context);
            $helper->relateReviews();
            $helper->syncAssignedPosts($review->assigned_posts, true);
            $helper->syncAssignedUsers($review->assigned_users, true);
            $helper->syncAssignedTerms();
            $this->maybeRestoreSite($originalSiteId);
        }
    }

    public function run(\Closure $func): void
    {
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
    }

    public function sync(): void
    {
        $review = glsr_get_review($this->sourcePostId);
        $data = $review->toArray();
        $data = glsr(UpdateReviewDefaults::class)->merge($data);
        $this->run(function ($context) use ($data, $review) {
            $remoteReview = glsr_update_review($context->remotePostId(), $data);
            $helper = new RelationSaveHelper($context);
            $helper->syncAssignedPosts($review->assigned_posts, true);
            $helper->syncAssignedTerms();
            $helper->syncAssignedUsers($review->assigned_users, true);
        });
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

    protected function relationExists(int $remoteSiteId): bool
    {
        $translations = translationIds($this->sourcePostId, ContentRelations::CONTENT_TYPE_POST, $this->sourceSiteId);
        return array_key_exists($remoteSiteId, $translations);
    }
}
