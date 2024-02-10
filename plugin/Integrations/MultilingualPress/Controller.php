<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress;

use GeminiLabs\SiteReviews\Commands\CreateReview;
use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Defaults\RatingDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Review;
use Inpsyde\MultilingualPress\Framework\Http\Request;
use Inpsyde\MultilingualPress\Framework\Service\ServiceProvidersCollection;
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
        return ['pending', 'publish'];
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
     * @action site-reviews/review/updated/post_ids
     */
    public function onBulkAssignPosts(Review $review, array $postIds = []): void
    {
        if ('edit' !== glsr_current_screen()->base) {
            return;
        }
    }

    /**
     * @action site-reviews/review/updated/user_ids
     */
    public function onBulkAssignUsers(Review $review, array $userIds = []): void
    {
        if ('edit' !== glsr_current_screen()->base) {
            return;
        }
    }

    /**
     * @action multilingualpress.metabox_after_relate_posts
     */
    public function onRelateReview(RelationshipContext $context, Request $request): void
    {
        @parse_str($request->body(), $body);

        if (!Review::isReview($context->remotePostId())) {
            return;
        }

        // switch_to_blog($context->sourceSiteId());
        // $review = glsr_get_review($context->sourcePostId());
        // $data = glsr(RatingDefaults::class)->restrict($review->toArray());
        // $data['name'] = $review->name;
        // $data['review_id'] = $context->remotePostId();
        // $data['is_approved'] = 'publish' === $context->remotePost()->post_status;
        // restore_current_blog();

        glsr_log('onRelateReview');
        glsr_log($body);
        glsr_log($body['multilingualpress']);
        glsr_log($data);
        glsr_log($context);
    }
}
