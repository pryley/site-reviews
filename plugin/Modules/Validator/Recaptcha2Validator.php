<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Captcha;

class Recaptcha2Validator extends CaptchaValidator
{
    /**
     * @return bool
     */
    public function isEnabled()
    {
        return glsr(Captcha::class)->isEnabled('recaptcha_v2_invisible');
    }

    /**
     * @return array
     */
    protected function request()
    {
        return [
            'remoteip' => $this->request->ip_address,
            'response' => $this->token(),
            'secret' => glsr_get_option('submissions.recaptcha.secret'),
            'sitekey' => glsr_get_option('submissions.recaptcha.key'),
        ];
    }

    /**
     * @return string
     */
    protected function siteverifyUrl()
    {
        return 'https://www.google.com/recaptcha/api/siteverify';
    }

    /**
     * @return string
     */
    protected function token()
    {
        return $this->request['_recaptcha'];
    }

    /**
     * @param object $response
     * @return array
     */
    protected function tokenErrors($response)
    {
        return Arr::consolidate(glsr_get($response, 'error-codes'));
    }
}
