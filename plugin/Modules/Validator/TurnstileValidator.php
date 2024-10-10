<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Defaults\CaptchaConfigDefaults;
use GeminiLabs\SiteReviews\Modules\Captcha;

class TurnstileValidator extends CaptchaValidatorAbstract
{
    /**
     * @see https://developers.google.com/recaptcha/docs/v3
     */
    public function config(): array
    {
        $language = $this->getLocale();
        $urlParameters = array_filter([
            'hl' => $language,
            'render' => 'explicit',
        ]);
        return glsr(CaptchaConfigDefaults::class)->merge([
            'class' => 'glsr-cf-turnstile',
            'language' => $language,
            'sitekey' => $this->siteKey(),
            'theme' => glsr_get_option('forms.captcha.theme'),
            'type' => 'turnstile',
            'urls' => [
                'nomodule' => add_query_arg($urlParameters, 'https://challenges.cloudflare.com/turnstile/v0/api.js'),
            ],
        ]);
    }

    public function isEnabled(): bool
    {
        return glsr(Captcha::class)->isEnabled('turnstile');
    }

    protected function data(): array
    {
        return [
            'remoteip' => $this->request->ip_address,
            'response' => $this->token(),
            'secret' => $this->siteSecret(),
        ];
    }

    protected function errorCodes(): array
    {
        return [
            'bad-request' => 'The request was rejected because it was malformed.',
            'internal-error' => 'An internal error happened while validating the response. The request can be retried.',
            'invalid-input-response' => 'The response parameter is invalid or has expired.',
            'invalid-input-secret' => 'The secret parameter was invalid or did not exist.',
            'missing-input-response' => 'The response parameter was not passed.',
            'missing-input-secret' => 'The secret parameter was not passed.',
            'timeout-or-duplicate' => 'The response parameter has already been validated before.',
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

    protected function siteKey(): string
    {
        return glsr_get_option('forms.turnstile.key');
    }

    protected function siteSecret(): string
    {
        return glsr_get_option('forms.turnstile.secret');
    }

    protected function siteVerifyUrl(): string
    {
        return 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    }

    protected function token(): string
    {
        return $this->request['_turnstile'] ?? '';
    }
}
