<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

class ColumnFilterTerms extends AbstractColumnFilter
{
    public function label(): string
    {
        return _x('Filter by terms', 'admin-text', 'site-reviews');
    }

    public function options(): array
    {
        return [
            1 => _x('Terms were accepted', 'admin-text', 'site-reviews'),
            0 => _x('Terms were not accepted', 'admin-text', 'site-reviews'),
        ];
    }

    public function placeholder(): string
    {
        return _x('Any terms', 'admin-text', 'site-reviews');
    }

    public function title(): string
    {
        return _x('Terms', 'admin-text', 'site-reviews');
    }
}
