<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Defaults\ValidateReviewDefaults;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Akismet;
use GeminiLabs\SiteReviews\Modules\Blacklist;
use GeminiLabs\SiteReviews\Modules\ReviewLimits;
use GeminiLabs\SiteReviews\Modules\Validator;

class ValidateReview
{
    const RECAPTCHA_ENDPOINT = 'https://www.google.com/recaptcha/api/siteverify';

    const RECAPTCHA_DISABLED = 0;
    const RECAPTCHA_EMPTY = 1;
    const RECAPTCHA_FAILED = 2;
    const RECAPTCHA_INVALID = 3;
    const RECAPTCHA_VALID = 4;

    const VALIDATION_RULES = [
        'content' => 'required',
        'email' => 'required|email',
        'name' => 'required',
        'rating' => 'required|number|between:1,5',
        'terms' => 'accepted',
        'title' => 'required',
    ];

    /**
     * @var string|void
     */
    public $error;

    /**
     * @var string
     */
    public $form_id;

    /**
     * @var bool
     */
    public $recaptchaIsUnset = false;

    /**
     * @var array
     */
    public $request;

    /**
     * @var array
     */
    protected $options;

    /**
     * @return static
     */
    public function validate(array $request)
    {
        $request['ip_address'] = Helper::getIpAddress(); // required for Akismet and Blacklist validation
        $this->form_id = $request['form_id'];
        $this->options = glsr(OptionManager::class)->all();
        $this->request = $this->validateRequest($request);
        $this->validateCustom();
        $this->validatePermission();
        $this->validateHoneyPot();
        $this->validateReviewLimits();
        $this->validateBlacklist();
        $this->validateAkismet();
        $this->validateRecaptcha();
        if (!empty($this->error)) {
            $this->setSessionValues('message', $this->error);
        }
        return $this;
    }

    /**
     * @param string $path
     * @param mixed $fallback
     * @return mixed
     */
    protected function getOption($path, $fallback = '')
    {
        return Arr::get($this->options, $path, $fallback);
    }

    /**
     * @return int
     */
    protected function getRecaptchaStatus()
    {
        if (!glsr(OptionManager::class)->isRecaptchaEnabled()) {
            return static::RECAPTCHA_DISABLED;
        }
        if (empty($this->request['_recaptcha-token'])) {
            return $this->request['_counter'] < intval(apply_filters('site-reviews/recaptcha/timeout', 5))
                ? static::RECAPTCHA_EMPTY
                : static::RECAPTCHA_FAILED;
        }
        return $this->getRecaptchaTokenStatus();
    }

    /**
     * @return int
     */
    protected function getRecaptchaTokenStatus()
    {
        $endpoint = add_query_arg([
            'remoteip' => Helper::getIpAddress(),
            'response' => $this->request['_recaptcha-token'],
            'secret' => $this->getOption('settings.submissions.recaptcha.secret'),
        ], static::RECAPTCHA_ENDPOINT);
        if (is_wp_error($response = wp_remote_get($endpoint))) {
            glsr_log()->error($response->get_error_message());
            return static::RECAPTCHA_FAILED;
        }
        $response = json_decode(wp_remote_retrieve_body($response));
        if (!empty($response->success)) {
            return boolval($response->success)
                ? static::RECAPTCHA_VALID
                : static::RECAPTCHA_INVALID;
        }
        foreach ($response->{'error-codes'} as $error) {
            glsr_log()->error('reCAPTCHA error: '.$error);
        }
        return static::RECAPTCHA_INVALID;
    }

    /**
     * @return array
     */
    protected function getValidationRules(array $request)
    {
        $rules = array_intersect_key(
            apply_filters('site-reviews/validation/rules', static::VALIDATION_RULES, $request),
            array_flip($this->getOption('settings.submissions.required', []))
        );
        $excluded = explode(',', Arr::get($request, 'excluded'));
        return array_diff_key($rules, array_flip($excluded));
    }

    /**
     * @return bool
     */
    protected function isRequestValid(array $request)
    {
        $rules = $this->getValidationRules($request);
        $errors = glsr(Validator::class)->validate($request, $rules);
        if (empty($errors)) {
            return true;
        }
        $this->error = __('Please fix the submission errors.', 'site-reviews');
        $this->setSessionValues('errors', $errors);
        $this->setSessionValues('values', $request);
        return false;
    }

    protected function setError($message, $loggedMessage = '')
    {
        $this->setSessionValues('errors', [], $loggedMessage);
        $this->error = $message;
    }

    /**
     * @param string $type
     * @param mixed $value
     * @param string $loggedMessage
     * @return void
     */
    protected function setSessionValues($type, $value, $loggedMessage = '')
    {
        glsr()->sessionSet($this->form_id.$type, $value);
        if (!empty($loggedMessage)) {
            glsr_log()->warning($loggedMessage)->debug($this->request);
        }
    }

    /**
     * @return void
     */
    protected function validateAkismet()
    {
        if (!empty($this->error)) {
            return;
        }
        if (glsr(Akismet::class)->isSpam($this->request)) {
            $this->setError(__('This review has been flagged as possible spam and cannot be submitted.', 'site-reviews'),
                'Akismet caught a spam submission (consider adding the IP address to the blacklist):'
            );
        }
    }

    /**
     * @return void
     */
    protected function validateBlacklist()
    {
        if (!empty($this->error)) {
            return;
        }
        if (!glsr(Blacklist::class)->isBlacklisted($this->request)) {
            return;
        }
        $blacklistAction = $this->getOption('settings.submissions.blacklist.action');
        if ('reject' != $blacklistAction) {
            $this->request['blacklisted'] = true;
            return;
        }
        $this->setError(__('Your review cannot be submitted at this time.', 'site-reviews'),
            'Blacklisted submission detected:'
        );
    }

    /**
     * @return void
     */
    protected function validateCustom()
    {
        if (!empty($this->error)) {
            return;
        }
        $validated = apply_filters('site-reviews/validate/custom', true, $this->request);
        if (true === $validated) {
            return;
        }
        $errorMessage = is_string($validated)
            ? $validated
            : __('The review submission failed. Please notify the site administrator.', 'site-reviews');
        $this->setError($errorMessage);
        $this->setSessionValues('values', $this->request);
    }

    /**
     * @return void
     */
    protected function validateHoneyPot()
    {
        if (!empty($this->error)) {
            return;
        }
        if (!empty($this->request['gotcha'])) {
            $this->setError(__('The review submission failed. Please notify the site administrator.', 'site-reviews'),
                'The Honeypot caught a bad submission:'
            );
        }
    }

    /**
     * @return void
     */
    protected function validatePermission()
    {
        if (!empty($this->error)) {
            return;
        }
        if (!is_user_logged_in() && glsr(OptionManager::class)->getBool('settings.general.require.login')) {
            $this->setError(__('You must be logged in to submit a review.', 'site-reviews'));
        }
    }

    /**
     * @return void
     */
    protected function validateReviewLimits()
    {
        if (!empty($this->error)) {
            return;
        }
        if (glsr(ReviewLimits::class)->hasReachedLimit($this->request)) {
            $this->setError(__('You have already submitted a review.', 'site-reviews'));
        }
    }

    /**
     * @return void
     */
    protected function validateRecaptcha()
    {
        if (!empty($this->error)) {
            return;
        }
        $status = $this->getRecaptchaStatus();
        if (in_array($status, [static::RECAPTCHA_DISABLED, static::RECAPTCHA_VALID])) {
            return;
        }
        if (static::RECAPTCHA_EMPTY === $status) {
            $this->setSessionValues('recaptcha', 'unset');
            $this->recaptchaIsUnset = true;
            return;
        }
        $this->setSessionValues('recaptcha', 'reset');
        $errors = [
            static::RECAPTCHA_FAILED => __('The reCAPTCHA failed to load, please refresh the page and try again.', 'site-reviews'),
            static::RECAPTCHA_INVALID => __('The reCAPTCHA verification failed, please try again.', 'site-reviews'),
        ];
        $this->setError($errors[$status]);
    }

    /**
     * @return array
     */
    protected function validateRequest(array $request)
    {
        return $this->isRequestValid($request)
            ? array_merge(glsr(ValidateReviewDefaults::class)->defaults(), $request)
            : $request;
    }
}
