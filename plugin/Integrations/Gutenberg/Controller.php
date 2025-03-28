<?php

namespace GeminiLabs\SiteReviews\Integrations\Gutenberg;

use GeminiLabs\SiteReviews\Commands\EnqueuePublicAssets;
use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks\SiteReviewBlock;
use GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks\SiteReviewsBlock;
use GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks\SiteReviewsFormBlock;
use GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks\SiteReviewsSummaryBlock;
use GeminiLabs\SiteReviews\Modules\Assets\AssetJs;
use GeminiLabs\SiteReviews\Modules\Style;

class Controller extends AbstractController
{
    /**
     * The CSS registered here will not load in the site editor unless it contains the .wp-block selector.
     *
     * @see https://github.com/WordPress/gutenberg/issues/41821
     *
     * @action enqueue_block_editor_assets
     */
    public function enqueueBlockEditorAssets(): void
    {
        wp_enqueue_style(
            glsr()->id.'/blocks',
            glsr(Style::class)->stylesheetUrl('blocks'),
            ['wp-edit-blocks'],
            glsr()->version
        );
        wp_add_inline_style(glsr()->id.'/blocks', (new EnqueuePublicAssets())->inlineStyles());
    }

    /**
     * @param bool|string[] $blockTypes
     *
     * @return bool|string[]
     *
     * @filter allowed_block_types_all
     */
    public function filterAllowedBlockTypes($blockTypes, \WP_Block_Editor_Context $context)
    {
        $postType = Arr::get($context, 'post.post_type');
        return glsr()->post_type !== $postType
            ? $blockTypes
            : [];
    }

    /**
     * @param array[] $categories
     *
     * @filter block_categories_all
     */
    public function filterBlockCategories(array $categories): array
    {
        $categories[] = [
            'slug' => glsr()->id,
            'title' => glsr()->name,
        ];
        return $categories;
    }

    /**
     * @filter use_block_editor_for_post_type
     */
    public function filterUseBlockEditor(bool $useBlockEditor, string $postType): bool
    {
        return glsr()->post_type !== $postType ? $useBlockEditor : false;
    }

    /**
     * @action init
     */
    public function registerBlocks(): void
    {
        glsr(SiteReviewBlock::class)->register();
        glsr(SiteReviewsBlock::class)->register();
        glsr(SiteReviewsFormBlock::class)->register();
        glsr(SiteReviewsSummaryBlock::class)->register();
    }

    /**
     * @param string[] $widgets
     *
     * @filter widget_types_to_hide_from_legacy_widget_block
     */
    public function removeLegacyWidgets(array $widgets): array
    {
        array_push($widgets, 'glsr_site-review', 'glsr_site-reviews', 'glsr_site-reviews-form', 'glsr_site-reviews-summary');
        return $widgets;
    }
}
