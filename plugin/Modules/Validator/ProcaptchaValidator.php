<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Defaults\CaptchaConfigDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Captcha;
use GeminiLabs\SiteReviews\Response;

class ProcaptchaValidator extends CaptchaValidatorAbstract
{
    public const API_URL = 'https://api.prosopo.io/siteverify';

    /**
     * @see https://docs.prosopo.io/
     */
    public function config(): array
    {
        return glsr(CaptchaConfigDefaults::class)->merge([
            'class' => glsr_get_option('forms.captcha.theme').' procaptcha',
            'captcha_type' => glsr_get_option('forms.procaptcha.type', 'frictionless'),
            'language' => $this->getLocale(),
            'sitekey' => $this->siteKey(),
            'theme' => glsr_get_option('forms.captcha.theme'),
            'token_field' => 'procaptcha-response',
            'type' => 'procaptcha',
            'urls' => [ // order is intentional, the module always loads first
                'module' => 'https://js.prosopo.io/js/procaptcha.bundle.js',
            ],
        ]);
    }

    public function isEnabled(): bool
    {
        return glsr(Captcha::class)->isEnabled('procaptcha');
    }

    protected function errorCodes(): array
    {
        return [
            'sitekey_invalid' => 'Your site key is likely invalid.',
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

    protected function requestArgs(array $body): array
    {
        return [
            'force' => true,
            'headers' => ['Content-Type' => 'application/json'],
            'body' => wp_json_encode($body),
        ];
    }

    /**
     * @see https://docs.prosopo.io/en/basics/server-side-verification/
     */
    protected function requestBody(): array
    {
        return [
            'ip' => $this->request->ip_address,
            'secret' => $this->siteSecret(),
            'token' => $this->token(),
        ];
    }

    protected function responseBody(Response $response): array
    {
        $body = $response->body();
        $status = $body['status'] ?? '';
        return [
            'action' => '', // unused
            'errors' => array_filter([$body['error'] ?? '']),
            'score' => 0, // unused
            'success' => 'ok' === $status && wp_validate_boolean($body['verified'] ?? false),
        ];
    }

    protected function siteKey(): string
    {
        return glsr_get_option('forms.procaptcha.key');
    }

    protected function siteSecret(): string
    {
        return glsr_get_option('forms.procaptcha.secret');
    }
}
