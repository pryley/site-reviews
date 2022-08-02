<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

class ColumnFilterAuthor extends ColumnFilterAssignedUser
{
    /**
     * @return string
     */
    public function label()
    {
        return _x('Filter by author', 'admin-text', 'site-reviews');
    }

    /**
     * @return array
     */
    public function options()
    {
        return [
            '' => _x('Any author', 'admin-text', 'site-reviews'),
            0 => _x('No author', 'admin-text', 'site-reviews'),
        ];
    }

    /**
     * @return string
     */
    public function title()
    {
        return _x('Author', 'admin-text', 'site-reviews');
    }
}
