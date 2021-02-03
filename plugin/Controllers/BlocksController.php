<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Style;

class BlocksController extends Controller
{
    /**
     * @param array $blockTypes
     * @param \WP_Post $post
     * @return array
     * @filter allowed_block_types
     */
    public function filterAllowedBlockTypes($blockTypes, $post)
    {
        return glsr()->post_type !== get_post_type($post)
            ? $blockTypes
            : [];
    }

    /**
     * @param array $categories
     * @return array
     * @filter block_categories
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
        return glsr()->post_type == $postType
            ? false
            : $bool;
    }

    /**
     * @return void
     * @action init
     */
    public function registerAssets()
    {
        wp_register_style(
            glsr()->id.'/blocks',
            $this->getStylesheet(),
            ['wp-edit-blocks'],
            glsr()->version
        );
        wp_register_script(
            glsr()->id.'/blocks',
            glsr()->url('assets/scripts/'.glsr()->id.'-blocks.js'),
            ['wp-api-fetch', 'wp-blocks', 'wp-i18n', 'wp-editor', 'wp-element', glsr()->id.'/admin'],
            glsr()->version
        );
    }

    /**
     * @return void
     * @action init
     */
    public function registerBlocks()
    {
        $blocks = [
            'form', 'reviews', 'summary',
        ];
        foreach ($blocks as $block) {
            $id = str_replace('_reviews', '', glsr()->id.'_'.$block);
            $blockClass = Helper::buildClassName($id.'-block', 'Blocks');
            if (!class_exists($blockClass)) {
                glsr_log()->error(sprintf('Block class missing (%s)', $blockClass));
                continue;
            }
            glsr($blockClass)->register($block);
        }
    }

    /**
     * @return string
     */
    protected function getStylesheet()
    {
        $style = glsr(Style::class)->style;
        return glsr()->url('assets/styles/blocks/'.$style.'-blocks.css');
    }
}
