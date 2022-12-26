<?php

namespace GeminiLabs\SiteReviews\Integrations\GamiPress\Commands;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Contracts\CommandContract as Contract;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Review;

class AwardAchievement implements Contract
{
    /**
     * @var array
     */
    public $assignedPosts;

    /**
     * @var array
     */
    public $assignedPostsAuthors;

    /**
     * @var array
     */
    public $assignedPostsTypes;

    /**
     * @var array
     */
    public $assignedUsers;

    /**
     * @var array
     */
    public $assignedUsersRoles;

    /**
     * @var int
     */
    public $requiredPostId;

    /**
     * @var string
     */
    public $requiredPostType;

    /**
     * @var int
     */
    public $requiredRating;

    /**
     * @var string
     */
    public $requiredRatingCondition;

    /**
     * @var int
     */
    public $requiredUserId;

    /**
     * @var string
     */
    public $requiredUserRole;

    /**
     * @var Review
     */
    public $review;

    /**
     * @var string
     */
    public $trigger;

    /**
     * @var int
     */
    public $userId;

    /**
     * @param string $trigger
     * @param int $userId
     */
    public function __construct($trigger, $userId, Arguments $requirements, Arguments $args)
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

    /**
     * @return bool
     */
    public function handle()
    {
        $method = Helper::buildMethodName(
            str_replace(['site_reviews_gamipress', '/'], ['award', '-'], $this->trigger)
        );
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method]);
        }
        return false;
    }

    /**
     * @return bool
     */
    protected function awardReceivedPost()
    {
        return in_array($this->userId, $this->assignedPostsAuthors)
            && $this->isRequiredRating();
    }

    /**
     * @return bool
     */
    protected function awardReceivedPostId()
    {
        return in_array($this->userId, $this->assignedPostsAuthors)
            && in_array($this->requiredPostId, $this->review->assigned_posts)
            && $this->isRequiredRating();
    }

    /**
     * @return bool
     */
    protected function awardReceivedPostType()
    {
        return in_array($this->userId, $this->assignedPostsAuthors)
            && in_array($this->requiredPostType, $this->assignedPostsTypes)
            && $this->isRequiredRating();
    }

    /**
     * @return bool
     */
    protected function awardReceivedUser()
    {
        return in_array($this->userId, $this->assignedUsers)
            && $this->isRequiredRating();
    }

    /**
     * @return bool
     */
    protected function awardReviewedAny()
    {
        return $this->userId === $this->review->author_id
            && $this->isRequiredRating();
    }

    /**
     * @return bool
     */
    protected function awardReviewedPost()
    {
        return $this->userId === $this->review->author_id
            && !empty($this->assignedPosts)
            && $this->isRequiredRating();
    }

    /**
     * @return bool
     */
    protected function awardReviewedPostId()
    {
        return $this->userId === $this->review->author_id
            && in_array($this->requiredPostId, $this->assignedPosts)
            && $this->isRequiredRating();
    }

    /**
     * @return bool
     */
    protected function awardReviewedPostType()
    {
        return $this->userId === $this->review->author_id
            && in_array($this->requiredPostType, $this->assignedPostsTypes)
            && $this->isRequiredRating();
    }

    /**
     * @return bool
     */
    protected function awardReviewedUser()
    {
        return $this->userId === $this->review->author_id
            && !empty($this->assignedUsers)
            && $this->isRequiredRating();
    }

    /**
     * @return bool
     */
    protected function awardReviewedUserId()
    {
        return $this->userId === $this->review->author_id
            && in_array($this->userId, $this->assignedUsers)
            && $this->isRequiredRating();
    }

    /**
     * @return bool
     */
    protected function awardReviewedUserRole()
    {
        return $this->userId === $this->review->author_id
            && in_array($this->requiredUserRole, $this->assignedUsersRoles)
            && $this->isRequiredRating();
    }

    /**
     * @return bool
     */
    protected function isRequiredRating()
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
