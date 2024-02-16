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

class ReviewCopier
{
    protected int $sourcePostId;
    protected int $sourceSiteId;

    public function __construct(int $sourcePostId, int $sourceSiteId)
    {
        $this->sourcePostId = $sourcePostId;
        $this->sourceSiteId = $sourceSiteId;
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
            switch_to_blog($remoteSiteId);
            call_user_func($func, $context);
            $this->maybeRestoreSite($this->sourceSiteId);
        }
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
}
