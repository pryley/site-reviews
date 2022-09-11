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
            'data-badge' => Arr::get($config, 'badge'),
            'data-size' => Arr::get($config, 'size'),
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
        $isEnabled = 'all' == $usage || ('guest' == $usage && !is_user_logged_in());
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
        ];
    }

    /**
     * @return array
     */
    protected function configHcaptcha()
    {
        return [
            'class' => 'h-captcha',
            'badge' => glsr_get_option('forms.captcha.position'),
            'sitekey' => glsr_get_option('forms.hcaptcha.key'),
            'size' => 'normal',
            'theme' => glsr_get_option('forms.captcha.theme'),
            'type' => 'hcaptcha',
        ];
    }

    /**
     * @return array
     */
    protected function configRecaptchaV2Invisible()
    {
        return [
            'class' => 'g-recaptcha',
            'badge' => glsr_get_option('forms.captcha.position'),
            'sitekey' => glsr_get_option('forms.recaptcha.key'),
            'size' => 'invisible',
            'theme' => glsr_get_option('forms.captcha.theme'),
            'type' => 'recaptcha_v2_invisible',
        ];
    }

    /**
     * @return array
     */
    protected function configRecaptchaV3()
    {
        return [
            'class' => 'g-recaptcha',
            'badge' => glsr_get_option('forms.captcha.position'),
            'sitekey' => glsr_get_option('forms.recaptcha_v3.key'),
            'size' => 'invisible',
            'theme' => glsr_get_option('forms.captcha.theme'),
            'type' => 'recaptcha_v3',
        ];
    }
}
