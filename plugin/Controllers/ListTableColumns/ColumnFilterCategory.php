<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

class ColumnFilterCategory extends ColumnFilter
{
    /**
     * {@inheritdoc}
     */
    public function handle(array $enabledFilters = [])
    {
        if (in_array('category', $enabledFilters)) {
            $this->enabled = true;
        }
        if ($options = $this->options()) {
            $label = $this->label('assigned_term',
                _x('Filter by category', 'admin-text', 'site-reviews')
            );
            $filter = $this->filter('assigned_term', $options,
                _x('All categories', 'admin-text', 'site-reviews')
            );
            return $label.$filter;
        }
    }

    /**
     * @param string $id
     * @return string
     */
    protected function id($id)
    {
        return 'glsr-filter-by-category';
    }

    /**
     * @return array
     */
    protected function options()
    {
        return get_terms([
            'count' => false,
            'fields' => 'id=>name',
            'hide_empty' => true,
            'taxonomy' => glsr()->taxonomy,
        ]);
    }

    /**
     * @param string $id
     * @return int|string
     */
    protected function value($id)
    {
        return filter_input(INPUT_GET, $id, FILTER_SANITIZE_NUMBER_INT);
    }
}
