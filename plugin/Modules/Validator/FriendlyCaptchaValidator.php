<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Captcha;

class FriendlyCaptchaValidator extends CaptchaValidator
{
    /**
     * @return bool
     */
    public function isEnabled()
    {
        return glsr(Captcha::class)->isEnabled('friendlycaptcha');
    }

    /**
     * @param object $response
     * @return array
     */
    protected function isTokenError($response)
    {
        $errors = Arr::consolidate(glsr_get($response, 'errors'));
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
            'secret' => glsr_get_option('submissions.friendlycaptcha.secret'),
            'sitekey' => glsr_get_option('submissions.friendlycaptcha.key'),
            'solution' => $this->token(),
        ];
    }

    /**
     * @return string
     */
    protected function siteverifyUrl()
    {
        return 'https://api.friendlycaptcha.com/api/v1/siteverify';
    }

    /**
     * @return string
     */
    protected function token()
    {
        return $this->request['_frcaptcha'];
    }
}
