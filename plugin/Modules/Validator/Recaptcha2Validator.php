<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Modules\Captcha;

class Recaptcha2Validator extends CaptchaValidator
{
    public function isEnabled(): bool
    {
        return glsr(Captcha::class)->isEnabled('recaptcha_v2_invisible');
    }

    protected function data(): array
    {
        $token = $this->token();
        if (array_key_exists($token, $this->errorCodes())) {
            $token = '';
        }
        return [
            'remoteip' => $this->request->ip_address,
            'response' => $token,
            'secret' => glsr_get_option('forms.recaptcha.secret'),
            'sitekey' => glsr_get_option('forms.recaptcha.key'),
        ];
    }

    protected function errorCodes(): array
    {
        return [
            'bad-request' => 'The request is invalid or malformed.',
            'invalid-input-response' => 'The response parameter is invalid or malformed.',
            'invalid-input-secret' => 'The secret key is invalid or malformed.',
            'missing-input-response' => 'The response parameter is missing.',
            'missing-input-secret' => 'Your secret key is missing.',
            'sitekey_invalid' => 'Your site key is invalid.',
            'sitekey_missing' => 'Your site key is missing.',
            'timeout-or-duplicate' => 'The response is no longer valid: either is too old or has been used previously.',
        ];
    }

    protected function errors(array $errors): array
    {
        if (empty(glsr_get_option('forms.recaptcha.key'))) {
            $errors[] = 'sitekey_missing';
        } elseif ('sitekey_invalid' === $this->token()) {
            $errors[] = 'sitekey_invalid';
        }
        return parent::errors($errors);
    }

    protected function siteverifyUrl(): string
    {
        return 'https://www.google.com/recaptcha/api/siteverify';
    }

    protected function token(): string
    {
        return $this->request['_recaptcha'] ?? '';
    }
}
