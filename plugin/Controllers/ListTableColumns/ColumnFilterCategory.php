<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Helpers\Arr;

class ColumnFilterCategory extends ColumnFilter
{
    /**
     * @return string
     */
    public function label()
    {
        return _x('Filter by category', 'admin-text', 'site-reviews');
    }

    /**
     * @return array
     */
    public function options()
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

    /**
     * @return string
     */
    public function placeholder()
    {
        return _x('Any category', 'admin-text', 'site-reviews');
    }

    /**
     * @return string|int
     */
    public function value()
    {
        return filter_input(INPUT_GET, $this->name(), FILTER_SANITIZE_NUMBER_INT);
    }
}
