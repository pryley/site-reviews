<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Modules\Rating;

class ColumnFilterRating extends AbstractColumnFilter
{
    public function label(): string
    {
        return _x('Filter by rating', 'admin-text', 'site-reviews');
    }

    public function options(): array
    {
        $options = [];
        $max = glsr()->constant('MAX_RATING', Rating::class);
        foreach (range($max, 0) as $rating) {
            $empty = $max - $rating;
            $title = _x('%s star rating', 'admin-text', 'site-reviews');
            $options[$rating] = [
                'text' => str_repeat('★', $rating).str_repeat('☆', $empty),
                'title' => sprintf($title, $rating),
            ];
        }
        return $options;
    }

    public function placeholder(): string
    {
        return _x('Any rating', 'admin-text', 'site-reviews');
    }

    public function title(): string
    {
        return _x('Rating', 'admin-text', 'site-reviews');
    }

    public function value(): string
    {
        return (string) filter_input(INPUT_GET, $this->name(), FILTER_SANITIZE_NUMBER_INT);
    }
}
