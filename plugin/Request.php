<?php

namespace GeminiLabs\SiteReviews;

use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Captcha;
use GeminiLabs\SiteReviews\Modules\Encryption;

class Request extends Arguments
{
    public function decrypt(string $key): string
    {
        $value = glsr(Encryption::class)->decrypt($this->cast($key, 'string'));
        return Cast::toString($value);
    }

    /**
     * @param mixed $key
     * @param mixed $fallback
     *
     * @return mixed
     */
    public function get($key, $fallback = null)
    {
        $value = Arr::get($this->getArrayCopy(), $key, null);
        if (is_null($fallback)) {
            return $value;
        }
        if (!Helper::isEmpty($value)) {
            return $value;
        }
        return Helper::runClosure($fallback);
    }

    /**
     * @return static
     *
     * @todo support array values
     */
    public static function inputGet(): Request
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
    public static function inputPost(): Request
    {
        $action = Helper::filterInput('action');
        $values = Helper::filterInputArray(glsr()->id);
        if (in_array($action, [glsr()->prefix.'admin_action', glsr()->prefix.'public_action'])) {
            $values['_ajax_request'] = true;
        }
        $requestAction = Helper::filterInput('_action', $values);
        if (in_array($requestAction, glsr(Captcha::class)->actions())) {
            $values['_frcaptcha'] = Helper::filterInput('frc-captcha-solution');
            $values['_hcaptcha'] = Helper::filterInput('h-captcha-response');
            $values['_procaptcha'] = Helper::filterInput('procaptcha-response');
            $values['_recaptcha'] = Helper::filterInput('g-recaptcha-response');
            $values['_turnstile'] = Helper::filterInput('cf-turnstile-response');
        }
        return new static($values);
    }

    /**
     * @param mixed $value
     */
    public function set(string $path, $value): void
    {
        $storage = Arr::set($this->getArrayCopy(), $path, $value);
        $this->exchangeArray($storage);
        if (!$this->exists('form_signature') || 'form_signature' === $path) {
            return;
        }
        $values = $this->decrypt('form_signature');
        $values = wp_parse_args(maybe_unserialize($values));
        if (array_key_exists($path, $values)) {
            $values[$path] = $value;
            $storage['form_signature'] = glsr(Encryption::class)->encrypt(maybe_serialize($values));
            $this->exchangeArray($storage);
        }
    }
}
