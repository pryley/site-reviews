<?php

namespace GeminiLabs\SiteReviews\Integrations\MultilingualPress\Controllers;

use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\MetaboxFields\AssignedPostsField;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\MetaboxFields\AssignedUsersField;
use GeminiLabs\SiteReviews\Integrations\MultilingualPress\Notices\NetworkNotice;
use Inpsyde\MultilingualPress\Core\PostTypeRepository;
use Inpsyde\MultilingualPress\Core\TaxonomyRepository;
use Inpsyde\MultilingualPress\TranslationUi\Post;

use function Inpsyde\MultilingualPress\resolve;

class Controller extends AbstractController
{
    /**
     * @action multilingualpress.update_plugin_settings
     */
    public function enforceEntitySupport(): void
    {
        if (!is_admin()) {
            return;
        }
        $enforcedTypes = get_post_types(['show_in_menu' => true]);
        $enforcedTypes = array_filter($enforcedTypes, fn ($type) => str_starts_with($type, glsr()->post_type));
        $postTypes = get_network_option(0, PostTypeRepository::OPTION);
        $taxonomies = get_network_option(0, TaxonomyRepository::OPTION);
        foreach ($enforcedTypes as $key => $type) {
            $postTypes[$key] = [
                PostTypeRepository::FIELD_ACTIVE => true,
                PostTypeRepository::FIELD_PERMALINK => false,
            ];
        }
        $taxonomies[glsr()->taxonomy] = [
            TaxonomyRepository::FIELD_ACTIVE => true,
            TaxonomyRepository::FIELD_SKIN => '',
        ];
        resolve(PostTypeRepository::class)->supportPostTypes($postTypes);
        resolve(TaxonomyRepository::class)->supportTaxonomies($taxonomies);
    }

    /**
     * @filter site-reviews/enqueue/admin/inline-styles
     */
    public function filterAdminInlineCss(string $css): string
    {
        $custom = [];
        if ($this->isEditor()) {
            $custom[] = '.mlp-translation-metabox[data-post-type^="site-review"]{margin:9px 0 0}';
        }
        if ($this->isListTable()) {
            $custom[] = 'td.column-translations{align-items:center;display:flex;flex-wrap:wrap;gap:8px}';
            $custom[] = 'td.column-translations .mlp-table-list-relations-divide{display:none!important}';
        }
        if ($this->isReviewEditor()) {
            $custom[] = 'tr.mlp-taxonomy-box>td{padding:0}';
            $custom[] = 'tr.mlp-taxonomy-box>td>ul{border:solid 1px #dcdcde;margin-bottom:.9em}';
        }
        return $css.implode('', $custom);
    }

    /**
     * @filter site-reviews/enqueue/admin/inline-script
     */
    public function filterAdminInlineJs(string $js): string
    {
        if (!$this->isEditor()) {
            return $js;
        }
        $custom = [
            '$(".post-new-php .mlp-translation-metabox[data-post-type^=site-review] .tab-relation input[value=new]").prop("checked",true).change();', // create translations on create
        ];
        if ($this->isReviewEditor()) {
            $custom[] = '$(".mlp-taxonomy-sync:has(input:checked)").closest("table").find(".mlp-taxonomy-box").hide();';
        }
        return $js.'jQuery(function($){'.implode('', $custom).'});';
    }

    /**
     * @filter multilingualpress.copy_content_is_checked
     *
     * @filter-location \Inpsyde\MultilingualPress\TranslationUi\Post\Field\CopyContent
     */
    public function filterContentIsChecked(bool $isChecked): bool
    {
        global $pagenow;
        if ('post-new.php' !== $pagenow) {
            return $isChecked;
        }
        if (!$this->isEditor()) {
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
            'Synchronize Taxonomies' => _x('Synchronize Categories', 'multilingualpress text (admin-text)', 'site-reviews'),
        ];
        if (array_key_exists($single, $translations)) {
            return $translations[$single];
        }
        return $translation;
    }

    /**
     * @filter multilingualpress.post_translation_metabox_tabs
     *
     * @filter-location \Inpsyde\MultilingualPress\TranslationUi\Post\Metabox
     */
    public function filterMetaboxTabs(array $tabs, Post\RelationshipContext $context): array
    {
        if (glsr()->post_type !== $context->sourcePost()->post_type) {
            return $tabs;
        }
        foreach ($tabs as $index => $tab) {
            if (Post\MetaboxFields::TAB_TAXONOMIES !== $tab->id()) {
                continue;
            }
            $tabs[$index] = new Post\MetaboxTab( // overwrite Taxonomies tab
                Post\MetaboxFields::TAB_TAXONOMIES,
                glsr()->name, // rename Taxonomies tab
                new Post\MetaboxField(
                    AssignedPostsField::FIELD_COPY_ASSIGNED_POSTS,
                    new AssignedPostsField(), // @phpstan-ignore-line
                    [AssignedPostsField::class, 'sanitize']
                ),
                new Post\MetaboxField(
                    AssignedUsersField::FIELD_COPY_ASSIGNED_USERS,
                    new AssignedUsersField(), // @phpstan-ignore-line
                    [AssignedUsersField::class, 'sanitize']
                ),
                ...$tab->fields(),
            );
        }
        return $tabs;
    }

    /**
     * @filter site-reviews/notices
     */
    public function filterNotices(array $notices): array
    {
        $notices[] = NetworkNotice::class;
        return $notices;
    }

    /**
     * @filter multilingualpress.translation_ui_post_statuses
     *
     * @filter-location \Inpsyde\MultilingualPress\TranslationUi\Post\Field\Status
     */
    public function filterPostStatuses(array $statuses): array
    {
        if ($this->isReviewEditor()) {
            $statuses = ['draft', 'pending', 'publish'];
        }
        return $statuses;
    }

    /**
     * @filter site-reviews/setting-form/config
     */
    public function filterSettingForm(array $config): array
    {
        unset($config['settings.general.multilingual']);
        return $config;
    }

    /**
     * @filter multilingualpress.copy_taxonomies_is_checked
     *
     * @filter-location \Inpsyde\MultilingualPress\TranslationUi\Post\Field\CopyTaxonomies
     */
    public function filterTaxonomiesIsChecked(bool $isChecked): bool
    {
        if ($this->isReviewEditor()) {
            return true;
        }
        return $isChecked;
    }
}
