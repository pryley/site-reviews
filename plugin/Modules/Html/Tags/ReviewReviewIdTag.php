<?php

namespace GeminiLabs\SiteReviews\Modules\Html\Tags;

class ReviewReviewIdTag extends ReviewTag
{
    /**
     * {@inheritdoc}
     */
    protected function handle($value = null)
    {
        return 'review-'.$value;
    }
}
