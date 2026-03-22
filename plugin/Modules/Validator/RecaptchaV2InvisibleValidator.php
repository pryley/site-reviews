<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Defaults\CaptchaConfigDefaults;
use GeminiLabs\SiteReviews\Modules\Captcha;

class RecaptchaV2InvisibleValidator extends CaptchaValidatorAbstract
{
    public const API_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * @see https://developers.google.com/recaptcha/docs/invisible
     */
    public function config(): array
    {
        $language = $this->getLocale();
        $urlParameters = array_filter([
            'hl' => $language,
            'render' => 'explicit',
        ]);
        $badgePosition = glsr_get_option('forms.captcha.badge');
        if (!in_array($badgePosition, ['bottomleft', 'bottomright'])) {
            $badgePosition = 'inline';
        }
        return glsr(CaptchaConfigDefaults::class)->merge([
            'badge' => $badgePosition,
            'class' => 'glsr-g-recaptcha',
            'language' => $language,
            'sitekey' => $this->siteKey(),
            'size' => 'invisible',
            'theme' => glsr_get_option('forms.captcha.theme'),
            'token_field' => 'g-recaptcha-response',
            'type' => 'recaptcha_v2_invisible',
            'urls' => [
                'nomodule' => add_query_arg($urlParameters, 'https://www.google.com/recaptcha/api.js'),
            ],
        ]);
    }

    public function isEnabled(): bool
    {
        return glsr(Captcha::class)->isEnabled('recaptcha_v2_invisible');
    }

    /**
     * @see https://developers.google.com/recaptcha/docs/verify#error_code_reference
     */
    protected function errorCodes(): array
    {
        return [
            'bad-request' => 'The request is invalid or malformed.',
            'invalid-input-response' => 'The response parameter is invalid or malformed.',
            'invalid-input-secret' => 'The secret parameter is invalid or malformed.',
            'missing-input-response' => 'The response parameter is missing.',
            'missing-input-secret' => 'The secret parameter is missing.',
            'sitekey_invalid' => 'Your site key is invalid.',
            'sitekey_missing' => 'Your site key is missing.',
            'timeout-or-duplicate' => 'The response is no longer valid: either is too old or has been used previously.',
        ];
    }

    protected function errors(array $errors): array
    {
        if (empty($this->siteSecret())) {
            $errors[] = 'missing-input-secret';
        }
        if (empty($this->siteKey())) {
            $errors[] = 'sitekey_missing';
        } elseif ('sitekey_invalid' === $this->token()) {
            $errors[] = 'sitekey_invalid';
        }
        return parent::errors(array_unique($errors));
    }

    /**
     * @see https://developers.google.com/recaptcha/docs/verify#api_request
     */
    protected function requestBody(): array
    {
        $token = $this->token();
        $response = array_key_exists($token, $this->errorCodes()) ? '' : $token;
        return [
            'remoteip' => $this->request->ip_address,
            'response' => $response,
            'secret' => $this->siteSecret(),
        ];
    }

    protected function siteKey(): string
    {
        return glsr_get_option('forms.recaptcha.key');
    }

    protected function siteSecret(): string
    {
        return glsr_get_option('forms.recaptcha.secret');
    }
}
