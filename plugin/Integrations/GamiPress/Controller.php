<?php

namespace GeminiLabs\SiteReviews\Integrations\GamiPress;

use GeminiLabs\SiteReviews\Controllers\Controller as BaseController;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Review;

class Controller extends BaseController
{
    const TRIGGER_GET = 'site_reviews_gamipress/received/user';
    const TRIGGER_WRITE = 'site_reviews_gamipress/reviewed/any';

    /**
     * @param string $label
     * @param int $requirementId
     * @param array $requirement
     * @return string
     * @filter gamipress_activity_trigger_label
     */
    public function filterActivityTriggerLabel($label, $requirementId, $requirement)
    {
        if (static::TRIGGER_GET === Arr::get($requirement, 'trigger_type')) {
            return _x('getting a review', '1 point for ... 1 time', 'site-reviews');
        }
        if (static::TRIGGER_WRITE === Arr::get($requirement, 'trigger_type')) {
            return _x('writing a review', '1 point for ... 1 time', 'site-reviews');
        }
        return $label;
    }

    /**
     * @param array $triggers
     * @return array
     * @filter gamipress_activity_triggers
     */
    public function filterActivityTriggers($triggers)
    {
        $triggers[glsr()->name] = [
            static::TRIGGER_GET => __('Get review', 'site-reviews'),
            static::TRIGGER_WRITE => __('Write review', 'site-reviews'),
        ];
        return $triggers;
    }

    /**
     * @param bool $result
     * @param int $userId
     * @param int $requirementId
     * @param string $trigger
     * @param int $siteId
     * @param array $args
     * @return bool
     * @filter user_has_access_to_achievement
     */
    public function filterUserHasAccessToAchievement($result, $userId, $requirementId, $trigger)
    {
        if (!function_exists('gamipress_get_requirement_types_slugs')) {
            return $result;
        }
        $isRequirement = in_array(get_post_type($requirementId), gamipress_get_requirement_types_slugs());
        if ($result && $isRequirement && in_array($trigger, [static::TRIGGER_GET, static::TRIGGER_WRITE])) {
            return true;
        }
        return $result;
    }

    /**
     * @return void
     * @action site-reviews/review/created
     */
    public function onReviewCreated(Review $review)
    {
        if (!is_user_logged_in() || !function_exists('gamipress_trigger_event')) {
            return;
        }
        $assignedUsers = wp_list_pluck(glsr_get_review($review->ID)->assignedUsers(), 'ID');
        $assignedUsers = array_values(array_diff($assignedUsers, [get_current_user_id()]));
        foreach ($assignedUsers as $userId) {
            gamipress_trigger_event([
                'event' => static::TRIGGER_GET,
                'user_id' => (int) $userId,
            ]);
        }
        gamipress_trigger_event([
            'event' => static::TRIGGER_WRITE,
            'user_id' => get_current_user_id(),
        ]);
    }
}
