<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Captcha;

class Recaptcha3Validator extends CaptchaValidator
{
    /**
     * @return bool
     */
    public function isEnabled()
    {
        return glsr(Captcha::class)->isEnabled('recaptcha_v3');
    }

    /**
     * @param object $response
     * @return array
     */
    protected function isTokenError($response)
    {
        $errors = Arr::consolidate(glsr_get($response, 'error-codes'));
        if (!empty($errors)) {
            glsr_log()->error($errors);
            return true;
        }
        return false;
    }

    /**
     * @param object $response
     * @return bool
     */
    public function isTokenValid($response)
    {
        return wp_validate_boolean($response->success)
            && $response->score >= $this->threshold();
    }

    /**
     * @return array
     */
    protected function request()
    {
        return [
            'remoteip' => $this->request->ip_address,
            'response' => $this->token(),
            'secret' => glsr_get_option('submissions.recaptcha_v3.secret'),
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
    protected function threshold()
    {
        return glsr_get_option('submissions.recaptcha_v3.threshold');
    }

    /**
     * @return string
     */
    protected function token()
    {
        return $this->request['_recaptcha'];
    }
}
