<?php

namespace GeminiLabs\SiteReviews\Commands;

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Metaboxes\ResponseMetabox;
use GeminiLabs\SiteReviews\Modules\Avatar;
use GeminiLabs\SiteReviews\Review;

/**
 * A review saved from its edit screen.
 */
class UpdateReview extends AbstractCommand
{
    protected \WP_Post $oldPost;
    protected Review $review;

    public function __construct(Review $review, \WP_Post $oldPost)
    {
        $this->oldPost = $oldPost;
        $this->review = $review;
    }

    public function handle(): void
    {
        $data = $this->data();
        if (!empty($data)) {
            glsr(ReviewManager::class)->updateCustom($this->review->ID, $data); // values are sanitized here
            glsr(ReviewManager::class)->updateGeolocation($this->review->ID, $data); // queues new geolocation request if needed
            glsr(ReviewManager::class)->updateRating($this->review->ID, $data); // values are sanitized here
            $this->review->refresh();
        }
        $assignedPostIds = filter_input(\INPUT_POST, 'post_ids', \FILTER_SANITIZE_NUMBER_INT, \FILTER_FORCE_ARRAY);
        $assignedUserIds = filter_input(\INPUT_POST, 'user_ids', \FILTER_SANITIZE_NUMBER_INT, \FILTER_FORCE_ARRAY);
        glsr()->action('review/updated/post_ids', $this->review, Cast::toArray($assignedPostIds)); // trigger a recount of assigned posts
        glsr()->action('review/updated/user_ids', $this->review, Cast::toArray($assignedUserIds)); // trigger a recount of assigned users
        glsr(ResponseMetabox::class)->save($this->review);
        $this->review->refresh();
        glsr()->action('review/updated', $this->review, $data, $this->oldPost);
    }

    protected function data(): array
    {
        $customDefaults = array_fill_keys(array_keys($this->review->custom()->toArray()), ''); // preserve saved empty custom key values
        $data = Helper::filterInputArray(glsr()->id);
        $data = wp_parse_args($data, $customDefaults);
        if (Arr::get($data, 'is_editing_review')) {
            $data['avatar'] = $this->refreshAvatar($data);
            $data['rating'] ??= '';
            $data['terms'] ??= 0;
        }
        if (Arr::getAs('bool', $data, 'is_pinned') === $this->review->is_pinned) {
            unset($data['is_pinned']);
        }
        if (Arr::getAs('bool', $data, 'is_verified') === $this->review->is_verified || !glsr()->filterBool('verification/enabled', false)) {
            unset($data['is_verified']);
        }
        return $data;
    }

    /**
     * The initials fallback avatar follows the author's name.
     */
    protected function refreshAvatar(array $data): string
    {
        $avatarUrl = Cast::toString($data['avatar'] ?? '');
        if ($this->review->author === ($data['name'] ?? false)) {
            return $avatarUrl;
        }
        $url = preg_replace('/(.*)\/site-reviews\/avatars\/[\p{L&}]+\.svg$/u', '', $avatarUrl);
        if (!empty($url)) {
            return $avatarUrl;
        }
        $this->review->set('author', $data['name'] ?? '');
        return glsr(Avatar::class)->generateInitials($this->review);
    }
}
