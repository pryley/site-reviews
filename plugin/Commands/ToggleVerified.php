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
        if (!$this->review->isValid()) {
            glsr_log()->error('Cannot toggle verified status: Invalid review');
            $this->fail();
            return;
        }
        if (!glsr()->can('edit_post', $this->review->ID)) {
            glsr_log()->error('Cannot toggle verified status: Invalid permission');
            $this->isVerified = wp_validate_boolean($this->review->is_verified);
            $this->fail();
            return;
        }
        if (!glsr()->filterBool('verification/enabled', false)) {
            glsr_log()->error('Cannot toggle verified status: Verification manually disabled with a filter hook');
            $this->isVerified = wp_validate_boolean($this->review->is_verified);
            $this->fail();
            return;
        }
        if ($this->isVerified === $this->review->is_verified) {
            return;
        }
        $result = glsr(ReviewManager::class)->updateRating($this->review->ID, [
            'is_verified' => $this->isVerified,
        ]);
        if (0 === $result) {
            $this->fail();
            return;
        }
        $this->review->set('is_verified', $this->isVerified);
        if ($this->isVerified) {
            glsr()->action('cache/flush', "review_{$this->review->ID}_verified", $this->review);
            glsr()->action('review/verified', $this->review);
            glsr(Notice::class)->addSuccess(_x('Review verified.', 'admin-text', 'site-reviews'));
        } else {
            glsr()->action('cache/flush', "review_{$this->review->ID}_unverified", $this->review);
            glsr(Notice::class)->addSuccess(_x('Review unverified.', 'admin-text', 'site-reviews'));
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
