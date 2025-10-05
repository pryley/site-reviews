<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\PostMeta;
use GeminiLabs\SiteReviews\Defaults\StatDefaults;
use GeminiLabs\SiteReviews\Geolocation;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;

class GeolocateReview extends AbstractCommand
{
    public array $location;
    public Review $review;

    public function __construct(Request $request)
    {
        $this->location = [];
        $this->review = glsr_get_review($request->review_id);
    }

    public function handle(): void
    {
        if (!$this->review->isValid()) {
            return;
        }
        if (Helper::isLocalIpAddress($this->review->ip_address)) {
            return;
        }
        $response = glsr(Geolocation::class)->lookup($this->review->ip_address);
        if ($response->failed()) {
            return;
        }
        $location = $response->body();
        if (!$this->validate($location)) {
            return;
        }
        $this->location = glsr(StatDefaults::class)->restrict($location);
        $this->location['rating_id'] = $this->review->rating_id;
        glsr(Database::class)->insert('stats', $this->location);
        glsr(PostMeta::class)->set($this->review->ID, 'geolocation',
            array_diff_key($this->location, ['rating_id' => 0])
        );
        glsr()->action('cache/flush', "review_{$this->review->ID}_geolocated", $this->review);
        glsr()->action('review/geolocated', $this->review, $this->location);
        glsr(Notice::class)->addSuccess(_x('Review geolocated.', 'admin-text', 'site-reviews'));
    }

    public function response(): array
    {
        return [
            'location' => $this->location,
            'notices' => glsr(Notice::class)->get(),
        ];
    }

    protected function validate(array $location): bool
    {
        $query = $location['query'] ?? '';
        $status = $location['status'] ?? '';
        return 'success' === $status && !empty($query);
    }
}
