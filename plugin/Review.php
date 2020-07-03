<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\RatingManager;
use GeminiLabs\SiteReviews\Defaults\CreateReviewDefaults;
use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Html\Partials\SiteReviews as SiteReviewsPartial;
use GeminiLabs\SiteReviews\Modules\Html\ReviewHtml;

/**
 * @property array $assigned_post_ids
 * @property array $assigned_term_ids
 * @property array $assigned_user_ids
 * @property string $author;
 * @property string $avatar;
 * @property string $content
 * @property Arguments $custom
 * @property string $date
 * @property string $email
 * @property int $ID
 * @property string $ip_address
 * @property bool $modified
 * @property bool $pinned
 * @property int $rating
 * @property int $rating_id
 * @property string $response
 * @property string $status
 * @property string $title
 * @property string $type
 * @property string $url
 * @property int $user_id
 */
class Review extends Arguments
{
    /**
     * @var Arguments
     */
    protected $_meta;

    /**
     * @var \WP_Post
     */
    protected $_post;

    /**
     * @var Rating
     */
    protected $_rating;

    /**
     * @var bool
     */
    protected $hasCheckedModified;

    /**
     * @param \WP_Post|int $post
     */
    public function __construct($post)
    {
        $post = get_post($post);
        if (glsr()->post_type === Arr::get($post, 'post_type')) {
            $this->_post = $post;
        }
        $args = [];
        $rating = $this->rating();
        $args['assigned_post_ids'] = [];
        $args['assigned_term_ids'] = [];
        $args['assigned_user_ids'] = [];
        $args['author'] = $rating->name;
        $args['avatar'] = $rating->avatar;
        $args['content'] = Arr::get($post, 'post_content');
        $args['custom'] = new Arguments($this->meta()->custom);
        $args['date'] = Arr::get($post, 'post_date');
        $args['email'] = $rating->email;
        $args['ID'] = Helper::castToInt(Arr::get($post, 'ID'));
        $args['ip_address'] = $rating->ip_address;
        $args['modified'] = false;
        $args['pinned'] = $rating->is_pinned;
        $args['rating'] = $rating->rating;
        $args['rating_id'] = $rating->ID;
        $args['response'] = $this->meta()->response;
        $args['status'] = Arr::get($post, 'post_status');
        $args['title'] = Arr::get($post, 'post_title');
        $args['type'] = $rating->type;
        $args['url'] = $rating->url;
        $args['user_id'] = Helper::castToInt(Arr::get($post, 'post_author'));
        parent::__construct($args);
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
        if (empty($this->get('ID'))) {
            return new ReviewHtml($this);
        }
        $partial = glsr(SiteReviewsPartial::class);
        $partial->args = glsr(SiteReviewsDefaults::class)->merge($args);
        $partial->options = Arr::flatten(glsr(OptionManager::class)->all());
        return $partial->buildReview($this);
    }


    /**
     * @param \WP_Post|int $post
     * @return bool
     */
    public static function isReview($post)
    {
        return glsr()->post_type === get_post_type($post);
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return !empty($this->id) && !empty($this->get('rating_id'));
    }
    /**
     * @return Arguments
     */
    public function meta()
    {
        if (!$this->_meta instanceof Arguments) {
            $meta = Arr::consolidate(get_post_meta(Arr::get($this->post(), 'ID')));
            $meta = array_map('array_shift', array_filter($meta));
            $meta = Arr::unprefixKeys(array_filter($meta, 'strlen'));
            $meta = array_map('maybe_unserialize', $meta);
            $meta = glsr(CreateReviewDefaults::class)->restrict($meta);
            $this->_meta = new Arguments($meta);
        }
        return $this->_meta;
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return parent::offsetExists($key) || !is_null($this->custom->$key);
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        if ('modified' === $key) {
            return $this->isModified();
        }
        if (!is_null($value = $this->ratingPivot($key))) {
            return $value;
        }
        if (is_null($value = parent::offsetGet($key))) {
            return $this->custom->$key;
        }
        return $value;
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
     * @param mixed $key
     * @return void
     */
    public function offsetUnset($key)
    {
        // This class is read-only
    }

    /**
     * @return \WP_Post|null
     */
    public function post()
    {
        return $this->_post;
    }

    /**
     * @return Rating
     */
    public function rating()
    {
        if (!$this->_rating instanceof Rating) {
            $this->_rating = glsr(RatingManager::class)->get($this->post());
        }
        return $this->_rating;
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
    protected function isModified()
    {
        if (!$this->hasCheckedModified) {
            $modified = glsr(Query::class)->hasRevisions($this->ID);
            $this->set('modified', $modified);
            $this->hasCheckedModified = true;
        }
        return $this->get('modified');
    }

    /**
     * @param string $key
     * @return array|null
     */
    protected function ratingPivot($key)
    {
        $key = Str::removePrefix('assigned_', $key);
        if (in_array($key, ['post_ids', 'term_ids', 'user_ids'])) {
            $pivot = $this->rating()->$key;
            $this->set(Str::prefix('assigned_', $key), $pivot);
            return $pivot;
        }
    }
}
