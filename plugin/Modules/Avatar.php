<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Database\Cache;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Modules\Avatars\InitialsAvatar;
use GeminiLabs\SiteReviews\Modules\Avatars\PixelAvatar;
use GeminiLabs\SiteReviews\Modules\Html\Builder;
use GeminiLabs\SiteReviews\Review;

class Avatar
{
    public const FALLBACK_SIZE = 40;
    public const GRAVATAR_URL = 'https://secure.gravatar.com/avatar';
    public const MAX_SIZE = 240;
    public const MIN_SIZE = 16;

    /**
     * @var int
     */
    public $size;

    /**
     * @var string
     */
    public $type;

    public function __construct()
    {
        $this->size = glsr_get_option('reviews.avatars_size', static::FALLBACK_SIZE, 'int');
        $this->type = glsr_get_option('reviews.avatars_fallback', 'mystery', 'string');
    }

    public function fallbackUrl(Review $review, int $size = 0): string
    {
        $url = '';
        if (in_array($this->type, ['custom', 'initials', 'none', 'pixels'])) {
            $method = Helper::buildMethodName('generate', $this->type);
            if (method_exists($this, $method)) {
                $url = call_user_func([$this, $method], $review);
            }
            $this->type = 'mm'; // fallback to the mystery man avatar if the custom/initials/pixels URL is invalid
        }
        if ($this->isUrl($url)) {
            $this->type = '404'; // fallback to the custom/initials/pixels URL
        } else {
            $args = [
                'd' => $this->type,
                's' => $this->size($size, true),
            ];
            $url = add_query_arg($args, static::GRAVATAR_URL);
        }
        return glsr()->filterString('avatar/fallback', $url, $review, $this->size($size));
    }

    public function generate(Review $review, int $size = 0): string
    {
        $fallbackUrl = $this->fallbackUrl($review, $size);
        $avatarUrl = get_avatar_url($this->userField($review), [
            'default' => $this->type,
            'size' => $this->size($size, true),
        ]);
        $avatarUrl = glsr()->filterString('avatar/generate', $avatarUrl, $review, $this->size($size));
        if (!$this->isUrl($avatarUrl) || !$this->isUrlOnline($avatarUrl)) {
            return $fallbackUrl;
        }
        return $avatarUrl;
    }

    public function generateCustom(): string
    {
        return glsr_get_option('reviews.avatars_fallback_url', '', 'string');
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
        $attributes = [
            'alt' => sprintf(__('Avatar for %s', 'site-reviews'), $review->author()),
            'height' => $this->size($size, true),
            'loading' => 'lazy',
            'src' => $this->url($review, $size),
            'style' => sprintf('width:%1$spx; height:%1$spx;', $this->size($size)),
            'width' => $this->size($size, true),
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
        return $this->generate($review, $size);
    }

    protected function isUrl(string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH);
        $encodedPath = array_map('urlencode', explode('/', $path));
        $encodedPath = implode('/', $encodedPath);
        $url = str_replace($path, $encodedPath, $url);
        return !empty(filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED));
    }

    protected function isUrlOnline(string $url): bool
    {
        $key = md5(strtolower($url));
        $status = glsr(Cache::class)->get($key, 'avatar',
            fn () => Helper::remoteStatusCheck($url),
            HOUR_IN_SECONDS
        );
        return 200 === $status;
    }

    protected function size(int $size = 0, bool $double = false): int
    {
        if ($size < 1) {
            $size = $this->size;
        }
        $size = min(static::MAX_SIZE, max(static::MIN_SIZE, $size));
        if ($double) {
            return $size * 2;
        }
        return $size;
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
