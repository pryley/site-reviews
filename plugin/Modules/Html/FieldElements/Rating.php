<?php

namespace GeminiLabs\SiteReviews\Modules\Html\FieldElements;

use GeminiLabs\SiteReviews\Modules\Rating as RatingModule;

class Rating extends AbstractFieldElement
{
    public function required(): array
    {
        return [
            'class' => 'browser-default disable-select no_wrap no-wrap',
            'options' => glsr(RatingModule::class)->optionsArray(),
            'placeholder' => __('Select a Rating', 'site-reviews'),
            'validation' => sprintf('number|between:%d,%d', RatingModule::min(), RatingModule::max()),
        ];
    }

    public function tag(): string
    {
        return 'select';
    }
}
