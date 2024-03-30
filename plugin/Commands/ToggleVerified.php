<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\ToggleVerifiedDefaults;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;

class ToggleVerified extends AbstractCommand
{
    public bool $isVerified;
    public Review $review;

    public function __construct(Request $request)
    {
        $args = glsr(ToggleVerifiedDefaults::class)->restrict($request->toArray());
        $review = glsr(ReviewManager::class)->get($args['post_id']);
        $this->isVerified = $args['verified'] >= 0 ? wp_validate_boolean($args['verified']) : !$review->is_verified;
        $this->review = $review;
    }

    public function handle(): void
    {
        if (!glsr()->can('edit_post', $this->review->ID)) {
            $this->isVerified = wp_validate_boolean($this->review->is_verified);
            $this->fail();
            return;
        }
        if (!glsr()->filterBool('verification/enabled', false)) {
            $this->isVerified = wp_validate_boolean($this->review->is_verified);
            $this->fail();
            return;
        }
        if ($this->isVerified !== $this->review->is_verified) {
            glsr(ReviewManager::class)->updateRating($this->review->ID, [
                'is_verified' => $this->isVerified,
            ]);
            $notice = $this->isVerified
                ? _x('Review verified.', 'admin-text', 'site-reviews')
                : _x('Review unverified.', 'admin-text', 'site-reviews');
            glsr(Notice::class)->addSuccess($notice);
        }
    }

    public function response(): array
    {
        return [
            'notices' => glsr(Notice::class)->get(),
            'value' => (int) $this->isVerified,
        ];
    }
}
