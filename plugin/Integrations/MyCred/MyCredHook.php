<?php

namespace GeminiLabs\SiteReviews\Integrations\MyCred;

use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Integrations\MyCred\Defaults\AssignedAuthorDefaults;
use GeminiLabs\SiteReviews\Integrations\MyCred\Defaults\AssignedUserDefaults;
use GeminiLabs\SiteReviews\Integrations\MyCred\Defaults\ReviewerDefaults;
use GeminiLabs\SiteReviews\Review;

class MyCredHook extends \myCRED_Hook
{
    public function __construct($args = [], $preferences = null, $type = MYCRED_DEFAULT_TYPE_KEY)
    {
        $defaults = [
            'id' => glsr()->id,
            'defaults' => [
                'assigned_author' => glsr(AssignedAuthorDefaults::class)->defaults(),
                'assigned_user' => glsr(AssignedUserDefaults::class)->defaults(),
                'reviewer' => glsr(ReviewerDefaults::class)->defaults(),
            ],
        ];
        parent::__construct($defaults, $preferences, $type);
    }

    /**
     * @action site-reviews/review/approved
     */
    public function onReviewApproved(Review $review, string $prevStatus): void
    {
        // $review = glsr(Query::class)->review($review->ID); // get a fresh copy of the review
    }

    /**
     * @action site-reviews/review/created
     */
    public function onReviewCreated(Review $review): void
    {
        $review = glsr(Query::class)->review($review->ID); // get a fresh copy of the review
        if (!$review->is_approved) {
            return;
        }
        if (!$this->userExceedsLimits($review)) {
            $this->processPoints('reviewer', $review, $review->author_id);
        }
        foreach ($review->assigned_posts as $postId) {
            $post = mycred_get_post($postId);
            if ($post && $post->post_author !== $review->author_id) {
                $this->processPoints('assigned_author', $review, $post->post_author);
            }
        }
        foreach ($review->assigned_users as $userId) {
            if ($userId !== $review->author_id) {
                $this->processPoints('assigned_user', $review, $userId);
            }
        }
    }

    /**
     * @action site-reviews/review/trashed
     */
    public function onReviewTrashed(Review $review, string $prevStatus): void
    {
        $review = glsr(Query::class)->review($review->ID); // get a fresh copy of the review
        if (!empty($this->prefs['reviewer']['remove_on_trash'])) {
            $this->removePoints('reviewer', $review, $review->author_id);
        }
    }

    public function preferences()
    {
        glsr()->render('integrations/mycred/preferences', [
            'hook' => $this,
        ]);
    }

    public function run()
    {
        add_action('site-reviews/review/approved', [$this, 'onReviewApproved'], 20, 2);
        add_action('site-reviews/review/created', [$this, 'onReviewCreated'], 20);
        add_action('site-reviews/review/trashed', [$this, 'onReviewTrashed'], 20, 2);
    }

    public function sanitise_preferences($data)
    {
        $sanitized = [];
        $sanitized['assigned_author'] = glsr(AssignedAuthorDefaults::class)->mergeRestricted(Arr::get($data, 'assigned_author'));
        $sanitized['assigned_user'] = glsr(AssignedUserDefaults::class)->mergeRestricted(Arr::get($data, 'assigned_user'));
        $sanitized['reviewer'] = glsr(ReviewerDefaults::class)->mergeRestricted(Arr::get($data, 'reviewer'));
        return $sanitized;
    }

    protected function processPoints(string $ref, Review $review, int $userId): void
    {
        $data = array_filter([
            'ref_type' => glsr()->post_type,
        ]);
        $points = $this->core->zero() + $this->prefs[$ref]['points'];
        if (empty($points)) {
            return;
        }

        // if ($this->over_hook_limit('', 'site_reviews', $userId)) {}

        $this->core->add_creds(
            $ref,
            $userId,
            $points,
            $this->prefs[$ref]['log'],
            $review->ID,
            $data,
            $this->mycred_type
        );
    }

    protected function removePoints(string $ref, Review $review, int $userId): void
    {
    }

    protected function userExceedsLimits(Review $review): bool
    {
        if (empty($review->author_id)) {
            return true;
        }
        if ($this->userExceedsPerPostLimit($review)) {
            return true;
        }
        if ($this->userExceedsPerDayLimit($review)) {
            return true;
        }
        return false;
    }

    protected function userExceedsPerDayLimit(Review $review): bool
    {
        if (0 === $this->prefs['reviewer']['per_day']) {
            return false;
        }
        $today = date('Y-m-d', current_time('timestamp'));
        $metaKey = $this->userLimitMetaKey('per_day');
        $metaValue = Arr::consolidate(mycred_get_user_meta($review->author_id, $metaKey, '', true));
        $limit = Arr::getAs('int', $metaValue, $today, 0);
        if ($limit >= $this->prefs['reviewer']['per_day']) {
            return true;
        }
        $metaValue = []; // we are only concerned about today
        $metaValue[$today] = $limit++;
        mycred_update_user_meta($review->author_id, $metaKey, '', $metaValue);
        return false;
    }

    protected function userExceedsPerPostLimit(Review $review): bool
    {
        if (0 === $this->prefs['reviewer']['per_post']) {
            return false;
        }
        $metaKey = $this->userLimitMetaKey('per_post');
        $metaValue = Arr::consolidate(mycred_get_user_meta($review->author_id, $metaKey, '', true));
        $exceededLimit = true;
        $postIds = $review->assigned_posts ?: [0];
        foreach ($postIds as $postId) {
            $limit = Arr::getAs('int', $metaValue, $postId, 0);
            if ($limit < $this->prefs['reviewer']['per_post']) {
                $exceededLimit = false;
                $metaValue[$postId] = $limit++;
            }
        }
        mycred_update_user_meta($review->author_id, $metaKey, '', $metaValue);
        return $exceededLimit;
    }

    protected function userLimitMetaKey(string $key): string
    {
        return $this->is_main_type
            ? sprintf('mycred_review_limit_%s', $key)
            : sprintf('mycred_review_limit_%s_%s', $key, $this->mycred_type);
    }
}
