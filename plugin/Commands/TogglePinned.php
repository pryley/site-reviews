<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\TogglePinnedDefaults;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;

class TogglePinned extends AbstractCommand
{
    public bool $isPinned;
    public Review $review;

    public function __construct(Request $request)
    {
        $args = glsr(TogglePinnedDefaults::class)->restrict($request->toArray());
        $review = glsr(ReviewManager::class)->get($args['post_id']);
        $this->isPinned = $args['pinned'] >= 0 ? wp_validate_boolean($args['pinned']) : !$review->is_pinned;
        $this->review = $review;
    }

    public function handle(): void
    {
        if (!glsr()->can('edit_post', $this->review->ID)) {
            $this->isPinned = wp_validate_boolean($this->review->is_pinned);
            $this->fail();
            return;
        }
        if ($this->isPinned === $this->review->is_pinned) {
            return;
        }
        $result = glsr(ReviewManager::class)->updateRating($this->review->ID, [
            'is_pinned' => $this->isPinned,
        ]);
        if ($result <= 0) {
            glsr(Notice::class)->addError('Something went wrong: unable to pin/unpin review');
            return;
        }
        $this->review->set('is_pinned', $this->isPinned); // quick and dirty
        glsr()->action('review/pinned', $this->review, $this->isPinned);
        $notice = $this->isPinned
            ? _x('Review pinned.', 'admin-text', 'site-reviews')
            : _x('Review unpinned.', 'admin-text', 'site-reviews');
        glsr(Notice::class)->addSuccess($notice);
    }

    public function response(): array
    {
        return [
            'notices' => glsr(Notice::class)->get(),
            'value' => (int) $this->isPinned,
        ];
    }
}
