<?php

namespace GeminiLabs\SiteReviews\Integrations\Gutenberg;

use GeminiLabs\SiteReviews\Commands\EnqueuePublicAssets;
use GeminiLabs\SiteReviews\Controllers\AbstractController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks\SiteReviewBlock;
use GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks\SiteReviewsBlock;
use GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks\SiteReviewsFormBlock;
use GeminiLabs\SiteReviews\Integrations\Gutenberg\Blocks\SiteReviewsSummaryBlock;
use GeminiLabs\SiteReviews\Modules\Assets\Stylesheets;

class Controller extends AbstractController
{
    protected bool $printsCanvasImportMap = false;

    /**
     * Enqueue what a block preview needs in the canvas.
     *
     * @action enqueue_block_assets
     */
    public function enqueueBlockAssets(): void
    {
        if (!is_admin()) {
            return;
        }
        if (!wp_should_load_block_editor_scripts_and_styles()) {
            $this->enqueueCanvasScripts();
        }
        if (!glsr()->filterBool('assets/css', true)) {
            return;
        }
        $stylesheets = glsr(Stylesheets::class);
        $handle = glsr()->id.'/css';
        wp_register_style($handle, false, [], glsr()->version);
        wp_add_inline_style($handle, $stylesheets->shared());
        wp_enqueue_style($handle);
        foreach ($stylesheets->urls() as $component => $url) {
            if (!in_array($component, Stylesheets::SHARED, true)) {
                wp_enqueue_style("{$handle}/{$component}", $url, [$handle], null); // the version is in the url
            }
        }
    }

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
     * @action wp_print_footer_scripts:1
     */
    public function printCanvasImportMap(): void
    {
        if ($this->printsCanvasImportMap) {
            $this->printsCanvasImportMap = false; // the page prints its own
            wp_script_modules()->print_import_map();
        }
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

    /**
     * Enqueue the public script and print the import map in the canvas.
     */
    protected function enqueueCanvasScripts(): void
    {
        // check if the editor has removed the callback
        $loadsEveryBlock = false !== has_action('enqueue_block_assets', 'wp_enqueue_registered_block_scripts_and_styles');
        // check if the script is registered in the Site Editor
        if ($loadsEveryBlock && !wp_script_is(glsr()->id, 'registered')) {
            $this->execute(new EnqueuePublicAssets());
        }
        $this->printsCanvasImportMap = true; // WordPress doesn't print the import map in the canvas
        add_action('wp_print_footer_scripts', [$this, 'printCanvasImportMap'], 1); // before footer scripts
    }
}
