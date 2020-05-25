<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Helpers\Arr;

/**
 * @property string $avatar;
 * @property string $email
 * @property int $ID
 * @property string $ip_address
 * @property bool $is_approved
 * @property bool $is_pinned
 * @property string $name
 * @property array $post_ids
 * @property int $rating
 * @property int $review_id
 * @property array $term_ids
 * @property string $type
 * @property string $url
 * @property array $user_ids
 */
class Rating extends Arguments
{
    /**
     * @var bool
     */
    protected $hasPostsPivot;

    /**
     * @var bool
     */
    protected $hasTermsPivot;

    /**
     * @var bool
     */
    protected $hasUsersPivot;

    public function __construct(array $args)
    {
        parent::__construct($args);
        $this->normalize();
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        if ('post_ids' === $key) {
            return $this->postIds();
        }
        if ('term_ids' === $key) {
            return $this->termIds();
        }
        if ('user_ids' === $key) {
            return $this->userIds();
        }
        return parent::offsetGet($key);
    }

    /**
     * @param mixed $key
     * @return void
     */
    public function offsetSet($key, $value)
    {
        // This class is read-only
    }

    /**
     * @return Review
     */
    public function review()
    {
        return glsr(ReviewManager::class)->single(get_post($this->review_id));
    }

    /**
     * @return array
     */
    protected function normalize()
    {
        $args = glsr()->args($this->toArray());
        $this->hasPostsPivot = is_array($args->post_ids);
        $this->hasTermsPivot = is_array($args->term_ids);
        $this->hasUsersPivot = is_array($args->user_ids);
        $args->ID = Helper::castToInt($args->ID);
        $args->is_approved = Helper::castToBool($args->is_approved);
        $args->is_pinned = Helper::castToBool($args->is_pinned);
        $args->post_ids = Helper::castToArray($args->post_ids);
        $args->rating = Helper::castToInt($args->rating);
        $args->review_id = Helper::castToInt($args->review_id);
        $args->term_ids = Helper::castToArray($args->term_ids);
        $args->user_ids = Helper::castToArray($args->user_ids);
        $this->exchangeArray($args->toArray());
    }

    /**
     * @return array
     */
    protected function postIds()
    {
        if (!$this->hasPostsPivot) {
            $pivot = glsr(Query::class)->ratingPivot('post_id', 'assigned_posts', $this->ID);
            $this->set('post_ids', Arr::uniqueInt($pivot));
            $this->hasPostsPivot = true;
        }
        return $this->get('post_ids');
    }

    /**
     * @return array
     */
    protected function termIds()
    {
        if (!$this->hasTermsPivot) {
            $pivot = glsr(Query::class)->ratingPivot('term_id', 'assigned_terms', $this->ID);
            $this->set('term_ids', Arr::uniqueInt($pivot));
            $this->hasTermsPivot = true;
        }
        return $this->get('term_ids');
    }

    /**
     * @return array
     */
    protected function userIds()
    {
        if (!$this->hasUsersPivot) {
            $pivot = glsr(Query::class)->ratingPivot('user_id', 'assigned_users', $this->ID);
            $this->set('user_ids', Arr::uniqueInt($pivot));
            $this->hasUsersPivot = true;
        }
        return $this->get('user_ids');
    }
}
