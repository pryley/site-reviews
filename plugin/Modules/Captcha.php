<?php

namespace GeminiLabs\SiteReviews\Modules;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Html\Builder;

class Captcha
{
    /**
     * @return array
     */
    public function config()
    {
        if (!$this->isEnabled()) {
            return [];
        }
        $method = Helper::buildMethodName(glsr_get_option('forms.captcha.integration'), 'config');
        if (!method_exists($this, $method)) {
            return [];
        }
        return call_user_func([$this, $method]);
    }

    /**
     * @return string|void
     */
    public function container()
    {
        if (!$this->isEnabled()) {
            return;
        }
        $config = $this->config();
        return glsr(Builder::class)->div([
            'class' => 'glsr-captcha-holder',
        ]);
    }

    /**
     * @param string $service
     * @return bool
     */
    public function isEnabled($service = '')
    {
        $integration = glsr_get_option('forms.captcha.integration');
        $usage = glsr_get_option('forms.captcha.usage');
        $isEnabled = 'all' === $usage || ('guest' === $usage && !is_user_logged_in());
        if (!empty($service)) {
            return $integration === $service && $isEnabled;
        }
        return !empty($integration) && $isEnabled;
    }

    /**
     * @return array
     */
    protected function configFriendlycaptcha()
    {
        return [
            'class' => 'frc-captcha '.glsr_get_option('forms.captcha.theme'),
            'sitekey' => glsr_get_option('forms.friendlycaptcha.key'),
            'theme' => glsr_get_option('forms.captcha.theme'),
            'type' => 'friendlycaptcha',
            'urls' => [ // order is intentional, the module always loads first
                'https://unpkg.com/friendly-challenge@0.9.4/widget.module.min.js',
                'https://unpkg.com/friendly-challenge@0.9.4/widget.min.js',
            ],
        ];
    }

    /**
     * @return array
     */
    protected function configHcaptcha()
    {
        $urlParameters = [
            'hl' => glsr()->filterString('captcha/language', get_locale()),
            'render' => 'explicit',
        ];
        return [
            'class' => 'glsr-h-captcha', // @compat
            'badge' => glsr_get_option('forms.captcha.position'),
            'sitekey' => glsr_get_option('forms.hcaptcha.key'),
            'size' => 'normal',
            'theme' => glsr_get_option('forms.captcha.theme'),
            'type' => 'hcaptcha',
            'urls' => [
                add_query_arg($urlParameters, 'https://js.hcaptcha.com/1/api.js'),
            ],
        ];
    }

    /**
     * @return array
     */
    protected function configRecaptchaV2Invisible()
    {
        $urlParameters = [
            'hl' => glsr()->filterString('captcha/language', get_locale()),
            'render' => 'explicit',
        ];
        return [
            'class' => 'g-recaptcha',
            'badge' => glsr_get_option('forms.captcha.position'),
            'sitekey' => glsr_get_option('forms.recaptcha.key'),
            'size' => 'invisible',
            'theme' => glsr_get_option('forms.captcha.theme'),
            'type' => 'recaptcha_v2_invisible',
            'urls' => [
                add_query_arg($urlParameters, 'https://www.google.com/recaptcha/api.js'),
            ],
        ];
    }

    /**
     * @return array
     */
    protected function configRecaptchaV3()
    {
        $urlParameters = [
            'hl' => glsr()->filterString('captcha/language', get_locale()),
            'render' => 'explicit',
        ];
        return [
            'class' => 'g-recaptcha',
            'badge' => glsr_get_option('forms.captcha.position'),
            'sitekey' => glsr_get_option('forms.recaptcha_v3.key'),
            'size' => 'invisible',
            'theme' => glsr_get_option('forms.captcha.theme'),
            'type' => 'recaptcha_v3',
            'urls' => [
                add_query_arg($urlParameters, 'https://www.google.com/recaptcha/api.js'),
            ],
        ];
    }

    /**
     * @return array
     */
    protected function configTurnstile()
    {
        $urlParameters = [
            'hl' => glsr()->filterString('captcha/language', get_locale()),
            'render' => 'explicit',
        ];
        return [
            'class' => 'glsr-cf-turnstile',
            'badge' => '',
            'sitekey' => glsr_get_option('forms.turnstile.key'),
            'size' => '',
            'theme' => glsr_get_option('forms.captcha.theme'),
            'type' => 'turnstile',
            'urls' => [
                add_query_arg($urlParameters, 'https://challenges.cloudflare.com/turnstile/v0/api.js'),
            ],
        ];
    }
}
