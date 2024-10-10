<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Defaults\CaptchaConfigDefaults;
use GeminiLabs\SiteReviews\Modules\Captcha;

class HcaptchaValidator extends CaptchaValidatorAbstract
{
    /**
     * @see https://docs.hcaptcha.com/
     */
    public function config(): array
    {
        $language = $this->getLocale();
        $urlParameters = array_filter([
            'hl' => $language,
            'render' => 'explicit',
        ]);
        return glsr(CaptchaConfigDefaults::class)->merge([
            'badge' => glsr_get_option('forms.captcha.position'),
            'class' => 'glsr-h-captcha', // @compat
            'language' => $language,
            'sitekey' => $this->siteKey(),
            'size' => 'normal',
            'theme' => glsr_get_option('forms.captcha.theme'),
            'type' => 'hcaptcha',
            'urls' => [
                'nomodule' => add_query_arg($urlParameters, 'https://js.hcaptcha.com/1/api.js'),
            ],
        ]);
    }

    public function isEnabled(): bool
    {
        return glsr(Captcha::class)->isEnabled('hcaptcha');
    }

    protected function data(): array
    {
        return [
            'remoteip' => $this->request->ip_address,
            'response' => $this->token(),
            'secret' => $this->siteSecret(),
            'sitekey' => $this->siteKey(),
        ];
    }

    protected function errorCodes(): array
    {
        return [
            'bad-request' => 'The request is invalid or malformed.',
            'invalid-input-response' => 'The response parameter (verification token) is invalid or malformed.',
            'invalid-input-secret' => 'Your secret key is invalid or malformed.',
            'invalid-or-already-seen-response' => 'The response parameter has already been checked, or has another issue.',
            'missing-input-response' => 'The response parameter (verification token) is missing.',
            'missing-input-secret' => 'Your secret key is missing.',
            'not-using-dummy-passcode' => 'You have used a testing site key but have not used its matching secret.',
            'sitekey_missing' => 'Your site key is missing.',
            'sitekey-secret-mismatch' => 'The site key is not registered with the provided secret.',
        ];
    }

    protected function errors(array $errors): array
    {
        if (empty($this->siteKey())) {
            $errors[] = 'sitekey_missing';
        }
        return parent::errors($errors);
    }

    protected function siteKey(): string
    {
        return glsr_get_option('forms.hcaptcha.key');
    }

    protected function siteSecret(): string
    {
        return glsr_get_option('forms.hcaptcha.secret');
    }

    protected function siteVerifyUrl(): string
    {
        return 'https://hcaptcha.com/siteverify';
    }

    protected function token(): string
    {
        return $this->request['_hcaptcha'] ?? '';
    }
}
