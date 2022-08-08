<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Modules\Rating;

class ColumnFilterRating extends ColumnFilter
{
    /**
     * @return string
     */
    public function label()
    {
        return _x('Filter by rating', 'admin-text', 'site-reviews');
    }

    /**
     * @return array
     */
    public function options()
    {
        $options = [];
        $max = glsr()->constant('MAX_RATING', Rating::class);
        foreach (range($max, 0) as $rating) {
            $empty = $max - $rating;
            $title = _x('%s star rating', 'admin-text', 'site-reviews');
            $options[$rating] = [
                'title' => sprintf($title, $rating),
                'value' => str_repeat('★', $rating).str_repeat('☆', $empty),
            ];
        }
        return $options;
    }

    /**
     * @return string
     */
    public function placeholder()
    {
        return _x('Any rating', 'admin-text', 'site-reviews');
    }

    /**
     * @return string
     */
    public function title()
    {
        return _x('Rating', 'admin-text', 'site-reviews');
    }

    /**
     * @return string|int
     */
    public function value()
    {
        return filter_input(INPUT_GET, $this->name(), FILTER_SANITIZE_NUMBER_INT);
    }
}
