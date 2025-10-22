<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Request;
use GeminiLabs\SiteReviews\Review;

class TogglePinned extends AbstractCommand
{
    public bool $isPinned;
    public Review $review;

    public function __construct(Request $request)
    {
        $pinned = $request->cast('pinned', 'int', -1);
        $this->review = glsr_get_review($request->post_id);
        $this->isPinned = $pinned >= 0 ? wp_validate_boolean($pinned) : !$this->review->is_pinned;
    }

    public function handle(): void
    {
        if (!$this->review->isValid()) {
            glsr_log()->error(sprintf('Cannot %s review: Invalid review', $this->isPinned ? 'pin' : 'unpin'));
            $this->fail();
            return;
        }
        if (!glsr()->can('edit_post', $this->review->ID)) {
            glsr_log()->error(sprintf('Cannot %s review: Invalid permission', $this->isPinned ? 'pin' : 'unpin'));
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
            $this->fail();
            return;
        }
        $this->review->set('is_pinned', $this->isPinned);
        if ($this->isPinned) {
            glsr()->action('cache/flush', "review_{$this->review->ID}_pinned", $this->review);
            glsr(Notice::class)->addSuccess(_x('Review pinned.', 'admin-text', 'site-reviews'));
        } else {
            glsr()->action('cache/flush', "review_{$this->review->ID}_unpinned", $this->review);
            glsr(Notice::class)->addSuccess(_x('Review unpinned.', 'admin-text', 'site-reviews'));
        }
        glsr()->action('review/pinned', $this->review, $this->isPinned);
    }

    public function response(): array
    {
        return [
            'notices' => glsr(Notice::class)->get(),
            'value' => (int) $this->isPinned,
        ];
    }
}
