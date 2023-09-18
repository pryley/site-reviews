<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Encryption;

class Request extends Arguments
{
    /**
     * @param mixed $key
     * @param mixed $fallback
     * @return mixed
     */
    public function get($key, $fallback = null)
    {
        $value = Arr::get($this->getArrayCopy(), $key, null);
        if (is_null($fallback) || !Helper::isEmpty($value)) {
            return $value;
        }
        return Helper::runClosure($fallback);
    }

    /**
     * @return static
     * @todo support array values
     */
    public static function inputGet()
    {
        $values = [];
        if ($token = filter_input(INPUT_GET, glsr()->prefix)) {
            $token = sanitize_text_field($token);
            $values = glsr(Encryption::class)->decryptRequest($token);
        }
        return new static($values);
    }

    /**
     * @return static
     */
    public static function inputPost()
    {
        $values = Helper::filterInputArray(glsr()->id);
        if (Helper::filterInput('action') === glsr()->prefix.'action') {
            $values['_ajax_request'] = true;
        }
        if ('submit-review' === Helper::filterInput('_action', $values)) {
            $values['_frcaptcha'] = Helper::filterInput('frc-captcha-solution');
            $values['_hcaptcha'] = Helper::filterInput('h-captcha-response');
            $values['_recaptcha'] = Helper::filterInput('g-recaptcha-response');
            $values['_turnstile'] = Helper::filterInput('cf-turnstile-response');
        }
        return new static($values);
    }
}
