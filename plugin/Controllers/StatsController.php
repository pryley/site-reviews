<?php

namespace GeminiLabs\SiteReviews\Controllers;

use GeminiLabs\SiteReviews\Database\StatsManager;
use GeminiLabs\SiteReviews\Geolocation;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Html\ReviewHtml;
use GeminiLabs\SiteReviews\Review;

class StatsController extends AbstractController
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
        $response = glsr(Geolocation::class)->lookup($review->ip_address);
        if ($response->failed()) {
            return;
        }
        glsr(StatsManager::class)->store($review, $response->body());
    }
}
