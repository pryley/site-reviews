<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

use GeminiLabs\SiteReviews\Modules\Rating as RatingModule;

class Rating extends AbstractFieldElement
{
    public function required(): array
    {
        $maxRating = max(1, (int) glsr()->constant('MAX_RATING', RatingModule::class));
        $optionLabel = _n_noop('%s Star', '%s Stars', 'site-reviews');
        return [
            'class' => 'browser-default no_wrap no-wrap',
            'options' => glsr(RatingModule::class)->optionsArray($optionLabel),
            'placeholder' => __('Select a Rating', 'site-reviews'),
            'validation' => "number|between:0,{$maxRating}",
        ];
    }

    public function tag(): string
    {
        return 'select';
    }
}
