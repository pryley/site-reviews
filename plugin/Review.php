<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Defaults\CreateReviewDefaults;
use GeminiLabs\SiteReviews\Defaults\SiteReviewsDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Modules\Html\Partials\SiteReviews as SiteReviewsPartial;
use GeminiLabs\SiteReviews\Modules\Html\ReviewHtml;

/**
 * @property array $assigned_post_ids
 * @property array $assigned_term_ids
 * @property array $assigned_user_ids
 * @property string $author
 * @property int $author_id
 * @property string $avatar;
 * @property string $content
 * @property Arguments $custom
 * @property string $date
 * @property string $email
 * @property int $ID
 * @property string $ip_address
 * @property bool $is_approved
 * @property bool $is_modified
 * @property bool $is_pinned
 * @property int $rating
 * @property int $rating_id
 * @property string $response
 * @property string $status
 * @property string $title
 * @property string $type
 * @property string $url
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
     * @var object
     */
    protected $_review;

    /**
     * @var bool
     */
    protected $hasCheckedModified;

    /**
     * @var int
     */
    protected $id;

    /**
     * @param array|object $values
     */
    public function __construct($values)
    {
        $values = glsr()->args($values);
        $this->id = Helper::castToInt($values->review_id);
        $args = [];
        $args['assigned_post_ids'] = Arr::uniqueInt(explode(',', $values->post_ids));
        $args['assigned_term_ids'] = Arr::uniqueInt(explode(',', $values->term_ids));
        $args['assigned_user_ids'] = Arr::uniqueInt(explode(',', $values->user_ids));
        $args['author'] = $values->name;
        $args['author_id'] = Helper::castToInt($values->author_id);
        $args['avatar'] = $values->avatar;
        $args['content'] = $values->content;
        $args['custom'] = new Arguments($this->meta()->custom);
        $args['date'] = $values->date;
        $args['email'] = $values->email;
        $args['ID'] = $this->id;
        $args['ip_address'] = $values->ip_address;
        $args['is_approved'] = Helper::castToBool($values->is_approved);
        $args['is_modified'] = false;
        $args['is_pinned'] = Helper::castToBool($values->is_pinned);
        $args['rating'] = Helper::castToInt($values->rating);
        $args['rating_id'] = Helper::castToInt($values->ID);
        $args['response'] = $this->meta()->response;
        $args['status'] = $values->status;
        $args['title'] = $values->title;
        $args['type'] = $values->type;
        $args['url'] = $values->url;
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
     * @param int $size
     * @return string
     */
    public function avatar($size = null)
    {
        if (!is_numeric($size)) {
            $size = glsr_get_option('reviews.avatars_size', 40, 'int');
        }
        $fallback = 'https://gravatar.com/avatar/?d=mm&s='.($size * 2);
        return glsr(Builder::class)->img([
            'data-fallback' => $fallback,
            'height' => $size,
            'loading' => 'lazy',
            'src' => $this->get('avatar', $fallback),
            'width' => $size,
        ]);
    }

    /**
     * @return ReviewHtml
     */
    public function build(array $args = [])
    {
        if (empty($this->id)) {
            return new ReviewHtml($this);
        }
        $partial = glsr(SiteReviewsPartial::class);
        $partial->args = glsr(SiteReviewsDefaults::class)->merge($args);
        $partial->options = Arr::flatten(glsr(OptionManager::class)->all());
        return $partial->buildReview($this);
    }

    /**
     * @return string
     */
    public function date($format = 'F j, Y')
    {
        return get_date_from_gmt($this->get('date'), $format);
    }

    /**
     * @param int|\WP_Post $postId
     * @return bool
     */
    public static function isEditable($postId)
    {
        $post = get_post($postId);
        return static::isReview($post)
            && post_type_supports(glsr()->post_type, 'title')
            && 'local' === glsr(Query::class)->review($post->ID)->type;
    }

    /**
     * @param \WP_Post|int|false $post
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
            $meta = Arr::consolidate(get_post_meta($this->id));
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
        $alternateKeys = [
            'approved' => 'is_approved',
            'modified' => 'is_modified',
            'name' => 'author',
            'pinned' => 'is_pinned',
            'user_id' => 'author_id',
        ];
        if (array_key_exists($key, $alternateKeys)) {
            return $this->offsetGet($alternateKeys[$key]);
        }
        if ('is_modified' === $key) {
            return $this->isModified();
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
        if (!$this->_post instanceof \WP_Post) {
            $this->_post = get_post($this->id);
        }
        return $this->_post;
    }

    /**
     * @return void
     */
    public function render()
    {
        echo $this->build();
    }

    /**
     * @return string
     */
    public function rating()
    {
        return glsr_star_rating($this->get('rating'));
    }

    /**
     * @return string
     */
    public function type()
    {
        $type = $this->get('type');
        return array_key_exists($type, glsr()->reviewTypes)
            ? glsr()->reviewTypes[$type]
            : _x('Unknown', 'admin-text', 'site-reviews');
    }

    /**
     * @return bool
     */
    protected function isModified()
    {
        if (!$this->hasCheckedModified) {
            $modified = glsr(Query::class)->hasRevisions($this->ID);
            $this->set('is_modified', $modified);
            $this->hasCheckedModified = true;
        }
        return $this->get('is_modified');
    }
}
