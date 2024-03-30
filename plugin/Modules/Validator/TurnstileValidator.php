<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Modules\Captcha;

class TurnstileValidator extends CaptchaValidator
{
    public function isEnabled(): bool
    {
        return glsr(Captcha::class)->isEnabled('turnstile');
    }

    protected function data(): array
    {
        return [
            'remoteip' => $this->request->ip_address,
            'response' => $this->token(),
            'secret' => glsr_get_option('forms.turnstile.secret'),
            // The sitekey does not need to be sent in the request, but it's here
            // so we can return a better error response to the form.
            // @see CaptchaValidator::verifyToken()
            'sitekey' => glsr_get_option('forms.turnstile.key'),
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
        if (empty(glsr_get_option('forms.turnstile.key'))) {
            $errors[] = 'sitekey_missing';
        }
        return parent::errors($errors);
    }

    protected function siteverifyUrl(): string
    {
        return 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    }

    protected function token(): string
    {
        return $this->request['_turnstile'] ?? '';
    }
}
