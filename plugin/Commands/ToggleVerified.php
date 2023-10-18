<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\ToggleVerifiedDefaults;
use GeminiLabs\SiteReviews\Modules\Notice;

class ToggleVerified implements Contract
{
    public $isVerified;
    public $review;

    public function __construct(array $input)
    {
        $args = glsr()->args(glsr(ToggleVerifiedDefaults::class)->restrict($input));
        $this->review = glsr(Query::class)->review($args->id);
        $this->isVerified = $args->verified >= 0
            ? wp_validate_boolean($args->verified)
            : !$this->review->is_verified;
    }

    /**
     * @return bool
     */
    public function handle()
    {
        if (!glsr()->can('edit_post', $this->review->ID)) {
            return wp_validate_boolean($this->review->is_verified);
        }
        if (!glsr()->filterBool('verification/enabled', false)) {
            return wp_validate_boolean($this->review->is_verified);
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
        return $this->isVerified;
    }
}
