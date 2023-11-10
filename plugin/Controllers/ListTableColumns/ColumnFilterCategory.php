<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Helpers\Arr;

class ColumnFilterCategory extends AbstractColumnFilter
{
    public function label(): string
    {
        return _x('Filter by category', 'admin-text', 'site-reviews');
    }

    public function options(): array
    {
        $options = get_terms([
            'count' => false,
            'fields' => 'id=>name',
            'hide_empty' => true,
            'taxonomy' => glsr()->taxonomy,
        ]);
        if (is_wp_error($options)) {
            return [];
        }
        $options = Arr::prepend($options, _x('No category', 'admin-text', 'site-reviews'), '-1');
        return $options;
    }

    public function placeholder(): string
    {
        return _x('Any category', 'admin-text', 'site-reviews');
    }

    public function title(): string
    {
        return _x('Category', 'admin-text', 'site-reviews');
    }

    public function value(): string
    {
        global $wp_query;
        if ($term = get_term_by('slug', $wp_query->get(glsr()->taxonomy), glsr()->taxonomy)) {
            return (string) $term->term_taxonomy_id;
        }
        return (string) filter_input(INPUT_GET, $this->name(), FILTER_SANITIZE_NUMBER_INT);
    }
}
