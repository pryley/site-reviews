<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Defaults\CaptchaConfigDefaults;
use GeminiLabs\SiteReviews\Modules\Captcha;

class HcaptchaValidator extends CaptchaValidatorAbstract
{
    public const API_URL = 'https://hcaptcha.com/siteverify';

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
            'class' => 'glsr-h-captcha', // @compat
            'language' => $language,
            'sitekey' => $this->siteKey(),
            'size' => 'normal',
            'theme' => glsr_get_option('forms.captcha.theme'),
            'token_field' => 'h-captcha-response',
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

    /**
     * @see https://docs.hcaptcha.com/#siteverify-error-codes-table
     */
    protected function errorCodes(): array
    {
        return [
            'already-seen-response' => 'The response parameter (verification token) was already verified once.',
            'bad-request' => 'The request is invalid or malformed.',
            'expired-input-response' => 'The response parameter (verification token) is expired. (120s default)',
            'invalid-input-response' => 'The response parameter (verification token) is invalid or malformed.',
            'invalid-input-secret' => 'Your secret key is invalid or malformed.',
            'invalid-remoteip' => 'The remoteip parameter is not a valid IP address or blinded value.',
            'missing-input-response' => 'The response parameter (verification token) is missing.',
            'missing-input-secret' => 'Your secret key is missing.',
            'missing-remoteip' => 'The remoteip parameter is missing.',
            'not-using-dummy-passcode' => 'You have used a testing sitekey but have not used its matching secret.',
            'sitekey-secret-mismatch' => 'The sitekey is not registered with the provided secret.',
            'sitekey_missing' => 'Your site key is missing.',
        ];
    }

    protected function errors(array $errors): array
    {
        if (empty($this->siteKey())) {
            $errors[] = 'sitekey_missing';
        }
        return parent::errors($errors);
    }

    /**
     * @see https://docs.hcaptcha.com/#verify-the-user-response-server-side
     */
    protected function requestBody(): array
    {
        return [
            'remoteip' => $this->request->ip_address,
            'response' => $this->token(),
            'secret' => $this->siteSecret(),
            'sitekey' => $this->siteKey(),
        ];
    }

    protected function siteKey(): string
    {
        return glsr_get_option('forms.hcaptcha.key');
    }

    protected function siteSecret(): string
    {
        return glsr_get_option('forms.hcaptcha.secret');
    }
}
