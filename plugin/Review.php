<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\RatingManager;
use GeminiLabs\SiteReviews\Defaults\CreateReviewDefaults;
use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Partials\SiteReviews as SiteReviewsPartial;
use GeminiLabs\SiteReviews\Modules\Html\ReviewHtml;
use WP_Post;

class Review implements \ArrayAccess
{
    public $assigned_post_ids;
    public $assigned_term_ids;
    public $assigned_user_ids;
    public $author;
    public $avatar;
    public $content;
    public $custom;
    public $date;
    public $email;
    public $ID;
    public $ip_address;
    public $modified;
    public $pinned;
    public $rating;
    public $rating_id;
    public $response;
    public $status;
    public $title;
    public $type;
    public $url;
    public $user_id;

    public function __construct(WP_Post $post)
    {
        if (Application::POST_TYPE !== $post->post_type) {
            return;
        }
        $rating = glsr(Query::class)->rating($post->ID);
        $this->assigned_post_ids = $rating->post_ids;
        $this->assigned_term_ids = $rating->term_ids;
        $this->assigned_user_ids = $rating->user_ids;
        $this->content = $post->post_content;
        $this->date = $post->post_date;
        $this->ID = absint($post->ID);
        $this->rating = $rating->rating;
        $this->rating_id = $rating->ID;
        $this->status = $post->post_status;
        $this->title = $post->post_title;
        $this->type = $rating->type;
        $this->user_id = absint($post->post_author);
        $this->setProperties($post);
    }

    /**
     * @return mixed
     */
    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->build();
    }

    /**
     * @return ReviewHtml
     */
    public function build(array $args = [])
    {
        if (empty($this->ID)) {
            return new ReviewHtml($this);
        }
        $partial = glsr(SiteReviewsPartial::class);
        $partial->args = glsr(SiteReviewsDefaults::class)->merge($args);
        $partial->options = Arr::flatten(glsr(OptionManager::class)->all());
        return $partial->buildReview($this);
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return property_exists($this, $key) || array_key_exists($key, (array) $this->custom);
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return property_exists($this, $key)
            ? $this->$key
            : Arr::get($this->custom, $key, null);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (property_exists($this, $key)) {
            $this->$key = $value;
            return;
        }
        if (!is_array($this->custom)) {
            $this->custom = array_filter((array) $this->custom);
        }
        $this->custom[$key] = $value;
    }

    /**
     * @param mixed $key
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->offsetSet($key, null);
    }

    /**
     * @return \GeminiLabs\SiteReviews\Rating|false
     */
    public function rating()
    {
        return glsr(RatingManager::class)->get($this->ID);
    }

    /**
     * @return void
     */
    public function render()
    {
        echo $this->build();
    }

    /**
     * @return bool
     */
    protected function isModified(array $properties)
    {
        return $this->content !== $properties['content']
            || $this->date !== $properties['date']
            || $this->title !== $properties['title'];
    }

    /**
     * @return void
     */
    protected function setProperties(WP_Post $post)
    {
        $defaults = [
            'author' => __('Anonymous', 'site-reviews'),
            'date' => '',
            'review_id' => '',
            'review_type' => 'local',
        ];
        $meta = array_filter(
            array_map('array_shift', array_filter((array) get_post_meta($post->ID))),
            'strlen'
        );
        $meta = array_merge($defaults, Arr::unprefixKeys($meta));
        $properties = glsr(CreateReviewDefaults::class)->restrict(array_merge($defaults, $meta));
        $this->modified = $this->isModified($properties);
        array_walk($properties, function ($value, $key) {
            if (property_exists($this, $key) && !isset($this->$key)) {
                $this->$key = maybe_unserialize($value);
            }
        });
    }
}
