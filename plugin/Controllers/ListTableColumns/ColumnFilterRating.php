<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Contracts\ColumnFilterContract;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Rating;

class ColumnFilterRating implements ColumnFilterContract
{
    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $ratings = range(glsr()->constant('MAX_RATING', Rating::class), 0);
        $options = ['' => _x('All ratings', 'admin-text', 'site-reviews')];
        foreach ($ratings as $rating) {
            $label = _nx('%s star', '%s stars', $rating, 'admin-text', 'site-reviews');
            $options[$rating] = sprintf($label, $rating);
        }
        $label = glsr(Builder::class)->label([
            'class' => 'screen-reader-text',
            'for' => 'rating',
            'text' => _x('Filter by rating', 'admin-text', 'site-reviews'),
        ]);
        $filter = glsr(Builder::class)->select([
            'name' => 'rating',
            'options' => $options,
            'value' => filter_input(INPUT_GET, 'rating'),
        ]);
        return $label.$filter;
    }
}
