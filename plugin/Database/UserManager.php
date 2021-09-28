<?php

namespace GeminiLabs\SiteReviews\Database;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;

class UserManager
{
    /**
     * @param int|string $userId
     * @return int
     */
    public function normalizeId($userId)
    {
        if ('user_id' === $userId) {
            return get_current_user_id();
        }
        if (!empty($userId)) {
            $userKey = Helper::ifTrue(is_numeric($userId), 'ID', 'login');
            $user = get_user_by($userKey, $userId);
            $userId = Arr::get($user, 'ID');
        }
        return Cast::toInt($userId);
    }

    /**
     * @param array|string $userIds
     * @return array
     */
    public function normalizeIds($userIds)
    {
        return Arr::uniqueInt(array_map([$this, 'normalizeId'], Cast::toArray($userIds)));
    }
}
