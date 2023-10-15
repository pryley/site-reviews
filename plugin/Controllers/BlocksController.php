<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Commands\EnqueuePublicAssets;
use GeminiLabs\SiteReviews\Commands\RegisterBlocks;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Style;

class BlocksController extends Controller
{
    /**
     * @param array $blockTypes
     * @param \WP_Post|\WP_Block_Editor_Context $context
     * @return array
     * @filter allowed_block_types_all
     */
    public function filterAllowedBlockTypes($blockTypes, $context)
    {
        $fallback = Arr::get($context, 'post_type'); // @compat <5.8
        $postType = Arr::get($context, 'post.post_type', $fallback);
        return glsr()->post_type !== $postType
            ? $blockTypes
            : [];
    }

    /**
     * @param array $categories
     * @return array
     * @filter block_categories_all
     */
    public function filterBlockCategories($categories)
    {
        $categories = Arr::consolidate($categories);
        $categories[] = [
            'icon' => null,
            'slug' => glsr()->id,
            'title' => glsr()->name,
        ];
        return $categories;
    }

    /**
     * @param bool $bool
     * @param string $postType
     * @return bool
     * @filter use_block_editor_for_post_type
     */
    public function filterUseBlockEditor($bool, $postType)
    {
        return glsr()->post_type === $postType
            ? false
            : $bool;
    }

    /**
     * @return void
     * @action init
     */
    public function registerAssets()
    {
        global $pagenow;
        wp_register_style(
            glsr()->id.'/blocks',
            glsr(Style::class)->stylesheetUrl('blocks'),
            ['wp-edit-blocks'],
            glsr()->version
        );
        wp_add_inline_style(glsr()->id.'/blocks', (new EnqueuePublicAssets())->inlineStyles());
        wp_register_script(
            glsr()->id.'/blocks',
            glsr()->url('assets/scripts/'.glsr()->id.'-blocks.js'),
            [
                glsr()->id.'/admin',
                'wp-block-editor',
                'wp-blocks',
                'wp-i18n',
                'wp-element',
            ],
            glsr()->version
        );
    }

    /**
     * @return void
     * @action init
     */
    public function registerBlocks()
    {
        $this->execute(new RegisterBlocks([
            'site_review',
            'site_reviews',
            'site_reviews_form',
            'site_reviews_summary',
        ]));
    }

    /**
     * @param array  $types
     * @return array
     * @filter widget_types_to_hide_from_legacy_widget_block
     */
    public function replaceLegacyWidgets($types)
    {
        // array_push($types, 'glsr_site-reviews', 'glsr_site-reviews-form', 'glsr_site-reviews-summary');
        return $types;
    }
}
