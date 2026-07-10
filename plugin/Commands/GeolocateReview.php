<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database;
use GeminiLabs\SiteReviews\Database\PostMeta;
use GeminiLabs\SiteReviews\Defaults\StatDefaults;
use GeminiLabs\SiteReviews\Geolocation;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Modules\Queue;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Response;
use GeminiLabs\SiteReviews\Review;

class GeolocateReview extends AbstractCommand
{
    /**
     * Maximum consecutive failed lookup attempts before giving up.
     */
    public const MAX_RETRIES = 3;

    /**
     * Key used for the queued action.
     */
    public const QUEUED_ACTION_KEY = 'queue/geolocation';

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
            $this->retryLookup($response);
            return;
        }
        delete_transient($this->retryKey());
        $location = $response->body();
        if (!$this->validate($location)) {
            return;
        }
        $this->location = glsr(StatDefaults::class)->restrict($location);
        $this->location['rating_id'] = $this->review->rating_id;
        glsr(Database::class)->delete('stats', [
            'rating_id' => $this->review->rating_id,
        ]);
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

    protected function retryKey(): string
    {
        return glsr()->prefix."geolocation_retry_{$this->review->ID}";
    }

    /**
     * Reschedule a failed lookup instead of giving up.
     * Gives up after MAX_RETRIES consecutive failures.
     */
    protected function retryLookup(Response $response): void
    {
        $retries = (int) get_transient($this->retryKey());
        if ($retries >= static::MAX_RETRIES) {
            delete_transient($this->retryKey());
            glsr_log()->error("Geolocation: Giving up on review {$this->review->ID} after {$retries} failed attempts.");
            return;
        }
        set_transient($this->retryKey(), $retries + 1, \HOUR_IN_SECONDS);
        $delay = max((int) ($response->headers['x-ttl'] ?? 0), 60);
        glsr(Queue::class)->once(time() + $delay, static::QUEUED_ACTION_KEY, ['review_id' => $this->review->ID], true);
    }

    protected function validate(array $location): bool
    {
        $query = $location['query'] ?? '';
        $status = $location['status'] ?? '';
        return 'success' === $status && !empty($query);
    }
}
