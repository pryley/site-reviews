<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Helpers\Str;

class AuthorTag extends Tag
{
    /**
     * {@inheritdoc}
     */
    public function handle($value)
    {
        if (!$this->isHidden()) {
            return Str::convertName($value,
                glsr_get_option('reviews.name.format'),
                glsr_get_option('reviews.name.initial')
            );
        }
    }
}
