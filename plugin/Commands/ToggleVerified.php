<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\ToggleVerifiedDefaults;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;

class ToggleVerified extends AbstractCommand
{
    /** @var Review */
    public $review;

    public function __construct(Request $request)
    {
        $args = glsr(ToggleVerifiedDefaults::class)->restrict($request->toArray());
        $review = glsr(ReviewManager::class)->get($args['id']);
        $this->result = $args['verified'] >= 0 ? wp_validate_boolean($args['verified']) : !$review->is_verified;
        $this->review = $review;
    }

    public function handle(): void
    {
        if (!glsr()->can('edit_post', $this->review->ID)) {
            $this->result = wp_validate_boolean($this->review->is_verified);
            return;
        }
        if (!glsr()->filterBool('verification/enabled', false)) {
            $this->result = wp_validate_boolean($this->review->is_verified);
            return;
        }
        if ($this->result !== $this->review->is_verified) {
            glsr(ReviewManager::class)->updateRating($this->review->ID, [
                'is_verified' => $this->result,
            ]);
            $notice = $this->result
                ? _x('Review verified.', 'admin-text', 'site-reviews')
                : _x('Review unverified.', 'admin-text', 'site-reviews');
            glsr(Notice::class)->addSuccess($notice);
        }
    }
}
