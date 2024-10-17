<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Defaults\CaptchaConfigDefaults;
use GeminiLabs\SiteReviews\Modules\Captcha;
 
class ProcaptchaValidator extends CaptchaValidatorAbstract
{
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

    public function isTokenValid(array $response): bool
    {
        return $response['success'];
    }

    protected function data(): array
    {
        return [
            'token' => $this->token(),
            'secret' => $this->siteSecret(),
        ];
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

    protected function response(array $body): array
    {
        $body = wp_parse_args($body, [
            'error' => '',
            'status' => '',
            'verified' => false,
        ]);
        return [
            'action' => '', // unused
            'errors' => [$body['error']],
            'score' => 0, // unused
            'success' => ('ok' === $body['status'] && wp_validate_boolean($body['verified'])),
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

    protected function siteVerifyUrl(): string
    {
        return 'https://api.prosopo.io/siteverify';
    }

    protected function token(): string
    {
        return $this->request['_procaptcha'] ?? '';
    }
}
