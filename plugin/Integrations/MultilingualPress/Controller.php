<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Review;
use Inpsyde\MultilingualPress\Framework\Http\ServerRequest;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext;

class Controller extends AbstractController
{
    /**
     * @filter site-reviews/enqueue/admin/inline-styles
     */
    public function filterAdminInlineCss(string $css): string
    {
        $custom = '';
        if ($this->isReviewEditor()) {
            $custom .= '.mlp-translation-metabox[data-post-type="site-review"]{margin:9px 0 0}';
            $custom .= 'tr.mlp-taxonomy-box>td{padding:0}';
            $custom .= 'tr.mlp-taxonomy-box>td>ul{border:solid 1px #dcdcde;margin-bottom:.9em}';
        }
        if ($this->isListTable()) {
            $custom .= 'td.column-translations{align-items:center;display:flex;flex-wrap:wrap;gap:8px}';
            $custom .= 'td.column-translations .mlp-table-list-relations-divide{display:none!important}';
        }
        return $css.$custom;
    }

    /**
     * @filter site-reviews/enqueue/admin/inline-script
     */
    public function filterAdminInlineJs(string $js): string
    {
        if (!$this->isReviewEditor()) {
            return $js;
        }
        $relation = '$(".post-new-php .tab-relation input[value=new]").prop("checked",true);';
        $taxonomy = '$(".mlp-taxonomy-sync:has(input:checked)").closest("table").find(".mlp-taxonomy-box").hide();';
        $trasher = '$(".post-new-php #mlp-trasher").prop("checked",true);';
        return sprintf('%sjQuery(function($){%s%s%s});', $js, $relation, $taxonomy, $trasher);
    }

    /**
     * @filter multilingualpress.copy_content_is_checked
     */
    public function filterContentIsChecked(bool $isChecked): bool
    {
        global $pagenow;
        if (!$this->isReviewEditor()) {
            return $isChecked;
        }
        if ('post-new.php' !== $pagenow) {
            return $isChecked;
        }
        return true;
    }

    /**
     * @filter gettext_multilingualpress
     */
    public function filterGettext(string $translation, string $single): string
    {
        if (!$this->isReviewEditor()) {
            return $translation;
        }
        $translations = [
            'Overwrites content on translated post with the content of source post.' => _x('Overwrite the content of the translated review with the content of the source review.', 'multilingualpress text (admin-text)', 'site-reviews'),
            'Overwrites the target post taxonomy terms with the translation of terms assigned to source post.' => _x('Overwrite the Categories of the target review with the translated Categories of the source review.', 'multilingualpress text (admin-text)', 'site-reviews'),
            'Post Slug:' => _x('Review Slug:', 'multilingualpress text (admin-text)', 'site-reviews'),
            'Post Status:' => _x('Review Status:', 'multilingualpress text (admin-text)', 'site-reviews'),
            'Post Title:' => _x('Review Title:', 'multilingualpress text (admin-text)', 'site-reviews'),
            'Send all the translations to trash when this post is trashed.' => _x('Send all translations to trash when this review is trashed.', 'multilingualpress text (admin-text)', 'site-reviews'),
            'Synchronize Taxonomies' => _x('Synchronize Categories', 'multilingualpress text (admin-text)', 'site-reviews'),
        ];
        if (array_key_exists($single, $translations)) {
            return $translations[$single];
        }
        return $translation;
    }

    /**
     * @filter multilingualpress.translation_ui_post_statuses
     */
    public function filterPostStatuses(array $postStatuses): array
    {
        if (!$this->isReviewEditor()) {
            return $postStatuses;
        }
        global $wp_post_statuses;
        $wp_post_statuses['pending']->label = _x('Unapproved', 'admin-text', 'site-reviews');
        $wp_post_statuses['publish']->label = _x('Approved', 'admin-text', 'site-reviews');
        return ['draft', 'pending', 'publish'];
    }

    /**
     * @filter multilingualpress.copy_taxonomies_is_checked
     */
    public function filterTaxonomiesIsChecked(bool $isChecked): bool
    {
        if (!$this->isReviewEditor()) {
            return $isChecked;
        }
        return true;
    }

    /**
     * @param int[] $updatedPostIds
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
    public function onCreatedReview(Review $review): void
    {
        if (glsr()->isAdmin()) {
            return;
        }
        if (glsr()->retrieve('glsr_create_review', false)) {
            return;
        }
        $sourcePostId = $review->ID;
        $sourceSiteId = get_current_blog_id();
        $copier = new ReviewCopier($sourcePostId, $sourceSiteId);
        $copier->copy();
    }

    /**
     * @action site-reviews/review/pinned
     */
    public function onPinned(int $sourcePostId, bool $isPinned): void
    {
        if (!Review::isReview($sourcePostId)) {
            return;
        }
        $sourceSiteId = get_current_blog_id();
        $copier = new ReviewCopier($sourcePostId, $sourceSiteId);
        $copier->run(function ($context) use ($isPinned) {
            $relationHelper = new RelationSaveHelper($context);
            $relationHelper->syncRating(['is_pinned' => $isPinned]);
        });
    }

    /**
     * @action site-reviews/review/transitioned
     */
    public function onTransitioned(Review $review, string $status, string $prevStatus): void
    {
        if ($this->isReviewEditor()) {
            return;
        }
        if (!empty(array_diff([$status, $prevStatus], ['pending', 'publish']))) {
            return;
        }
        $sourcePostId = $review->ID;
        $sourceSiteId = get_current_blog_id();
        $copier = new ReviewCopier($sourcePostId, $sourceSiteId);
        $copier->run(function ($context) use ($prevStatus, $status) {
            if ($prevStatus !== get_post_status($context->remotePostId())) {
                return;
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
    public function onUpdatedReview(Review $review): void
    {
        if (glsr()->isAdmin()) {
            return;
        }
        if (glsr()->retrieve('glsr_update_review', false)) {
            return;
        }
        $sourcePostId = $review->ID;
        $sourceSiteId = get_current_blog_id();
        $copier = new ReviewCopier($sourcePostId, $sourceSiteId);
        $copier->sync();
    }

    /**
     * @action site-reviews/review/verified
     */
    public function onVerified(int $sourcePostId): void
    {
        if (!Review::isReview($sourcePostId)) {
            return;
        }
        $timestamp = Cast::toInt(glsr(Database::class)->meta($sourcePostId, 'verified_on'));
        $sourceSiteId = get_current_blog_id();
        $copier = new ReviewCopier($sourcePostId, $sourceSiteId);
        $copier->run(function ($context) use ($timestamp) {
            $relationHelper = new RelationSaveHelper($context);
            $relationHelper->syncVerified($timestamp);
        });
    }

    /**
     * @action multilingualpress.metabox_after_relate_posts
     */
    public function onRelateReview(RelationshipContext $context, ServerRequest $request): void
    {
        if (!Review::isReview($context->remotePostId())) {
            return;
        }
        $data = Arr::consolidate($request->bodyValue(glsr()->id));
        $postIds = Arr::consolidate($request->bodyValue('post_ids'));
        $userIds = Arr::consolidate($request->bodyValue('user_ids'));
        $relationHelper = new RelationSaveHelper($context);
        $relationHelper->syncRating($data);
        $relationHelper->syncAssignedPosts($postIds);
        $relationHelper->syncAssignedUsers($userIds);
    }
}
