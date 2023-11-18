<?php

namespace GeminiLabs\SiteReviews\Integrations\MyCred;

use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Integrations\MyCred\Defaults\AssignedAuthorDefaults;
use GeminiLabs\SiteReviews\Integrations\MyCred\Defaults\AssignedUserDefaults;
use GeminiLabs\SiteReviews\Integrations\MyCred\Defaults\ReviewerDefaults;
use GeminiLabs\SiteReviews\Review;

class MyCredHook extends \myCRED_Hook
{
    public function __construct($preferences, $type = MYCRED_DEFAULT_TYPE_KEY)
    {
        $args = [
            'id' => Str::snakeCase(glsr()->id),
            'defaults' => [
                'reviewer' => glsr(ReviewerDefaults::class)->defaults(),
                'assigned_author' => glsr(AssignedAuthorDefaults::class)->defaults(),
                'assigned_user' => glsr(AssignedUserDefaults::class)->defaults(),
            ],
        ];
        parent::__construct($args, $preferences, $type);
    }

    /**
     * @action site-reviews/review/created
     */
    public function onReviewCreated(Review $review): void
    {
        $review = glsr(ReviewManager::class)->get($review->ID); // get a fresh copy of the review
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
        $review = glsr(ReviewManager::class)->get($review->ID); // get a fresh copy of the review
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
            'step' => Arr::get([1, .1, .01, .001, .0001], $this->core->format['decimals'], 1),
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
        $sanitized['reviewer'] = glsr(ReviewerDefaults::class)->restrict(Arr::get($data, 'reviewer'));
        $sanitized['assigned_author'] = glsr(AssignedAuthorDefaults::class)->restrict(Arr::get($data, 'assigned_author'));
        $sanitized['assigned_user'] = glsr(AssignedUserDefaults::class)->restrict(Arr::get($data, 'assigned_user'));
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
        $points = Arr::get($this->prefs, sprintf('%s.points%s', $key, $suffix), 0);
        $points = $this->core->number($points);
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
        if ($this->userExceedsLimits($review, $isDeduction)) {
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

    protected function userExceedsLimits(Review $review, bool $isDeduction): bool
    {
        if (empty($review->author_id)) {
            return true;
        }
        $limits = [
            'per_day' => [
                date('Y-m-d', strtotime($review->date)),
            ],
            'per_post' => $review->assigned_posts ?: [0],
        ];
        foreach ($limits as $key => $values) {
            if ($this->userLimitExceeded($review->author_id, $isDeduction, $key, $values)) {
                return true;
            }
        }
        return false;
    }

    protected function userLimitExceeded(int $userId, bool $isDeduction, string $key, array $values): bool
    {
        $limit = Arr::getAs('int', $this->prefs, "reviewer.{$key}");
        if (0 === $limit) {
            return false;
        }
        $limitExceeded = true;
        $metaKey = $this->userLimitMetaKey($key);
        $metaValue = Arr::consolidate(mycred_get_user_meta($userId, $metaKey, '', true));
        foreach ($values as $id) {
            $total = Arr::getAs('int', $metaValue, $id, 0);
            $metaValue[$id] = $isDeduction ? max(0, --$total) : ++$total;
            if ($total > $limit) {
                continue;
            }
            if (0 === $total) {
                unset($metaValue[$id]);
            }
            $limitExceeded = false;
        }
        if (false === $limitExceeded) {
            mycred_update_user_meta($userId, $metaKey, '', $metaValue);
        }
        return $limitExceeded;
    }

    protected function userLimitMetaKey(string $key): string
    {
        return $this->is_main_type
            ? sprintf('mycred_review_limit_%s', $key)
            : sprintf('mycred_review_limit_%s_%s', $key, $this->mycred_type);
    }
}
