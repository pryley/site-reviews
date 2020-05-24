<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Str;

class ReviewAuthorTag extends ReviewTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        if (!$this->isHidden()) {
            $tagValue = Str::convertName($value,
                glsr_get_option('reviews.name.format'),
                glsr_get_option('reviews.name.initial')
            );
            return $this->wrap($tagValue, 'span');
        }
    }
}
