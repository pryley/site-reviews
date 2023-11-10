<?php

namespace GeminiLabs\SiteReviews\Contracts;

use GeminiLabs\SiteReviews\Review;

interface WebhookContract
{
    /**
     * @return static
     */
    public function compose(Review $review, array $notification);

    public function send(): bool;
}
