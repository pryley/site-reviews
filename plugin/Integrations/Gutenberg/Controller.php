<?php

namespace GeminiLabs\SiteReviews\Integrations\Gutenberg;

use GeminiLabs\SiteReviews\Commands\EnqueuePublicAssets;
use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks\SiteReviewBlock;
use GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks\SiteReviewsBlock;
use GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks\SiteReviewsFormBlock;
use GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks\SiteReviewsSummaryBlock;

class Controller extends AbstractController
{
    /**
     * @action enqueue_block_editor_assets
     */
    public function enqueueBlockEditorAssets(): void
    {
        $this->execute(new EnqueuePublicAssets());
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
     * @param string $className The current applied classname.
     * @param string $blockName The block name.
     *
     * @filter block_default_classname
     */
    public function filterBlockGeneratedClassname($className, $blockName): string
    {
        if ('site-reviews/review' === $blockName) {
            return 'wp-block-site-review';
        }
        if ('site-reviews/reviews' === $blockName) {
            return 'wp-block-site-reviews';
        }
        return $className;
    }

    /**
     * Disable the Block editor for all Site Reviews post types (including addons)
     * 
     * @filter use_block_editor_for_post_type
     */
    public function filterUseBlockEditor(bool $useBlockEditor, string $postType): bool
    {
        return !str_starts_with($postType, glsr()->post_type) ? $useBlockEditor : false;
    }

    /**
     * @action init
     */
    public function registerBlocks(): void
    {
        if (function_exists('wp_register_block_metadata_collection')) { // WP 6.7
            wp_register_block_metadata_collection(
                glsr()->path('assets/blocks', false),
                glsr()->path('assets/blocks/blocks-manifest.php')
            );
        }
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
