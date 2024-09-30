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
            'sitekey' => glsr_get_option('forms.procaptcha.key'),
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

    protected function data(): array
    {
        return [
            'token' => $this->token(),
            'secret' => glsr_get_option('forms.procaptcha.secret'),
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
        if (empty(glsr_get_option('forms.procaptcha.key'))) {
            $errors[] = 'sitekey_missing';
        }
        return parent::errors($errors);
    }

    protected function makeRequest(array $data): array
    {
        $response = wp_remote_post($this->siteverifyUrl(), [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => wp_json_encode($data),
        ]);
        if (is_wp_error($response)) {
            glsr_log()->error($response->get_error_message());
            return [];
        }
        $body = json_decode(wp_remote_retrieve_body($response));
        $body = wp_parse_args($body, [
            'status' => '',
            'verified' => false,
        ]);
        return [
            'action' => '', // unused
            'errors' => $this->errors([]),
            'score' => 0, // unused
            'success' => ('ok' === $body['status'] && wp_validate_boolean($body['verified'])),
        ];
    }

    protected function siteverifyUrl(): string
    {
        return 'https://api.prosopo.io/siteverify';
    }

    protected function token(): string
    {
        return $this->request['_procaptcha'] ?? '';
    }
}
