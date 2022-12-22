<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Avatars\InitialsAvatar;
use GeminiLabs\SiteReviews\Modules\Avatars\PixelAvatar;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Review;

class Avatar
{
    public const FALLBACK_SIZE = 40;
    public const GRAVATAR_URL = 'https://secure.gravatar.com/avatar';

    /**
     * @var string
     */
    public $type;

    public function __construct()
    {
        $this->type = glsr_get_option('reviews.avatars_fallback', 'mystery', 'string');
    }

    /**
     * @return string
     */
    public function fallbackDefault(Review $review)
    {
        if ('custom' === $this->type) {
            $customUrl = glsr_get_option('reviews.avatars_fallback_url');
            if ($this->isUrl($customUrl)) {
                return $customUrl;
            }
            $this->type = 'mystery'; // fallback to mystery if a custom url is not set
        }
        if ('pixels' === $this->type) {
            return $this->generatePixels($review);
        }
        if ('initials' === $this->type) {
            return $this->generateInitials($review);
        }
        return $this->type;
    }

    /**
     * @param int $size
     * @return string
     */
    public function fallbackUrl(Review $review, $size = 0)
    {
        $fallbackUrl = $this->fallbackDefault($review);
        if ($fallbackUrl === $this->type) {
            $fallbackUrl = add_query_arg('d', $this->type, static::GRAVATAR_URL);
            $fallbackUrl = add_query_arg('s', $this->size($size), $fallbackUrl);
        }
        return glsr()->filterString('avatar/fallback', $fallbackUrl, $size, $review);
    }

    /**
     * @param int $size
     * @return string
     */
    public function generate(Review $review, $size = 0)
    {
        $default = $this->fallbackDefault($review);
        if ($default !== $this->type) {
            $default = '404';
        }
        $size = $this->size($size);
        $avatarUrl = get_avatar_url($this->userField($review), [
            'default' => $default,
            'size' => $size,
        ]);
        $avatarUrl = glsr()->filterString('avatar/generate', $avatarUrl, $size, $review);
        if (!$this->isUrl($avatarUrl)) {
            return $this->fallbackUrl($review, $size);
        }
        if (200 !== Helper::remoteStatusCheck($avatarUrl)) {
            // @todo generate the images with javascript on canvas to avoid this status check?
            return $this->fallbackUrl($review, $size);
        }
        return $avatarUrl;
    }

    /**
     * @return string
     */
    public function generateInitials(Review $review)
    {
        $name = $review->author;
        if (empty($review->author)) {
            $name = __('Anonymous', 'site-reviews');
        }
        return glsr(InitialsAvatar::class)->create($name);
    }

    /**
     * @return string
     */
    public function generatePixels(Review $review)
    {
        return glsr(PixelAvatar::class)->create($this->userField($review));
    }

    /**
     * @param int $size
     * @return string
     */
    public function img(Review $review, $size = 0)
    {
        $size = $this->size($size);
        $attributes = [
            'alt' => sprintf(__('Avatar for %s', 'site-reviews'), $review->author()),
            'height' => $size, // @2x
            'loading' => 'lazy',
            'src' => $this->url($review, $size), // @2x
            'style' => sprintf('width:%1$spx; height:%1$spx;', $size / 2), // @1x
            'width' => $size, // @2x
        ];
        if (glsr()->isAdmin()) {
            $attributes['data-fallback'] = $this->fallbackUrl($review, $size);
        }
        $attributes = glsr()->filterArray('avatar/attributes', $attributes, $review);
        return glsr(Builder::class)->img($attributes);
    }

    /**
     * @param int $size
     * @return string
     */
    public function url(Review $review, $size = 0)
    {
        if ($this->isUrl($review->avatar)) {
            return $review->avatar;
        }
        return $this->fallbackUrl($review, $size);
    }

    /**
     * @param mixed $url
     * @return bool
     */
    protected function isUrl($url)
    {
        return !empty(filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED));
    }

    /**
     * @param int $size
     * @return int
     */
    protected function size($size = 0)
    {
        $size = Cast::toInt($size);
        if ($size < 1) {
            $size = glsr_get_option('reviews.avatars_size', static::FALLBACK_SIZE, 'int');
            $size = Helper::ifEmpty($size, static::FALLBACK_SIZE, $strict = true);
        }
        return $size * 2; // @2x
    }

    /**
     * @return int|string
     */
    protected function userField(Review $review)
    {
        if ($review->author_id) {
            $value = $review->author_id;
        }
        if (empty($value) || !is_numeric($value)) {
            $value = $review->email;
        }
        return glsr()->filterString('avatar/id_or_email', $value, $review->toArray());
    }
}
