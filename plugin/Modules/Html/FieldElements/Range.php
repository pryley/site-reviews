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
        $field = glsr(Builder::class)->div([
            'class' => 'glsr-range-options',
            'data-placeholder' => __('Please select', 'site-reviews'),
            'text' => parent::buildReviewField($args),
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

    protected function normalizeOptions(): void
    {
        if (!empty($this->field->options)) {
            $keys = range(1, count($this->field->options));
            $values = array_values($this->field->options);
            $values = array_map(fn ($value) => Cast::toString($value), $values);
            $this->field->options = array_combine($keys, $values);
        }
    }
}
