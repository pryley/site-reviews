<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\TogglePinnedDefaults;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;

class TogglePinned extends AbstractCommand
{
    /** @var Review */
    public $review;

    public function __construct(Request $request)
    {
        $args = glsr(TogglePinnedDefaults::class)->restrict($request->toArray());
        $review = glsr(Query::class)->review($args['id']);
        $this->result = $args['pinned'] >= 0 ? wp_validate_boolean($args['pinned']) : !$review->is_pinned;
        $this->review = $review;
    }

    public function handle(): void
    {
        if (!glsr()->can('edit_post', $this->review->ID)) {
            $this->result = wp_validate_boolean($this->review->is_pinned);
            return;
        }
        if ($this->result !== $this->review->is_pinned) {
            glsr(ReviewManager::class)->updateRating($this->review->ID, [
                'is_pinned' => $this->result,
            ]);
            $notice = $this->result
                ? _x('Review pinned.', 'admin-text', 'site-reviews')
                : _x('Review unpinned.', 'admin-text', 'site-reviews');
            glsr(Notice::class)->addSuccess($notice);
        }
    }
}
