<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Helpers\Arr;

class ColumnFilterAuthor extends ColumnFilter
{
    /**
     * {@inheritdoc}
     */
    public function handle(array $enabledFilters = [])
    {
        if (in_array('author', $enabledFilters)) {
            $this->enabled = true;
        }
        if ($options = $this->options()) {
            $label = $this->label('author',
                _x('Filter by author', 'admin-text', 'site-reviews')
            );
            $filter = $this->filter('author', $options,
                _x('All authors', 'admin-text', 'site-reviews')
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
        return 'glsr-filter-by-author';
    }

    /**
     * @return array
     */
    protected function options()
    {
        $options = glsr(Database::class)->users();
        $options = Arr::prepend($options, _x('No author (guest submissions)', 'admin-text', 'site-reviews'), '0');
        return $options;
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
