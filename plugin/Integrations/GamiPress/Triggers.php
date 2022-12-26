<?php

namespace GeminiLabs\SiteReviews\Integrations\GamiPress;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Review;

class Triggers
{
    /**
     * @param string|array $keys
     * @return array
     */
    public function by($keys)
    {
        $keys = Arr::convertFromString($keys);
        $keys = array_map(function ($key) {
            return Str::removePrefix($key, '/');
        }, $keys);
        return array_values(array_filter(array_keys($this->triggers()), function ($trigger) use ($keys) {
            return Str::contains($trigger, $keys);
        }));
    }

    /**
     * @return array
     */
    public function byPostId()
    {
        return $this->by('post_id');
    }

    /**
     * @return array
     */
    public function byPostType()
    {
        return $this->by('post_type');
    }

    /**
     * @return array
     */
    public function byUserId()
    {
        return $this->by('user_id');
    }

    /**
     * @return array
     */
    public function byUserRole()
    {
        return $this->by('user_role');
    }

    /**
     * @param string $trigger
     * @param string $fallback
     * @return string
     */
    public function label($trigger, Arguments $requirements, $fallback = '')
    {
        $label = Arr::getAs('string', $this->triggers(), $trigger.'.label_'.$requirements->rating_condition);
        if (empty($label)) {
            return $fallback;
        }
        if (Str::contains($label, '%d') && !Str::contains($label, '%s')) {
            return sprintf($label, $requirements->rating);
        }
        foreach (['post_id', 'post_type', 'user_id', 'user_role'] as $key) {
            $method = Helper::buildMethodName($key, 'labelFor');
            if (Str::contains($trigger, '/'.$key) && method_exists($this, $method)) {
                return call_user_func([$this, $method], $label, $requirements, $fallback);
            }
        }
        return $label;
    }

    /**
     * @param string $label
     * @param string $fallback
     * @return string
     */
    public function labelForPostId($label, Arguments $requirements, $fallback = '')
    {
        if ($title = get_the_title($requirements->post_id)) {
            return sprintf($label, $title, $requirements->rating);
        }
        return $fallback;
    }

    /**
     * @param string $label
     * @param string $fallback
     * @return string
     */
    public function labelForPostType($label, Arguments $requirements, $fallback = '')
    {
        if ($type = Arr::get(get_post_type_object($requirements->post_type), 'labels.singular_name')) {
            return sprintf($label, $type, $requirements->rating);
        }
        return $fallback;
    }

    /**
     * @param string $label
     * @param string $fallback
     * @return string
     */
    public function labelForUserId($label, Arguments $requirements, $fallback = '')
    {
        if ($user = get_user_by('id', $requirements->user_id)) {
            return sprintf($label, $user->display_name, $requirements->rating);
        }
        return $fallback;
    }

    /**
     * @param string $label
     * @param string $fallback
     * @return string
     */
    public function labelForUserRole($label, Arguments $requirements, $fallback = '')
    {
        if ($role = Arr::get(wp_roles()->get_names(), $requirements->user_role)) {
            return sprintf($label, $role, $requirements->rating);
        }
        return $fallback;
    }

    /**
     * @return array
     */
    public function labels()
    {
        return array_map(function ($strings) { return $strings['label']; }, $this->triggers());
    }

    /**
     * @return array
     */
    public function triggers()
    {
        return [ // order is intentional
            // Assigned User: Received review assigned to the user
            'site_reviews_gamipress/received/user' => [
                'label' => __('Get review', 'site-reviews'),
                'label_any' => _x('getting a review', '1 point for ... 1 time', 'site-reviews'),
                'label_exact' => _x('getting a review with a %d-star rating', 'admin-text', 'site-reviews'),
                'label_minimum' => _x('getting a review with a minimum %d-star rating', 'admin-text', 'site-reviews'),
            ],
            // Assigned Post: Received review assigned to a post the user authored
            'site_reviews_gamipress/received/post' => [
                'label' => __('Get review on a post', 'site-reviews'),
                'label_any' => _x('getting a review on a post', '1 point for ... 1 time', 'site-reviews'),
                'label_exact' => _x('getting a review on a post with a %d-star rating', 'admin-text', 'site-reviews'),
                'label_minimum' => _x('getting a review on a post with a minimum %d-star rating', 'admin-text', 'site-reviews'),
            ],
            // Assigned Post: Received review assigned to a post of a specific post type that the user authored
            'site_reviews_gamipress/received/post_type' => [
                'label' => __('Get review on a post of a type', 'site-reviews'),
                'label_any' => _x('getting a review on a %s', '1 point for ... 1 time', 'site-reviews'),
                'label_exact' => _x('getting a review on a %s with a %d-star rating', 'admin-text', 'site-reviews'),
                'label_minimum' => _x('getting a review on a %s with a minimum %d-star rating', 'admin-text', 'site-reviews'),
            ],
            // Assigned Post: Received review assigned to a specific post ID the user authored
            'site_reviews_gamipress/received/post_id' => [
                'label' => __('Get review on a specific post', 'site-reviews'),
                'label_any' => _x('getting a review on "%s"', '1 point for ... 1 time', 'site-reviews'),
                'label_exact' => _x('getting a review on "%s" with a %d-star rating', 'admin-text', 'site-reviews'),
                'label_minimum' => _x('getting a review on "%s" with a minimum %d-star rating', 'admin-text', 'site-reviews'),
            ],
            // Logged In User: Submitted review
            'site_reviews_gamipress/reviewed/any' => [
                'label' => __('Write review', 'site-reviews'),
                'label_any' => _x('writing a review', '1 point for ... 1 time', 'site-reviews'),
                'label_exact' => _x('writing a review with a %d-star rating', '1 point for ... 1 time', 'site-reviews'),
                'label_minimum' => _x('writing a review with a minimum %d-star rating', '1 point for ... 1 time', 'site-reviews'),
            ],
            // Logged In User (assigned_posts): Submitted review assigned to a post
            'site_reviews_gamipress/reviewed/post' => [
                'label' => __('Write review of a post', 'site-reviews'),
                'label_any' => _x('writing a review of a post', '1 point for ... 1 time', 'site-reviews'),
                'label_exact' => _x('writing a review of a post with a %d-star rating', '1 point for ... 1 time', 'site-reviews'),
                'label_minimum' => _x('writing a review of a post with a minimum %d-star rating', '1 point for ... 1 time', 'site-reviews'),
            ],
            // Logged In User (assigned_posts): Submitted review assigned to a post of a specific post type
            'site_reviews_gamipress/reviewed/post_type' => [
                'label' => __('Write review of a post of a type', 'site-reviews'),
                'label_any' => _x('writing a review of a %s', '1 point for ... 1 time', 'site-reviews'),
                'label_exact' => _x('writing a review of a %s with a %d-star rating', '1 point for ... 1 time', 'site-reviews'),
                'label_minimum' => _x('writing a review of a %s with a minimum %d-star rating', '1 point for ... 1 time', 'site-reviews'),
            ],
            // Logged In User (assigned_posts): Submitted review assigned to a specific post ID
            'site_reviews_gamipress/reviewed/post_id' => [
                'label' => __('Write review of a specific post', 'site-reviews'),
                'label_any' => _x('writing a review of "%s"', '1 point for ... 1 time', 'site-reviews'),
                'label_exact' => _x('writing a review of "%s" with a %d-star rating', '1 point for ... 1 time', 'site-reviews'),
                'label_minimum' => _x('writing a review of "%s" with a minimum %d-star rating', '1 point for ... 1 time', 'site-reviews'),
            ],
            // Logged In User (assigned_terms): Submitted review assigned to a term
            // 'site_reviews_gamipress/reviewed/term' => [
            //     'label' => __('Write review of a category', 'site-reviews'),
            //     'label_any' => _x('writing a review of a category', '1 point for ... 1 time', 'site-reviews'),
            //     'label_exact' => _x('writing a review of a category with a %d-star rating', '1 point for ... 1 time', 'site-reviews'),
            //     'label_minimum' => _x('writing a review of a category with a minimum %d-star rating', '1 point for ... 1 time', 'site-reviews'),
            // ],
            // Logged In User (assigned_terms): Submitted review assigned to a specific term ID
            // 'site_reviews_gamipress/reviewed/term_id' => [
            //     'label' => __('Write review of a specific category', 'site-reviews'),
            //     'label_any' => _x('writing a review of the category "%s"', '1 point for ... 1 time', 'site-reviews'),
            //     'label_exact' => _x('writing a review of the category "%s" with a %d-star rating', '1 point for ... 1 time', 'site-reviews'),
            //     'label_minimum' => _x('writing a review of the category "%s" with a minimum %d-star rating', '1 point for ... 1 time', 'site-reviews'),
            // ],
            // Logged In User (assigned_users): Submitted review assigned to a user
            'site_reviews_gamipress/reviewed/user' => [
                'label' => __('Write review of a user', 'site-reviews'),
                'label_any' => _x('writing a review of a user', '1 point for ... 1 time', 'site-reviews'),
                'label_exact' => _x('writing a review of a user with a %d-star rating', '1 point for ... 1 time', 'site-reviews'),
                'label_minimum' => _x('writing a review of a user with a minimum %d-star rating', '1 point for ... 1 time', 'site-reviews'),
            ],
            // Logged In User (assigned_users): Submitted review assigned to a user of a specific role
            'site_reviews_gamipress/reviewed/user_role' => [
                'label' => __('Write review of a user of a role', 'site-reviews'),
                'label_any' => _x('writing a review of a user with the %s role', '1 point for ... 1 time', 'site-reviews'),
                'label_exact' => _x('writing a review of a user with the %s role with a %d-star rating', '1 point for ... 1 time', 'site-reviews'),
                'label_minimum' => _x('writing a review of a user with the %s role with a minimum %d-star rating', '1 point for ... 1 time', 'site-reviews'),
            ],
            // Logged In User (assigned_users): Submitted review assigned to a specific user ID
            'site_reviews_gamipress/reviewed/user_id' => [
                'label' => __('Write review of a specific user', 'site-reviews'),
                'label_any' => _x('writing a review of %s', '1 point for ... 1 time', 'site-reviews'),
                'label_exact' => _x('writing a review of %s with a %d-star rating', '1 point for ... 1 time', 'site-reviews'),
                'label_minimum' => _x('writing a review of %s with a minimum %d-star rating', '1 point for ... 1 time', 'site-reviews'),
            ],
        ];
    }
}
