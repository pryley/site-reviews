<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Application;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class TaxonomyController
{
    /**
     * @return void
     * @action Application::TAXONOMY._add_form_fields
     * @action Application::TAXONOMY._edit_form
     */
    public function disableParents()
    {
        global $wp_taxonomies;
        $wp_taxonomies[Application::TAXONOMY]->hierarchical = false;
    }

    /**
     * @return void
     * @action Application::TAXONOMY._term_edit_form_top
     * @action Application::TAXONOMY._term_new_form_tag
     */
    public function enableParents()
    {
        global $wp_taxonomies;
        $wp_taxonomies[Application::TAXONOMY]->hierarchical = true;
    }

    /**
     * @return void
     * @action restrict_manage_posts
     */
    public function renderTaxonomyFilter()
    {
        if (!is_object_in_taxonomy(glsr_current_screen()->post_type, Application::TAXONOMY)) {
            return;
        }
        echo glsr(Builder::class)->label(__('Filter by category', 'site-reviews'), [
            'class' => 'screen-reader-text',
            'for' => Application::TAXONOMY,
        ]);
        wp_dropdown_categories([
            'depth' => 3,
            'hide_empty' => true,
            'hide_if_empty' => true,
            'hierarchical' => true,
            'name' => Application::TAXONOMY,
            'orderby' => 'name',
            'selected' => $this->getSelected(),
            'show_count' => false,
            'show_option_all' => $this->getShowOptionAll(),
            'taxonomy' => Application::TAXONOMY,
            'value_field' => 'slug',
        ]);
    }

    /**
     * @param int $postId
     * @param array $terms
     * @param array $newTTIds
     * @param string $taxonomy
     * @param bool $append
     * @param array $oldTTIds
     * @return void
     * @action set_object_terms
     */
    public function restrictTermSelection($postId, $terms, $newTTIds, $taxonomy, $append, $oldTTIds)
    {
        if (Application::TAXONOMY != $taxonomy || count($newTTIds) <= 1) {
            return;
        }
        $diff = array_diff($newTTIds, $oldTTIds);
        if (empty($newTerm = array_shift($diff))) {
            $newTerm = array_shift($newTTIds);
        }
        if ($newTerm) {
            wp_set_object_terms($postId, intval($newTerm), $taxonomy);
        }
    }

    /**
     * @return string
     */
    protected function getSelected()
    {
        global $wp_query;
        return Arr::get($wp_query->query, Application::TAXONOMY);
    }

    /**
     * @return string
     */
    protected function getShowOptionAll()
    {
        $taxonomy = get_taxonomy(Application::TAXONOMY);
        return $taxonomy
            ? ucfirst(strtolower($taxonomy->labels->all_items))
            : '';
    }
}
