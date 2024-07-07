<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Database\Query;
use GeminiLabs\SiteReviews\Database\ReviewManager;
use GeminiLabs\SiteReviews\Defaults\CustomFieldsDefaults;
use GeminiLabs\SiteReviews\Defaults\ReviewDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Text;
use GeminiLabs\SiteReviews\Modules\Avatar;
use GeminiLabs\SiteReviews\Modules\Date;
use GeminiLabs\SiteReviews\Modules\Encryption;
use GeminiLabs\SiteReviews\Modules\Html\ReviewHtml;
use GeminiLabs\SiteReviews\Modules\Multilingual;

/**
 * @property bool $approved  This property is mapped to $is_approved
 * @property array $assigned_posts
 * @property array $assigned_terms
 * @property array $assigned_users
 * @property string $author
 * @property int $author_id
 * @property string $avatar
 * @property string $content
 * @property Arguments $custom
 * @property string $date
 * @property string $date_gmt
 * @property string $name  This property is mapped to $author
 * @property string $email
 * @property bool $has_revisions  This property is mapped to $is_modified
 * @property int $ID
 * @property string $ip_address
 * @property bool $is_approved
 * @property bool $is_modified
 * @property bool $is_pinned
 * @property bool $is_verified
 * @property bool $modified  This property is mapped to $is_modified
 * @property bool $pinned  This property is mapped to $is_pinned
 * @property int $rating
 * @property int $rating_id
 * @property string $response
 * @property int $score
 * @property string $status
 * @property bool $terms
 * @property string $title
 * @property string $type
 * @property string $url
 * @property int $user_id  This property is mapped to $author_id
 */
class Review extends Arguments
{
    /** @var Arguments|null */
    protected $_meta;

    /** @var \WP_Post|null */
    protected $_post;

    protected bool $has_checked_revisions = false;

    protected int $id;

    /**
     * @param array|object $values
     */
    public function __construct($values, bool $init = true)
    {
        $values = glsr()->args($values);
        $this->id = Cast::toInt($values->review_id);
        $args = glsr(ReviewDefaults::class)->restrict($values->toArray());
        $args['ID'] = $this->id;
        parent::__construct($args);
        if ($init) {
            $this->set('avatar', glsr(Avatar::class)->url($this));
            $this->set('custom', $this->custom());
            $this->set('response', $this->meta()->_response);
        }
    }

    /**
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        array_unshift($args, $this);
        $result = apply_filters_ref_array(glsr()->id."/review/call/{$method}", $args);
        if (!is_a($result, get_class($this))) {
            return $result;
        }
    }

    public function __toString(): string
    {
        return (string) $this->build();
    }

    public function approveUrl(): string
    {
        $token = glsr(Encryption::class)->encryptRequest('approve', [$this->id]);
        return !empty($token)
            ? add_query_arg(glsr()->prefix, $token, admin_url())
            : '';
    }

    public function assignedPosts(bool $multilingual = true): array
    {
        $postIds = $this->assigned_posts;
        if ($multilingual) {
            $postIds = glsr(Multilingual::class)->getPostIds($postIds);
        }
        if (empty($postIds)) {
            return [];
        }
        return get_posts([
            'post__in' => $postIds,
            'post_type' => 'any',
            'posts_per_page' => -1,
        ]);
    }

    public function assignedTerms(bool $multilingual = true): array
    {
        $termIds = $this->assigned_terms;
        if ($multilingual) {
            $termIds = glsr(Multilingual::class)->getTermIds($termIds);
        }
        if (empty($termIds)) {
            return [];
        }
        $terms = get_terms([
            'hide_empty' => !$multilingual,
            'include' => $termIds,
            'taxonomy' => glsr()->taxonomy,
        ]);
        if (!is_array($terms)) {
            return [];
        }
        return $terms;
    }

    public function assignedUsers(): array
    {
        if (empty($this->assigned_users)) {
            return [];
        }
        return get_users([
            'fields' => ['display_name', 'ID', 'user_email', 'user_nicename', 'user_url'],
            'include' => $this->assigned_users,
        ]);
    }

    public function author(): string
    {
        $name = $this->get('author', __('Anonymous', 'site-reviews'));
        $format = glsr_get_option('reviews.name.format', '', 'string');
        $initial = glsr_get_option('reviews.name.initial', '', 'string');
        return Text::name($name, $format, $initial);
    }

    public function avatar(int $size = 0): string
    {
        return glsr(Avatar::class)->img($this, $size);
    }

    public function build(array $args = []): ReviewHtml
    {
        return new ReviewHtml($this, $args);
    }

    public function custom(): Arguments
    {
        $custom = array_filter($this->meta()->toArray(),
            fn ($key) => str_starts_with($key, '_custom'),
            ARRAY_FILTER_USE_KEY
        );
        $custom = Arr::unprefixKeys($custom, '_custom_');
        $custom = Arr::unprefixKeys($custom, '_');
        $custom = glsr(CustomFieldsDefaults::class)->merge($custom);
        return glsr()->args($custom);
    }

    public function date(string $format = 'F j, Y'): string
    {
        $value = $this->get('date');
        if (!empty(func_get_args())) {
            return Cast::toString(mysql2date($format, $value));
        }
        $dateFormat = glsr_get_option('reviews.date.format', 'default');
        if ('relative' === $dateFormat) {
            return glsr(Date::class)->relative($value, 'past');
        }
        $format = 'custom' === $dateFormat
            ? glsr_get_option('reviews.date.custom', 'M j, Y')
            : glsr(OptionManager::class)->wp('date_format', 'F j, Y');
        return Cast::toString(mysql2date($format, $value));
    }

    public function editUrl(): string
    {
        $obj = get_post_type_object(glsr()->post_type);
        $link = admin_url(sprintf($obj->_edit_link.'&action=edit', $this->id));
        $link = apply_filters('get_edit_post_link', $link, $this->id, 'display');
        return Cast::toString($link);
    }

    /** @param int|\WP_Post $post */
    public static function isEditable($post): bool
    {
        $postId = Helper::getPostId($post);
        return static::isReview($postId)
            && in_array(glsr_get_review($postId)->type, ['', 'local']);
    }

    /** @param \WP_Post|int|false $post */
    public static function isReview($post): bool
    {
        return glsr()->post_type === get_post_type($post);
    }

    public function isValid(): bool
    {
        return !empty($this->id) && !empty($this->get('rating_id'));
    }

    public function meta(): Arguments
    {
        if (!$this->_meta instanceof Arguments) {
            $meta = Arr::consolidate(get_post_meta($this->id));
            $meta = array_map(fn ($item) => array_shift($item), array_filter($meta));
            $meta = array_filter($meta, '\GeminiLabs\SiteReviews\Helper::isNotEmpty');
            $meta = array_map('maybe_unserialize', $meta);
            $this->_meta = glsr()->args($meta);
        }
        return $this->_meta;
    }

    /**
     * @param mixed $key
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($key): bool
    {
        return parent::offsetExists($key) || !is_null($this->custom()->$key);
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($key)
    {
        $alternateKeys = [
            'approved' => 'is_approved',
            'has_revisions' => 'is_modified',
            'modified' => 'is_modified',
            'name' => 'author',
            'pinned' => 'is_pinned',
            'user_id' => 'author_id',
        ];
        if (array_key_exists($key, $alternateKeys)) {
            return $this->offsetGet($alternateKeys[$key]);
        }
        if ('is_modified' === $key) {
            return $this->hasRevisions();
        }
        if (is_null($value = parent::offsetGet($key))) {
            return $this->custom()->$key;
        }
        return $value;
    }

    /**
     * @param mixed $key
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($key, $value): void
    {
        // This class is read-only, except for custom fields
        if ('custom' === $key) {
            $value = Arr::consolidate($value);
            $value = Arr::prefixKeys($value, '_custom_');
            $meta = wp_parse_args($value, $this->meta()->toArray());
            $this->_meta = glsr()->args($meta);
            parent::offsetSet($key, $this->custom());
        }
    }

    /**
     * @param mixed $key
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($key): void
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

    public function rating(): string
    {
        return glsr_star_rating($this->get('rating'));
    }

    public function refresh(): Review
    {
        $values = glsr(ReviewManager::class)->get($this->id, true)->toArray();
        $this->merge($values);
        $this->_meta = null;
        $this->_post = null;
        $this->has_checked_revisions = false;
        $this->set('avatar', glsr(Avatar::class)->url($this));
        $this->set('custom', $this->custom());
        $this->set('response', $this->meta()->_response);
        return $this;
    }

    public function render(array $args = []): void
    {
        echo $this->build($args);
    }

    public function toArray(array $excludedKeys = []): array
    {
        $excludedKeys = Arr::consolidate($excludedKeys);
        $values = Cast::toArrayDeep($this->getArrayCopy());
        $values['name'] = $this->get('author'); // fallback
        return array_diff_key($values, array_flip($excludedKeys));
    }

    public function type(): string
    {
        $type = $this->get('type');
        $reviewTypes = glsr()->retrieveAs('array', 'review_types');
        return Arr::get($reviewTypes, $type, _x('Unknown', 'admin-text', 'site-reviews'));
    }

    /**
     * @return \WP_User|false
     */
    public function user()
    {
        return get_user_by('id', $this->get('author_id'));
    }

    public function verifyUrl(string $path = ''): string
    {
        if ($this->get('is_verified') && !empty($this->meta()->_verified_on)) {
            return '';
        }
        $path = trailingslashit($path);
        $token = glsr(Encryption::class)->encryptRequest('verify', [$this->id, $path]);
        return !empty($token)
            ? add_query_arg(glsr()->prefix, $token, get_home_url())
            : '';
    }

    protected function hasRevisions(): bool
    {
        if (!$this->has_checked_revisions) {
            $modified = glsr(Query::class)->hasRevisions($this->ID);
            $this->set('is_modified', $modified);
            $this->has_checked_revisions = true;
        }
        return $this->get('is_modified');
    }
}
