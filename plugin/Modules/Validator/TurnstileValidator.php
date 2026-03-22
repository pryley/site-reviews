<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Defaults\CaptchaConfigDefaults;
use GeminiLabs\SiteReviews\Modules\Captcha;

class TurnstileValidator extends CaptchaValidatorAbstract
{
    public const API_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    /**
     * @see https://developers.google.com/recaptcha/docs/v3
     */
    public function config(): array
    {
        return glsr(CaptchaConfigDefaults::class)->merge([
            'class' => 'glsr-cf-turnstile',
            'language' => $this->getLocale(),
            'sitekey' => $this->siteKey(),
            'theme' => glsr_get_option('forms.captcha.theme'),
            'token_field' => 'cf-turnstile-response',
            'type' => 'turnstile',
            'urls' => [
                'nomodule' => 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit',
            ],
        ]);
    }

    public function isEnabled(): bool
    {
        return glsr(Captcha::class)->isEnabled('turnstile');
    }

    /**
     * @see https://developers.cloudflare.com/turnstile/get-started/server-side-validation/#request-format
     */
    protected function errorCodes(): array
    {
        return [
            'bad-request' => 'Request is malformed: check request format and parameters',
            'internal-error' => 'Internal error occurred: retry the request',
            'invalid-input-response' => 'Token is invalid, malformed, or expired: user should retry the challenge',
            'invalid-input-secret' => 'Secret key is invalid or expired: check your secret key in the Cloudflare dashboard',
            'missing-input-response' => 'Response parameter was not provided: ensure token is included',
            'missing-input-secret' => 'Secret parameter not provided',
            'sitekey_missing' => 'Your site key is missing',
            'timeout-or-duplicate' => 'Token has already been validated',
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
     * @see https://developers.cloudflare.com/turnstile/get-started/server-side-validation/#request-format
     */
    protected function requestBody(): array
    {
        return [
            'remoteip' => $this->request->ip_address,
            'response' => $this->token(),
            'secret' => $this->siteSecret(),
        ];
    }

    protected function siteKey(): string
    {
        return glsr_get_option('forms.turnstile.key');
    }

    protected function siteSecret(): string
    {
        return glsr_get_option('forms.turnstile.secret');
    }
}
