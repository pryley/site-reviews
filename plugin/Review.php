<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\CreateReviewDefaults;
use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Partials\SiteReviews as SiteReviewsPartial;
use GeminiLabs\SiteReviews\Modules\Html\ReviewHtml;
use WP_Post;

class Review implements \ArrayAccess
{
    public $assigned_to;
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
    public $response;
    public $review_id;
    public $review_type;
    public $status;
    public $term_ids;
    public $title;
    public $url;
    public $user_id;

    public function __construct(WP_Post $post)
    {
        if (Application::POST_TYPE != $post->post_type) {
            return;
        }
        $this->content = $post->post_content;
        $this->date = $post->post_date;
        $this->ID = intval($post->ID);
        $this->status = $post->post_status;
        $this->title = $post->post_title;
        $this->user_id = intval($post->post_author);
        $this->setProperties($post);
        $this->setTermIds($post);
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
        $partial->options = Arr::flattenArray(glsr(OptionManager::class)->all());
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
        return $this->date != $properties['date']
            || $this->content != $properties['content']
            || $this->title != $properties['title'];
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
        $meta = array_merge($defaults, Arr::unprefixArrayKeys($meta));
        $properties = glsr(CreateReviewDefaults::class)->restrict(array_merge($defaults, $meta));
        $this->modified = $this->isModified($properties);
        array_walk($properties, function ($value, $key) {
            if (!property_exists($this, $key) || isset($this->$key)) {
                return;
            }
            $this->$key = maybe_unserialize($value);
        });
    }

    /**
     * @return void
     */
    protected function setTermIds(WP_Post $post)
    {
        $this->term_ids = [];
        if (!is_array($terms = get_the_terms($post, Application::TAXONOMY))) {
            return;
        }
        foreach ($terms as $term) {
            $this->term_ids[] = $term->term_id;
        }
    }
}
