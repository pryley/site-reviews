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
        $ratings = range(glsr()->constant('MAX_RATING', Rating::class), 0);
        foreach ($ratings as $rating) {
            $label = _nx('%s star', '%s stars', $rating, 'admin-text', 'site-reviews');
            $options[$rating] = sprintf($label, $rating);
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
