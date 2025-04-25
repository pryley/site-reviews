<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Defaults\GeolocationDefaults;
use GeminiLabs\SiteReviews\Geolocation;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Review;

class StatsManager
{
    public function location(Review $review): array
    {
        $location = get_post_meta($review->ID, '_geolocation', true);
        if (!empty($location)) {
            return glsr(GeolocationDefaults::class)->restrict($location);
        }
        $fallback = glsr(GeolocationDefaults::class)->defaults();
        if (Helper::isLocalIpAddress($review->ip_address)) {
            return $fallback;
        }
        $response = glsr(Geolocation::class)->lookup($review->ip_address);
        if ($response->failed()) {
            return $fallback;
        }
        $location = $response->body();
        if (!$this->store($review, $location)) {
            return $fallback;
        }
        return glsr(GeolocationDefaults::class)->restrict($location);
    }

    public function store(Review $review, array $values): bool
    {
        $values = glsr(GeolocationDefaults::class)->restrict($values);
        $data = [
            'rating_id' => $review->rating_id,
        ];
        $data = wp_parse_args($data, $values);
        $result = glsr(Database::class)->insert('stats', $data);
        if (false === $result) {
            return false;
        }
        update_post_meta($review->ID, '_geolocation', $values);
        return true;
    }
}
