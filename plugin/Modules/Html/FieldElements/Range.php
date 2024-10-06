<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Template;
use GeminiLabs\SiteReviews\Modules\Style;

class Range extends Radio
{
    protected function buildReviewField(Arguments $args): string
    {
        $index = 0;
        $optionKeys = array_keys($args->options);
        $inputs = array_reduce($optionKeys,
            fn ($carry, $value) => $carry.$this->buildInput((string) $value, ++$index, $args),
            ''
        );
        $field = glsr(Builder::class)->div([
            'class' => 'glsr-range-options',
            'data-placeholder' => __('Please select', 'site-reviews'),
            'text' => $inputs,
        ]);
        if (empty($args->labels)) {
            return $field;
        }
        $labels = [
            Arr::get($args->labels, 0, ''),
            Arr::get($args->labels, 1, ''),
            Arr::get($args->labels, 2, ''),
        ];
        $labels = array_reduce($labels,
            fn ($carry, $label) => $carry.glsr(Builder::class)->span($label),
            ''
        );
        $labels = glsr(Builder::class)->div([
            'class' => 'glsr-range-labels',
            'text' => $labels,
        ]);
        return $labels.$field;
    }
}
