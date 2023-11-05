<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

class ColumnFilterType extends AbstractColumnFilter
{
    public function label(): string
    {
        return _x('Filter by review type', 'admin-text', 'site-reviews');
    }

    public function options(): array
    {
        return glsr()->retrieveAs('array', 'review_types');
    }

    public function placeholder(): string
    {
        return _x('Any review type', 'admin-text', 'site-reviews');
    }

    public function title(): string
    {
        return _x('Type', 'admin-text', 'site-reviews');
    }
}
