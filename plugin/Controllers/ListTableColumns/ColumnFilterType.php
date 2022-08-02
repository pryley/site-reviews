<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

class ColumnFilterType extends ColumnFilter
{
    /**
     * @return string
     */
    public function label()
    {
        return _x('Filter by review type', 'admin-text', 'site-reviews');
    }

    /**
     * @return array
     */
    public function options()
    {
        return glsr()->retrieveAs('array', 'review_types');
    }

    /**
     * @return string
     */
    public function placeholder()
    {
        return _x('Any review type', 'admin-text', 'site-reviews');
    }

    /**
     * @return string
     */
    public function title()
    {
        return _x('Type', 'admin-text', 'site-reviews');
    }
}
