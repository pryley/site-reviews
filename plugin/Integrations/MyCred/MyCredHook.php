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
        $args = [
            'id' => glsr()->id,
            'defaults' => [
                'assigned_author' => glsr(AssignedAuthorDefaults::class)->defaults(),
                'assigned_user' => glsr(AssignedUserDefaults::class)->defaults(),
                'reviewer' => glsr(ReviewerDefaults::class)->defaults(),
            ],
        ];
        parent::__construct($args, $preferences, $type);
    }

    /**
     * @action site-reviews/review/created
     */
    public function onReviewCreated(Review $review): void
    {
        $review = glsr(Query::class)->review($review->ID); // get a fresh copy of the review
        if ($review->is_approved) {
            $this->reviewApproved($review);
        }
    }

    /**
     * @action site-reviews/review/transitioned
     */
    public function onReviewStatusChanged(Review $review, string $new, string $old): void
    {
        if (!in_array('publish', [$new, $old])) {
            return;
        }
        $review = glsr(Query::class)->review($review->ID); // get a fresh copy of the review
        if ('publish' === $new) {
            $this->reviewApproved($review);
        } elseif ('publish' === $old && 'trash' === $new) {
            $this->reviewTrashed($review);
        } elseif ('publish' === $old && 'trash' !== $new) {
            $this->reviewUnapproved($review);
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
        add_action('site-reviews/review/created', [$this, 'onReviewCreated'], 20);
        add_action('site-reviews/review/transitioned', [$this, 'onReviewStatusChanged'], 20, 3);
    }

    public function sanitise_preferences($data)
    {
        $sanitized = [];
        $sanitized['assigned_author'] = glsr(AssignedAuthorDefaults::class)->restrict(Arr::get($data, 'assigned_author'));
        $sanitized['assigned_user'] = glsr(AssignedUserDefaults::class)->restrict(Arr::get($data, 'assigned_user'));
        $sanitized['reviewer'] = glsr(ReviewerDefaults::class)->restrict(Arr::get($data, 'reviewer'));
        return $sanitized;
    }

    protected function getEntryFor(string $key, bool $isDeduction = false): string
    {
        $suffix = $isDeduction ? '_deduction' : '';
        return Arr::getAs('string', $this->prefs, sprintf('%s.log%s', $key, $suffix));
    }

    /**
     * @return int|float
     */
    protected function getPointsFor(string $key, bool $isDeduction = false)
    {
        $suffix = $isDeduction ? '_deduction' : '';
        $points = Arr::getAs('int', $this->prefs, sprintf('%s.points%s', $key, $suffix), 0);
        return $isDeduction
            ? $this->core->zero() - $points
            : $this->core->zero() + $points;
    }

    protected function processAuthorPoints(string $reference, Review $review, bool $isDeduction = false): void
    {
        $points = $this->getPointsFor('assigned_author', $isDeduction);
        if ($points === $this->core->zero()) {
            return;
        }
        foreach ($review->assigned_posts as $postId) {
            $post = mycred_get_post($postId);
            if (Arr::get($post, 'post_author') === $review->author_id) {
                continue;
            }
            $this->core->add_creds(
                $reference,
                $post->post_author,
                $points,
                $this->getEntryFor('assigned_author', $isDeduction),
                $postId,
                [
                    'ref_type' => 'post',
                    'review_id' => $review->ID,
                ],
                $this->mycred_type
            );
        }
    }

    protected function processReviewerPoints(string $reference, Review $review, bool $isDeduction = false): void
    {
        $points = $this->getPointsFor('reviewer', $isDeduction);
        if ($points === $this->core->zero()) {
            return;
        }
        if ($this->userExceedsLimits($review)) {
            return;
        }
        $this->core->add_creds(
            $reference,
            $review->author_id,
            $points,
            $this->getEntryFor('reviewer', $isDeduction),
            $review->ID,
            [
                'ref_type' => glsr()->post_type,
            ],
            $this->mycred_type
        );
    }

    protected function processUserPoints(string $reference, Review $review, bool $isDeduction = false): void
    {
        $points = $this->getPointsFor('assigned_user', $isDeduction);
        if ($points === $this->core->zero()) {
            return;
        }
        foreach ($review->assigned_users as $userId) {
            if ($userId === $review->author_id) {
                continue;
            }
            $this->core->add_creds(
                $reference,
                $userId,
                $points,
                $this->getEntryFor('assigned_user', $isDeduction),
                $review->ID,
                [
                    'ref_type' => glsr()->post_type,
                ],
                $this->mycred_type
            );
        }
    }

    protected function reviewApproved(Review $review): void
    {
        $this->processReviewerPoints('review_approved', $review, false);
        $this->processAuthorPoints('review_approved', $review, false);
        $this->processUserPoints('review_approved', $review, false);
    }

    protected function reviewTrashed(Review $review): void
    {
        $this->processReviewerPoints('review_trashed', $review, true);
        $this->processAuthorPoints('review_trashed', $review, true);
        $this->processUserPoints('review_trashed', $review, true);
    }

    protected function reviewUnapproved(Review $review): void
    {
        $this->processReviewerPoints('review_unapproved', $review, true);
        $this->processAuthorPoints('review_unapproved', $review, true);
        $this->processUserPoints('review_unapproved', $review, true);
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
        $perDay = Arr::getAs('int', $this->prefs, 'reviewer.per_day');
        if (0 === $perDay) {
            return false;
        }
        $today = date('Y-m-d', current_time('timestamp'));
        $metaKey = $this->userLimitMetaKey('per_day');
        $metaValue = Arr::consolidate(mycred_get_user_meta($review->author_id, $metaKey, '', true));
        $limit = Arr::getAs('int', $metaValue, $today, 0);
        if ($limit >= $perDay) {
            return true;
        }
        $metaValue = []; // we are only concerned about today
        $metaValue[$today] = $limit++;
        mycred_update_user_meta($review->author_id, $metaKey, '', $metaValue);
        return false;
    }

    protected function userExceedsPerPostLimit(Review $review): bool
    {
        $perPost = Arr::getAs('int', $this->prefs, 'reviewer.per_post');
        if (0 === $perPost) {
            return false;
        }
        $metaKey = $this->userLimitMetaKey('per_post');
        $metaValue = Arr::consolidate(mycred_get_user_meta($review->author_id, $metaKey, '', true));
        $exceededLimit = true;
        $postIds = $review->assigned_posts ?: [0];
        foreach ($postIds as $postId) {
            $limit = Arr::getAs('int', $metaValue, $postId, 0);
            if ($limit < $perPost) {
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
