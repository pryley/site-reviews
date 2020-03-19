<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Fields;

use GeminiLabs\SiteReviews\Modules\Rating as RatingModule;

class Rating extends Field
{
    /**
     * @inheritDoc
     */
    public function getTag()
    {
        return 'select';
    }

    /**
     * @inheritDoc
     */
    public static function required()
    {
        $options = ['' => __('Select a Rating', 'site-reviews')];
        foreach (range(glsr()->constant('MAX_RATING', RatingModule::class), 1) as $rating) {
            $options[$rating] = sprintf(_n('%s Star', '%s Stars', $rating, 'site-reviews'), $rating);
        }
        return [
            'class' => 'glsr-star-rating',
            'options' => $options,
            'type' => 'select',
        ];
    }
}
