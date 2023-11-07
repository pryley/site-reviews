<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

use GeminiLabs\SiteReviews\Review;

class ReviewTag extends Tag
{
    /** @var Review */
    public $review;

    /**
     * @param mixed $with
     */
    protected function validate($with): bool
    {
        if ($with instanceof Review) {
            $this->review = $with;
            return true;
        }
        return false;
    }
}
