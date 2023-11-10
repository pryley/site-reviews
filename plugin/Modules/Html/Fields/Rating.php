<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Modules\Rating as RatingModule;

class Rating extends Field
{
    public static function required(string $fieldLocation = ''): array
    {
        $options = glsr(RatingModule::class)->optionsArray(
            _n_noop('%s Star', '%s Stars', 'site-reviews')
        );
        return [
            'class' => 'browser-default no_wrap no-wrap',
            'options' => $options,
            'placeholder' => __('Select a Rating', 'site-reviews'),
            'type' => 'select',
        ];
    }

    public function tag(): string
    {
        return 'select';
    }
}
