<?php

namespace GeminiLabs\SiteReviews\Contracts;

use GeminiLabs\SiteReviews\Review;

interface WebhookContract
{
    /**
     * @return WebhookContract
     */
    public function compose(Review $review, array $notification);

    /**
     * @return \WP_Error|array
     */
    public function send();
}
