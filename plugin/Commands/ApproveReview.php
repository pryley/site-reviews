<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Modules\Notice;
use GeminiLabs\SiteReviews\Review;

class ApproveReview implements Contract
{
    public $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    /**
     * @return bool
     */
    public function handle()
    {
        if ($this->review->is_approved) {
            return false;
        }
        if (!glsr()->can('edit_post', $this->review->ID)) {
            glsr_log()->error('Cannot approve review: Invalid permission.');
            return false;
        }
        $args = [
            'ID' => $this->review->ID,
            'post_status' => 'publish',
        ];
        $postId = wp_update_post($args, true);
        if (is_wp_error($postId)) {
            glsr_log()->error($postId->get_error_message());
            return false;
        }
        $message = sprintf(_x('The %sreview%s was approved successfully.', 'admin-text', 'site-reviews'),
            sprintf('<a href="%s">', $this->review->editUrl()), '</a>'
        );
        glsr(Notice::class)->addSuccess($message);
        return true;
    }
}
