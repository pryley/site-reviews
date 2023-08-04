<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

class ColumnFilterTerms extends ColumnFilter
{
    /**
     * @return string
     */
    public function label()
    {
        return _x('Filter by terms', 'admin-text', 'site-reviews');
    }

    /**
     * @return array
     */
    public function options()
    {
        return [
            1 => _x('Terms were accepted', 'admin-text', 'site-reviews'),
            0 => _x('Terms were not accepted', 'admin-text', 'site-reviews'),
        ];
    }

    /**
     * @return string
     */
    public function placeholder()
    {
        return _x('Any terms', 'admin-text', 'site-reviews');
    }

    /**
     * @return string
     */
    public function title()
    {
        return _x('Terms', 'admin-text', 'site-reviews');
    }
}
