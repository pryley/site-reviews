<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Defaults\CaptchaConfigDefaults;
use GeminiLabs\SiteReviews\Modules\Captcha;

class FriendlycaptchaValidator extends CaptchaValidatorAbstract
{
    /**
     * @see https://docs.friendlycaptcha.com/
     */
    public function config(): array
    {
        return glsr(CaptchaConfigDefaults::class)->merge([
            'class' => glsr_get_option('forms.captcha.theme').' frc-captcha',
            'language' => $this->getLocale(),
            'sitekey' => $this->siteKey(),
            'theme' => glsr_get_option('forms.captcha.theme'),
            'type' => 'friendlycaptcha',
            'urls' => [ // order is intentional, module should always load first
                'module' => 'https://unpkg.com/friendly-challenge@0.9.4/widget.module.min.js',
                'nomodule' => 'https://unpkg.com/friendly-challenge@0.9.4/widget.min.js',
            ],
        ]);
    }

    public function isEnabled(): bool
    {
        return glsr(Captcha::class)->isEnabled('friendlycaptcha');
    }

    protected function data(): array
    {
        return [
            'secret' => $this->siteSecret(),
            'sitekey' => $this->siteKey(),
            'solution' => $this->token(),
        ];
    }

    protected function errorCodes(): array
    {
        return [
            'bad_request' => 'Something else is wrong with your request, e.g. your request body is empty.',
            'secret_invalid' => 'Your secret key is invalid.',
            'secret_missing' => 'Your secret key is missing.',
            'sitekey_invalid' => 'Your site key is likely invalid.',
            'sitekey_missing' => 'Your site key is missing.',
            'solution_invalid' => 'The solution you provided was invalid (perhaps the user tried to tamper with the puzzle).',
            'solution_missing' => 'You forgot to add the solution parameter.',
            'solution_timeout_or_duplicate' => 'The puzzle that the solution was for has expired or has already been used.',
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

    protected function siteKey(): string
    {
        return glsr_get_option('forms.friendlycaptcha.key');
    }

    protected function siteSecret(): string
    {
        return glsr_get_option('forms.friendlycaptcha.secret');
    }

    protected function siteVerifyUrl(): string
    {
        return 'https://api.friendlycaptcha.com/api/v1/siteverify';
    }

    protected function token(): string
    {
        return $this->request['_frcaptcha'] ?? '';
    }
}
