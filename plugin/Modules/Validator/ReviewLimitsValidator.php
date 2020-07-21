<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Helper;

class ReviewLimitsValidator extends ValidatorAbstract
{
    /**
     * @return void
     */
    public function performValidation()
    {
        if (!$this->isValid()) {
            $this->setErrors(__('You have already submitted a review.', 'site-reviews'));
        }
    }

    /**
     * @return bool
     */
    protected function isValid()
    {
        $method = Helper::buildMethodName(glsr_get_option('submissions.limit'), 'validateBy');
        return method_exists($this, $method)
            ? call_user_func([$this, $method])
            : true;
    }

    /**
     * @param string $value
     * @param string $whitelist
     * @return bool
     */
    protected function isWhitelisted($value, $whitelist)
    {
        if (empty($value) || empty($whitelist)) {
            return false;
        }
        return in_array($value, array_filter(explode("\n", $whitelist), 'trim'));
    }

    /**
     * @return bool
     */
    protected function validateByEmail()
    {
        glsr_log()->debug('Email is: '.$this->request->email);
        return $this->validateLimit('email', [
            'email' => $this->request->email,
        ]);
    }

    /**
     * @return bool
     */
    protected function validateByIpAddress()
    {
        glsr_log()->debug('IP Address is: '.$this->request->ip_address);
        return $this->validateLimit('ip_address', [
            'ip_address' => $this->request->ip_address,
        ]);
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
        return $this->validateLimit('username', [
            'author' => $user->ID,
            'post_status' => ['pending', 'publish'],
        ]);
    }

    /**
     * @return bool
     */
    protected function validateLimit($key, array $queryArgs)
    {
        if ($this->isWhitelisted($this->request[$key], glsr_get_option('submissions.limit_whitelist.'.$key))) {
            return true;
        }
        $queryArgs['assigned_posts'] = $this->request->assign_to;
        $reviews = glsr_get_reviews($queryArgs);
        $result = 0 === $reviews->total;
        return glsr()->filterBool('validate/review-limits', $result, $reviews, $this->request, $key);
    }
}
