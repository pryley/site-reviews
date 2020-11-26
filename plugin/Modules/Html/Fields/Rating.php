<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Rating as RatingModule;

class Rating extends Field
{
    /**
     * {@inheritdoc}
     */
    public static function required($fieldLocation = null)
    {
        $options = [];
        foreach (range(glsr()->constant('MAX_RATING', RatingModule::class), 1) as $rating) {
            $options[$rating] = sprintf(_n('%s Star', '%s Stars', $rating, 'site-reviews'), $rating);
        }
        return [
            'class' => 'glsr-star-rating',
            'options' => $options,
            'placeholder' => __('Select a Rating', 'site-reviews'),
            'type' => 'select',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function tag()
    {
        return 'select';
    }
}
