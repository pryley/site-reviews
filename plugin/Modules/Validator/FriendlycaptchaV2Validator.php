<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Defaults\CaptchaConfigDefaults;
use GeminiLabs\SiteReviews\Modules\Captcha;

class FriendlycaptchaV2Validator extends CaptchaValidatorAbstract
{
    public const API_URL = 'https://global.frcapi.com/api/v2/captcha/siteverify';

    /**
     * @see https://docs.friendlycaptcha.com/
     */
    public function config(): array
    {
        return glsr(CaptchaConfigDefaults::class)->merge([
            'class' => glsr_get_option('forms.captcha.theme').' frc-captcha',
            'language' => '', // v2 automatically matches the language on the website
            'sitekey' => $this->siteKey(),
            'theme' => glsr_get_option('forms.captcha.theme'),
            'token_field' => 'frc-captcha-response',
            'type' => 'friendlycaptcha_v2',
            'urls' => [ // order is intentional, module should always load first
                'module' => 'https://unpkg.com/@friendlycaptcha/sdk@0.2.0/site.min.js',
                'nomodule' => 'https://unpkg.com/@friendlycaptcha/sdk@0.2.0/site.compat.min.js',
            ],
        ]);
    }

    public function isEnabled(): bool
    {
        return glsr(Captcha::class)->isEnabled('friendlycaptcha_v2');
    }

    /**
     * @see https://developer.friendlycaptcha.com/docs/v2/api/siteverify
     */
    protected function errorCodes(): array
    {
        return [
            'auth_invalid' => 'The API key you provided was invalid.',
            'auth_required' => 'You forgot to set the X-API-Key header.',
            'bad_request' => 'Something else is wrong with your request, e.g. your request body is empty.',
            'response_duplicate' => 'The response has already been used.',
            'response_invalid' => 'The response you provided was invalid (perhaps the user tried to work around the captcha).',
            'response_missing' => 'You forgot to add the response parameter.',
            'response_timeout' => 'The response has expired.',
            'sitekey_invalid' => 'The sitekey in your request is invalid.',
            'sitekey_missing' => 'Your site key is missing.',
        ];
    }

    protected function errors(array $errors): array
    {
        if (empty($this->siteKey())) {
            $errors[] = 'sitekey_missing';
        } elseif ('sitekey_invalid' === $this->token()) {
            $errors[] = 'sitekey_invalid';
        }
        return parent::errors($errors);
    }

    protected function requestArgs(array $body): array
    {
        return [
            'force' => true,
            'headers' => [
                'Content-Type' => 'application/json',
                'X-API-Key' => $this->siteSecret(),
            ],
            'body' => wp_json_encode($body),
        ];
    }

    /**
     * @see https://developer.friendlycaptcha.com/docs/v2/api/siteverify
     */
    protected function requestBody(): array
    {
        return [
            'response' => $this->token(),
            'sitekey' => $this->siteKey(),
        ];
    }

    protected function siteKey(): string
    {
        return glsr_get_option('forms.friendlycaptcha.key');
    }

    protected function siteSecret(): string
    {
        return glsr_get_option('forms.friendlycaptcha.secret');
    }
}
