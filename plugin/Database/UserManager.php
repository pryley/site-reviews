<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

class UserManager
{
    /**
     * @param int|string $postId
     * @return int
     */
    public function normalizeId($userId)
    {
        if ('user_id' == $userId) {
            return get_current_user_id();
        }
        if (!is_numeric($userId)) {
            $userId = username_exists($userId);
        }
        return Cast::toInt($userId);
    }

    /**
     * @param array[]|string $userIds
     * @return array
     */
    public function normalizeIds($userIds)
    {
        return Arr::uniqueInt(array_map([$this, 'normalizeId'], Cast::toArray($userIds)));
    }
}
