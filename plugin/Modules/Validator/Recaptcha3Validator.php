<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Modules\Captcha;

class Recaptcha3Validator extends CaptchaValidator
{
    public function isEnabled(): bool
    {
        return glsr(Captcha::class)->isEnabled('recaptcha_v3');
    }

    public function isTokenValid(array $response): bool
    {
        $threshold = glsr_get_option('forms.recaptcha_v3.threshold');
        $isValid = $response['success']
            && $response['score'] >= $threshold
            && 'submit_review' === $response['action'];
        if ($isValid) {
            glsr_log()->debug('reCAPTCHA v3 passed with score: '.$response['score']);
        } else {
            glsr_log()->debug('reCAPTCHA v3 failed with score: '.$response['score']);
        }
        return $isValid;
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
            'secret' => glsr_get_option('forms.recaptcha_v3.secret'),
            // The sitekey does not need to be sent in the request, but it's here
            // so we can return a better error response to the form.
            // @see CaptchaValidator::verifyToken()
            'sitekey' => glsr_get_option('forms.recaptcha_v3.key'),
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
        if (empty(glsr_get_option('forms.recaptcha_v3.secret'))) {
            $errors[] = 'missing-input-secret';
        }
        if (empty(glsr_get_option('forms.recaptcha_v3.key'))) {
            $errors[] = 'sitekey_missing';
        } elseif ('sitekey_invalid' === $this->token()) {
            $errors[] = 'sitekey_invalid';
        }
        return parent::errors(array_unique($errors));
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
