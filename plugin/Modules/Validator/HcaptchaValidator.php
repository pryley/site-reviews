<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Captcha;

class HcaptchaValidator extends CaptchaValidator
{
    /**
     * @return bool
     */
    public function isEnabled()
    {
        return glsr(Captcha::class)->isEnabled('hcaptcha');
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
     * @return array
     */
    protected function request()
    {
        return [
            'remoteip' => $this->request->ip_address,
            'response' => $this->token(),
            'secret' => glsr_get_option('submissions.hcaptcha.secret'),
            'sitekey' => glsr_get_option('submissions.hcaptcha.key'),
        ];
    }

    /**
     * @return string
     */
    protected function siteverifyUrl()
    {
        return 'https://hcaptcha.com/siteverify';
    }

    /**
     * @return string
     */
    protected function token()
    {
        return $this->request['_hcaptcha'];
    }
}
