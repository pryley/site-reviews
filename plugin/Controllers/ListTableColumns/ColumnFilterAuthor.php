<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

class ColumnFilterAuthor extends ColumnFilterAssignedUser
{
    public function label(): string
    {
        return _x('Filter by author', 'admin-text', 'site-reviews');
    }

    public function options(): array
    {
        return [
            '' => _x('Any author', 'admin-text', 'site-reviews'),
            0 => _x('No author', 'admin-text', 'site-reviews'),
        ];
    }

    public function title(): string
    {
        return _x('Author', 'admin-text', 'site-reviews');
    }
}
