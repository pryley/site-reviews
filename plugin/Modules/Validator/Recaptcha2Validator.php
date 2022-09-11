<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

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
    protected function errorCodes()
    {
        return [
            'bad-request' => 'The request is invalid or malformed.',
            'invalid-input-response' => 'The response parameter is invalid or malformed.',
            'invalid-input-secret' => 'The secret key is invalid or malformed.',
            'missing-input-response' => 'The response parameter is missing.',
            'missing-input-secret' => 'Your secret key is missing.',
            'sitekey_invalid' => 'Your site key is invalid.',
            'sitekey_missing' => 'Your site key is missing.',
            'timeout-or-duplicate' => 'The response is no longer valid: either is too old or has been used previously.',
        ];
    }

    /**
     * @return array
     */
    protected function errors(array $errors)
    {
        if (empty(glsr_get_option('forms.recaptcha.key'))) {
            $errors[] = 'sitekey_missing';
        } elseif ('sitekey_invalid' === $this->token()) {
            $errors[] = 'sitekey_invalid';
        }
        return parent::errors($errors);
    }

    /**
     * @return array
     */
    protected function request()
    {
        $token = $this->token();
        if (array_key_exists($token, $this->errorCodes())) {
            $token = '';
        }
        return [
            'remoteip' => $this->request->ip_address,
            'response' => $token,
            'secret' => glsr_get_option('forms.recaptcha.secret'),
            'sitekey' => glsr_get_option('forms.recaptcha.key'),
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
}
