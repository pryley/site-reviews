<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Review;

class ApproveReview extends AbstractCommand
{
    /** @var string */
    public $prevStatus;

    /** @var Review */
    public $review;

    public function __construct(Review $review)
    {
        $this->prevStatus = $review->status;
        $this->review = $review;
    }

    public function handle(): void
    {
        if ($this->review->is_approved) {
            $this->fail();
            return;
        }
        if (!glsr()->can('edit_post', $this->review->ID)) {
            glsr_log()->error('Cannot approve review: Invalid permission.');
            $this->fail();
            return;
        }
        $args = [
            'ID' => $this->review->ID,
            'post_status' => 'publish',
        ];
        $postId = wp_update_post($args, true);
        if (is_wp_error($postId)) {
            glsr_log()->error($postId->get_error_message());
            $this->fail();
            return;
        }
        $message = sprintf(_x('The %sreview%s was approved successfully.', 'admin-text', 'site-reviews'),
            sprintf('<a href="%s">', $this->review->editUrl()), '</a>'
        );
        glsr(Notice::class)->addSuccess($message);
    }
}
