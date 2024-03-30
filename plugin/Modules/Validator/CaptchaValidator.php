<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Captcha;

class CaptchaValidator extends ValidatorAbstract
{
    public const CAPTCHA_DISABLED = 0;
    // public const CAPTCHA_EMPTY = 1;
    public const CAPTCHA_FAILED = 2;
    public const CAPTCHA_INVALID = 3;
    public const CAPTCHA_VALID = 4;

    protected $status;

    public function isEnabled(): bool
    {
        return false;
    }

    public function isTokenValid(array $response): bool
    {
        return $response['success'];
    }

    public function isValid(): bool
    {
        if (in_array($this->status, [static::CAPTCHA_DISABLED, static::CAPTCHA_VALID])) {
            return true;
        }
        return false;
    }

    public function performValidation(): void
    {
        $this->status = $this->verifyStatus();
        if ($this->isValid()) {
            return;
        }
        $error = Helper::ifTrue($this->status === static::CAPTCHA_FAILED,
            __('The CAPTCHA failed to load, please refresh the page and try again.', 'site-reviews'),
            __('The CAPTCHA verification failed, please try again.', 'site-reviews')
        );
        $this->setErrors($error);
    }

    protected function data(): array
    {
        return [];
    }

    protected function errors(array $errors): array
    {
        $codes = $this->errorCodes();
        $errors = array_fill_keys($errors, '');
        return array_merge(
            array_intersect_key($codes, $errors), // known errors
            array_diff_key($errors, $codes) // unknown errors
        );
    }

    protected function errorCodes(): array
    {
        return [];
    }

    protected function makeRequest(array $data): array
    {
        $response = wp_remote_post($this->siteverifyUrl(), [
            'body' => $data,
        ]);
        if (is_wp_error($response)) {
            glsr_log()->error($response->get_error_message());
            return [];
        }
        $body = json_decode(wp_remote_retrieve_body($response));
        $errors = Arr::consolidate(Arr::get($body, 'error-codes', Arr::get($body, 'errors')));
        return [
            'action' => Arr::get($body, 'action'),
            'errors' => $this->errors($errors),
            'score' => Arr::get($body, 'score', 0),
            'success' => wp_validate_boolean(Arr::get($body, 'success')),
        ];
    }

    protected function siteverifyUrl(): string
    {
        return '';
    }

    protected function token(): string
    {
        return '';
    }

    protected function verifyStatus(): int
    {
        if (!$this->isEnabled()) {
            return static::CAPTCHA_DISABLED;
        }
        return $this->verifyToken();
    }

    protected function verifyToken(): int
    {
        $data = $this->data();
        $response = $this->makeRequest($data);
        if (empty($response)) {
            return static::CAPTCHA_FAILED;
        }
        if ($this->isTokenValid($response)) {
            return static::CAPTCHA_VALID;
        }
        if (!empty($response['errors'])) {
            $data['secret'] = Str::mask($data['secret'], 4, 4, 20);
            $data['sitekey'] = Str::mask($data['sitekey'], 4, 4, 20);
            glsr_log()->error($response)->debug($data);
        }
        if (empty($data['secret']) || empty($data['sitekey'])) {
            return static::CAPTCHA_FAILED;
        }
        return static::CAPTCHA_INVALID;
    }
}
