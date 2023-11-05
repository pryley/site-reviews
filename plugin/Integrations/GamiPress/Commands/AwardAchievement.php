<?php

namespace GeminiLabs\SiteReviews\Integrations\GamiPress\Commands;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Commands\AbstractCommand;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Review;

class AwardAchievement extends AbstractCommand
{
    /** @var array */
    public $assignedPosts;

    /** @var array */
    public $assignedPostsAuthors;

    /** @var array */
    public $assignedPostsTypes;

    /** @var array */
    public $assignedUsers;

    /** @var array */
    public $assignedUsersRoles;

    /** @var int */
    public $requiredPostId;

    /** @var string */
    public $requiredPostType;

    /** @var int */
    public $requiredRating;

    /** @var string */
    public $requiredRatingCondition;

    /** @var int */
    public $requiredUserId;

    /** @var string */
    public $requiredUserRole;

    /** @var Review */
    public $review;

    /** @var string */
    public $trigger;

    /** @var int */
    public $userId;

    public function __construct(string $trigger, int $userId, Arguments $requirements, Arguments $args)
    {
        $this->assignedPosts = $args->sanitize('assigned_posts', 'array-int');
        $this->assignedPostsAuthors = $args->sanitize('assigned_posts_authors', 'array-int');
        $this->assignedPostsTypes = $args->cast('assigned_posts_types', 'array');
        $this->assignedUsers = $args->sanitize('assigned_users', 'array-int');
        $this->assignedUsersRoles = $args->cast('assigned_users_roles', 'array');
        $this->requiredPostId = $requirements->cast('post_id', 'int');
        $this->requiredPostType = $requirements->cast('post_type', 'string');
        $this->requiredRating = $requirements->cast('rating', 'int');
        $this->requiredRatingCondition = $requirements->get('rating_condition', 'any');
        $this->requiredUserId = $requirements->cast('user_id', 'int');
        $this->requiredUserRole = $requirements->cast('user_role', 'string');
        $this->review = $args->review;
        $this->trigger = $trigger;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        $trigger = str_replace(['site_reviews_gamipress', '/'], ['award', '-'], $this->trigger);
        $method = Helper::buildMethodName($trigger);
        if (method_exists($this, $method)) {
            $this->result = call_user_func([$this, $method]);
        } else {
            $this->fail();
        }
    }

    protected function awardReceivedPost(): bool
    {
        return in_array($this->userId, $this->assignedPostsAuthors)
            && $this->isRequiredRating();
    }

    protected function awardReceivedPostId(): bool
    {
        return in_array($this->userId, $this->assignedPostsAuthors)
            && in_array($this->requiredPostId, $this->review->assigned_posts)
            && $this->isRequiredRating();
    }

    protected function awardReceivedPostType(): bool
    {
        return in_array($this->userId, $this->assignedPostsAuthors)
            && in_array($this->requiredPostType, $this->assignedPostsTypes)
            && $this->isRequiredRating();
    }

    protected function awardReceivedUser(): bool
    {
        return in_array($this->userId, $this->assignedUsers)
            && $this->isRequiredRating();
    }

    protected function awardReviewedAny(): bool
    {
        return $this->userId === $this->review->author_id
            && $this->isRequiredRating();
    }

    protected function awardReviewedPost(): bool
    {
        return $this->userId === $this->review->author_id
            && !empty($this->assignedPosts)
            && $this->isRequiredRating();
    }

    protected function awardReviewedPostId(): bool
    {
        return $this->userId === $this->review->author_id
            && in_array($this->requiredPostId, $this->assignedPosts)
            && $this->isRequiredRating();
    }

    protected function awardReviewedPostType(): bool
    {
        return $this->userId === $this->review->author_id
            && in_array($this->requiredPostType, $this->assignedPostsTypes)
            && $this->isRequiredRating();
    }

    protected function awardReviewedUser(): bool
    {
        return $this->userId === $this->review->author_id
            && !empty($this->assignedUsers)
            && $this->isRequiredRating();
    }

    protected function awardReviewedUserId(): bool
    {
        return $this->userId === $this->review->author_id
            && in_array($this->userId, $this->assignedUsers)
            && $this->isRequiredRating();
    }

    protected function awardReviewedUserRole(): bool
    {
        return $this->userId === $this->review->author_id
            && in_array($this->requiredUserRole, $this->assignedUsersRoles)
            && $this->isRequiredRating();
    }

    protected function isRequiredRating(): bool
    {
        if ('exact' === $this->requiredRatingCondition) {
            return $this->review->rating === $this->requiredRating;
        }
        if ('minimum' === $this->requiredRatingCondition) {
            return $this->review->rating >= $this->requiredRating;
        }
        return true;
    }
}
