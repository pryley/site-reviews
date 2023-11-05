<?php

namespace GeminiLabs\SiteReviews\Contracts;

use GeminiLabs\SiteReviews\Review;

interface WebhookContract
{
    public function compose(Review $review, array $notification): self;

    public function send(): bool;
}
