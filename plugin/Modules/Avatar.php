<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Database\Cache;
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
        if ('initials' === $this->type) {
            return $this->generateInitials($review);
        }
        if ('none' === $this->type) {
            $this->type = '';
        }
        if ('pixels' === $this->type) {
            return $this->generatePixels($review);
        }
        return $this->type;
    }

    public function fallbackUrl(Review $review, int $size = 0): string
    {
        $fallbackUrl = $this->fallbackDefault($review);
        if ($fallbackUrl === $this->type) {
            $fallbackUrl = add_query_arg('d', $this->type, static::GRAVATAR_URL);
            $fallbackUrl = add_query_arg('s', $this->size($size), $fallbackUrl);
        }
        return glsr()->filterString('avatar/fallback', $fallbackUrl, $size, $review);
    }

    public function generate(Review $review, int $size = 0): string
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
        if (!$this->isUrl($avatarUrl) || !$this->isUrlOnline($avatarUrl)) {
            return $this->fallbackUrl($review, $size);
        }
        return $avatarUrl;
    }

    public function generateInitials(Review $review): string
    {
        $name = $review->author;
        if (empty($review->author)) {
            $name = __('Anonymous', 'site-reviews');
        }
        return glsr(InitialsAvatar::class)->create($name);
    }

    public function generatePixels(Review $review): string
    {
        return glsr(PixelAvatar::class)->create($this->userField($review));
    }

    public function img(Review $review, int $size = 0): string
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

    public function url(Review $review, int $size = 0): string
    {
        if ($this->isUrl($review->avatar)) {
            return $review->avatar;
        }
        return $this->fallbackUrl($review, $size);
    }

    protected function isUrl(string $url): bool
    {
        return !empty(filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED));
    }

    protected function isUrlOnline(string $url): bool
    {
        $key = md5(strtolower($url));
        $status = glsr(Cache::class)->get($key, 'avatar', function () use ($url) {
            return Helper::remoteStatusCheck($url);
        }, HOUR_IN_SECONDS);
        return 200 === $status;
    }

    protected function size(int $size = 0): int
    {
        $size = Cast::toInt($size);
        if ($size < 1) {
            $size = glsr_get_option('reviews.avatars_size', static::FALLBACK_SIZE, 'int');
            $size = Helper::ifEmpty($size, static::FALLBACK_SIZE, $strict = true);
        }
        return $size * 2; // @2x
    }

    protected function userField(Review $review): string
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
