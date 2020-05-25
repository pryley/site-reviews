<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class ColumnFilterReviewType implements ColumnFilter
{
    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        if (count(glsr()->reviewTypes) < 2) {
            return;
        }
        $label = glsr(Builder::class)->label([
            'class' => 'screen-reader-text',
            'for' => 'review_type',
            'text' => _x('Filter by type', 'admin-text', 'site-reviews'),
        ]);
        $filter = glsr(Builder::class)->select([
            'name' => 'review_type',
            'options' => Arr::prepend(glsr()->reviewTypes, _x('All types', 'admin-text', 'site-reviews'), ''),
            'value' => filter_input(INPUT_GET, 'review_type'),
        ]);
        return $label.$filter;
    }
}
