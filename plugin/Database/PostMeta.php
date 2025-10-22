<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

class PostMeta
{
    public function delete(int $postId, string $key): bool
    {
        $metaKey = Str::prefix($key, '_');
        return delete_metadata('post', $postId, $metaKey);
    }

    public function exists(int $postId, string $key): bool
    {
        $metaKey = Str::prefix($key, '_');
        return metadata_exists('post', $postId, $metaKey);
    }

    /**
     * @return mixed
     */
    public function get(int $postId, string $key, string $cast = '')
    {
        $metaKey = Str::prefix($key, '_');
        $metaValue = get_metadata('post', $postId, $metaKey, true);
        return Cast::to($cast, $metaValue);
    }

    /**
     * @param mixed $value
     */
    public function set(int $postId, string $key, $value): bool
    {
        $metaKey = Str::prefix($key, '_');
        return false !== update_metadata('post', $postId, $metaKey, $value);
    }
}
