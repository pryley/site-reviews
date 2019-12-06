<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;

class BlocksController extends Controller
{
    /**
     * @param array $categories
     * @return array
     * @filter block_categories
     */
    public function filterBlockCategories($categories)
    {
        $categories = Arr::consolidateArray($categories);
        $categories[] = [
            'icon' => null,
            'slug' => Application::ID,
            'title' => glsr()->name,
        ];
        return $categories;
    }

    /**
     * @param array $editors
     * @param string $postType
     * @return array
     * @filter classic_editor_enabled_editors_for_post_type
     * @plugin classic-editor/classic-editor.php
     */
    public function filterEnabledEditors($editors, $postType)
    {
        return Application::POST_TYPE == $postType
            ? ['block_editor' => false, 'classic_editor' => false]
            : $editors;
    }

    /**
     * @param bool $bool
     * @param string $postType
     * @return bool
     * @filter use_block_editor_for_post_type
     */
    public function filterUseBlockEditor($bool, $postType)
    {
        return Application::POST_TYPE == $postType
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
            Application::ID.'/blocks',
            glsr()->url('assets/styles/'.Application::ID.'-blocks.css'),
            ['wp-edit-blocks'],
            glsr()->version
        );
        wp_register_script(
            Application::ID.'/blocks',
            glsr()->url('assets/scripts/'.Application::ID.'-blocks.js'),
            ['wp-api-fetch', 'wp-blocks', 'wp-i18n', 'wp-editor', 'wp-element', Application::ID],
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
            $id = str_replace('_reviews', '', Application::ID.'_'.$block);
            $blockClass = Helper::buildClassName($id.'-block', 'Blocks');
            if (!class_exists($blockClass)) {
                glsr_log()->error(sprintf('Class missing (%s)', $blockClass));
                continue;
            }
            glsr($blockClass)->register($block);
        }
    }
}
