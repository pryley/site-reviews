<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Captcha;

class CaptchaValidator extends ValidatorAbstract
{
    public const CAPTCHA_DISABLED = 0;
    // public const CAPTCHA_EMPTY = 1;
    public const CAPTCHA_FAILED = 2;
    public const CAPTCHA_INVALID = 3;
    public const CAPTCHA_VALID = 4;

    protected $status;

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isTokenValid(array $response)
    {
        return $response['success'];
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        if (in_array($this->status, [static::CAPTCHA_DISABLED, static::CAPTCHA_VALID])) {
            return true;
        }
        return false;
    }

    /**
     * @return void
     */
    public function performValidation()
    {
        $this->status = $this->verifyStatus();
        if ($this->isValid()) {
            return;
        }
        $error = Helper::ifTrue($this->status === static::CAPTCHA_FAILED,
            __('The CAPTCHA failed to load, please refresh the page and try again.', 'site-reviews'),
            __('The CAPTCHA verification failed, please try again.', 'site-reviews')
        );
        $this->setErrors($error);
    }

    /**
     * @return array
     */
    protected function errors(array $errors)
    {
        $codes = $this->errorCodes();
        $errors = array_fill_keys($errors, '');
        return array_merge(
            array_intersect_key($codes, $errors), // known errors
            array_diff_key($errors, $codes) // unknown errors
        );
    }

    /**
     * @return array
     */
    protected function errorCodes()
    {
        return [];
    }

    /**
     * @return array|false
     */
    protected function makeRequest(array $request)
    {
        $response = wp_remote_post($this->siteverifyUrl(), [
            'body' => $request,
        ]);
        if (is_wp_error($response)) {
            glsr_log()->error($response->get_error_message());
            return false;
        }
        $body = json_decode(wp_remote_retrieve_body($response));
        $errors = Arr::consolidate(Arr::get($body, 'error-codes', Arr::get($body, 'errors')));
        return [
            'action' => Arr::get($body, 'action'),
            'errors' => $this->errors($errors),
            'score' => Arr::get($body, 'score', 0),
            'success' => wp_validate_boolean(Arr::get($body, 'success')),
        ];
    }

    /**
     * @return array
     */
    protected function request()
    {
        return [];
    }

    /**
     * @return string
     */
    protected function siteverifyUrl()
    {
        return '';
    }

    /**
     * @return string
     */
    protected function token()
    {
        return '';
    }

    /**
     * @return int
     */
    protected function verifyStatus()
    {
        if (!$this->isEnabled()) {
            return static::CAPTCHA_DISABLED;
        }
        return $this->verifyToken();
    }

    /**
     * @return int
     */
    protected function verifyToken()
    {
        $request = $this->request();
        $response = $this->makeRequest($request);
        if (empty($response)) {
            return static::CAPTCHA_FAILED;
        }
        if ($this->isTokenValid($response)) {
            return static::CAPTCHA_VALID;
        }
        if (!empty($response['errors'])) {
            $request['secret'] = Str::mask($request['secret'], 4, 4, 20);
            $request['sitekey'] = Str::mask($request['sitekey'], 4, 4, 20);
            glsr_log()->error($response)->debug($request);
        }
        if (empty($request['secret']) || empty($request['sitekey'])) {
            return static::CAPTCHA_FAILED;
        }
        return static::CAPTCHA_INVALID;
    }
}
