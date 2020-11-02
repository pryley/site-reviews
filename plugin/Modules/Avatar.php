<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class Avatar
{
    const FALLBACK = 'https://gravatar.com/avatar/?d=mm&s=128';
    const FALLBACK_SIZE = 40;

    /**
     * @param int $size
     * @return string
     */
    public function fallback($size = null)
    {
        $fallbackUrl = static::FALLBACK;
        if ($size = $this->size($size)) {
            $fallbackUrl = add_query_arg('s', $size, remove_query_arg('s', $fallbackUrl));
        }
        return glsr()->filterString('avatar/fallback', $fallbackUrl, $size);
    }

    /**
     * @param \WP_User|int|string $userField
     * @param int $size
     * @return string
     */
    public function generate($userField, $size = null)
    {
        $size = $this->size($size);
        return $this->url(get_avatar_url($userField, ['size' => $size]), $size);
    }

    /**
     * @param string $src
     * @param int $size
     * @return string
     */
    public function img($src, $size = null)
    {
        $size = $this->size($size);
        $attributes = [
            'height' => $size * 2,
            'loading' => 'lazy',
            'src' => $this->url($src, $size * 2),
            'style' => sprintf('width:%1$spx; height:%1$spx;', $size),
            'width' => $size * 2,
        ];
        if (glsr()->isAdmin()) {
            $attributes['data-fallback'] = $this->fallback($size);
        }
        return glsr(Builder::class)->img($attributes);
    }

    /**
     * @param string|false $avatarUrl
     * @param int $size
     * @return string
     */
    public function url($avatarUrl, $size = null)
    {
        if (filter_var($avatarUrl, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
            return $avatarUrl;
        }
        return $this->fallback($size);
    }

    /**
     * @param int $size
     * @return int
     */
    protected function size($size = null)
    {
        if ($size = Cast::toInt($size)) {
            return $size;
        }
        $size = glsr_get_option('reviews.avatars_size', static::FALLBACK_SIZE, 'int');
        return Helper::ifEmpty($size, static::FALLBACK_SIZE, $strict = true);
    }
}
