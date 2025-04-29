<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\ReviewHtml;
use GeminiLabs\SiteReviews\Modules\Queue;
use GeminiLabs\SiteReviews\Review;

class GeolocationController extends AbstractController
{
    /**
     * @filter site-reviews/review/build/after
     */
    public function filterReviewTemplateTags(array $tags, Review $review, ReviewHtml $html): array
    {
        $tags['location'] = $html->buildTemplateTag($review, 'location', '');
        return $tags;
    }

    /**
     * @action site-reviews/review/created
     */
    public function geolocateReview(Review $review): void
    {
        if (defined('WP_IMPORTING')) {
            return;
        }
        if (Helper::isLocalIpAddress($review->ip_address)) {
            return;
        }
        glsr(Queue::class)->once(time(), 'queue/geolocation', ['review_id' => $review->ID], true);
    }
}
