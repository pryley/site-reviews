<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Rating;

class SummaryTag extends Tag
{
    public array $ratings = [];

    protected function hideOption(): string
    {
        $mappedTags = [
            'percentages' => 'bars',
            'text' => 'summary',
        ];
        return Arr::get($mappedTags, $this->tag, $this->tag);
    }

    protected function validate($with): bool
    {
        if (Arr::isIndexedAndFlat($with) && $with === array_filter($with, 'is_numeric')) {
            if (empty($with)) {
                $with = glsr(Rating::class)->emptyArray();
            }
            $this->ratings = $with;
            return true;
        }
        return false;
    }
}
