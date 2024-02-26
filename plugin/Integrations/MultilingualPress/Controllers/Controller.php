<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Review;
use Inpsyde\MultilingualPress\TranslationUi\Post\MetaboxFields;
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
        $relation = '$(".post-new-php .tab-relation input[value=new]").prop("checked",true).change();';
        $taxonomy = '$(".mlp-taxonomy-sync:has(input:checked)").closest("table").find(".mlp-taxonomy-box").hide();';
        $trasher = '$(".post-new-php #mlp-trasher").prop("checked",true).change();';
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
            'Send all the translations to trash when this post is trashed.' => _x('Trash all translations when trashed.', 'multilingualpress text (admin-text)', 'site-reviews'),
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
    public function filterPostStatuses(array $statuses): array
    {
        if ($this->isReviewEditor()) {
            $statuses = ['draft', 'pending', 'publish'];
        }
        return $statuses;
    }

    /**
     * @filter multilingualpress.new_relate_remote_post_before_insert
     */
    public function filterRemotePostData(array $post, RelationshipContext $context, string $operation): array
    {
        error_log(print_r('filterRemotePostData', true));
        if ($operation !== MetaboxFields::FIELD_RELATION_NEW) {
            error_log(print_r($post, true));
            error_log(print_r($context, true));
            return $post;
        }
        return $post;
    }

    /**
     * @filter multilingualpress.sync_post_meta_keys
     */
    public function filterSyncedMetaKeys(array $keys, RelationshipContext $context): array
    {
        $sourcePostId = $context->sourcePostId();
        if (!Review::isReview($sourcePostId)) {
            return $keys;
        }
        if (metadata_exists('post', $sourcePostId, '_submitted')) { // original submitted request
            $keys[] = '_submitted';
        }
        if (metadata_exists('post', $sourcePostId, '_verified')) { // WooCommerce verified owner
            $keys[] = '_verified';
        }
        return $keys;
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
}
