<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Captcha;

class CaptchaValidator extends ValidatorAbstract
{
    const CAPTCHA_DISABLED = 0;
    // const CAPTCHA_EMPTY = 1;
    const CAPTCHA_FAILED = 2;
    const CAPTCHA_INVALID = 3;
    const CAPTCHA_VALID = 4;

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
        return wp_validate_boolean($response['success']);
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        if (in_array($this->status, [static::CAPTCHA_DISABLED, static::CAPTCHA_VALID])) {
            return true;
        }
        glsr()->sessionSet('form_captcha', 'reset');
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
        return [
            'action' => Arr::get($body, 'action'),
            'errors' => Arr::get($body, 'error-codes', Arr::get($body, 'errors', [])),
            'score' => Arr::get($body, 'score', 0),
            'success' => Arr::get($body, 'success'),
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
        if (!empty($this->token())) {
            return $this->verifyToken();
        }
        return static::CAPTCHA_FAILED;
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
            glsr_log()->error($response)->debug($request);
        }
        return static::CAPTCHA_INVALID;
    }
}
