<?php

namespace GeminiLabs\SiteReviews\Controllers\ListTableColumns;

use GeminiLabs\SiteReviews\Contracts\ColumnFilterContract;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class ColumnFilterType implements ColumnFilterContract
{
    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        if (count($reviewTypes = glsr()->retrieveAs('array', 'review_types')) < 2) {
            return;
        }
        $label = glsr(Builder::class)->label([
            'class' => 'screen-reader-text',
            'for' => 'type',
            'text' => _x('Filter by type', 'admin-text', 'site-reviews'),
        ]);
        $filter = glsr(Builder::class)->select([
            'name' => 'type',
            'options' => Arr::prepend($reviewTypes, _x('All types', 'admin-text', 'site-reviews'), ''),
            'value' => filter_input(INPUT_GET, 'type'),
        ]);
        return $label.$filter;
    }
}
