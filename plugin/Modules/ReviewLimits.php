<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;

class ReviewLimits
{
    protected $request;

    /**
     * @return array
     * @filter site-reviews/get/reviews/query
     */
    public function filterReviewsQuery(array $parameters, array $args)
    {
        if ($authorId = get_current_user_id()) {
            $parameters['author'] = $authorId;
        }
        $parameters['post_status'] = ['pending', 'publish'];
        return apply_filters('site-reviews/review-limits/query', $parameters, $args);
    }

    /**
     * @return bool
     */
    public function hasReachedLimit(array $request = [])
    {
        $this->request = $request;
        $method = Helper::buildMethodName(
            glsr(OptionManager::class)->get('settings.submissions.limit'), 'validateBy'
        );
        return method_exists($this, $method)
            ? !call_user_func([$this, $method])
            : false;
    }

    /**
     * @param string $value
     * @param string $whitelist
     * @return bool
     */
    public function isWhitelisted($value, $whitelist)
    {
        if (empty($whitelist)) {
            return false;
        }
        return in_array($value, array_filter(explode("\n", $whitelist), 'trim'));
    }

    /**
     * @param string $whitelistName
     * @return string
     */
    protected function getWhitelist($whitelistName)
    {
        return glsr(OptionManager::class)->get('settings.submissions.limit_whitelist.'.$whitelistName);
    }

    /**
     * @return bool
     */
    protected function validate($key, $value, $addMetaQuery = true)
    {
        if ($this->isWhitelisted($value, $this->getWhitelist($key))) {
            return true;
        }
        add_filter('site-reviews/get/reviews/query', [$this, 'filterReviewsQuery'], 5, 2);
        $args = ['assigned_to' => Arr::get($this->request, 'assign_to')];
        if ($addMetaQuery) {
            $args[$key] = $value;
        }
        $reviews = glsr_get_reviews($args);
        remove_filter('site-reviews/get/reviews/query', [$this, 'filterReviewsQuery'], 5);
        $result = 0 === count($reviews);
        $result = apply_filters('site-reviews/review-limits/validate', $result, $reviews, $this->request, $key);
        return wp_validate_boolean($result);
    }

    /**
     * @return bool
     */
    protected function validateByEmail()
    {
        glsr_log()->debug('Email is: '.Arr::get($this->request, 'email'));
        return $this->validate('email', Arr::get($this->request, 'email'));
    }

    /**
     * @return bool
     */
    protected function validateByIpAddress()
    {
        glsr_log()->debug('IP Address is: '.Arr::get($this->request, 'ip_address'));
        return $this->validate('ip_address', Arr::get($this->request, 'ip_address'));
    }

    /**
     * @return bool
     */
    protected function validateByUsername()
    {
        $user = wp_get_current_user();
        if (!$user->exists()) {
            return true;
        }
        glsr_log()->debug('Username is: '.$user->user_login);
        return $this->validate('username', $user->user_login, false);
    }
}
