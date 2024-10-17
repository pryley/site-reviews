<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Captcha;

abstract class CaptchaValidatorAbstract extends ValidatorAbstract
{
    public const CAPTCHA_DISABLED = 0;
    public const CAPTCHA_EMPTY = 1;
    public const CAPTCHA_FAILED = 2;
    public const CAPTCHA_INVALID = 3;
    public const CAPTCHA_VALID = 4;

    protected $status;

    abstract public function config(): array;

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
        $this->fail($error);
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

    protected function getLocale(): string
    {
        $locale = '';
        if (function_exists('locale_parse')) {
            $values = locale_parse(get_locale());
            if (!empty($values['language'])) {
                $locale = $values['language'];
            }
        }
        return glsr()->filterString('captcha/language', $locale);
    }

    protected function makeRequest(array $data): array
    {
        $response = wp_remote_post($this->siteVerifyUrl(), [
            'body' => $data,
        ]);
        if (is_wp_error($response)) {
            glsr_log()->error($response->get_error_message());
            return [];
        }
        $body = json_decode(wp_remote_retrieve_body($response), true);
        return $this->response($body);
    }

    protected function response(array $body): array
    {
        $errors = Arr::consolidate(Arr::get($body, 'error-codes', Arr::get($body, 'errors')));
        return [
            'action' => Arr::get($body, 'action'),
            'errors' => $this->errors($errors),
            'score' => Arr::get($body, 'score', 0),
            'success' => wp_validate_boolean(Arr::get($body, 'success')),
        ];
    }

    protected function siteKey(): string
    {
        return '';
    }

    protected function siteSecret(): string
    {
        return '';
    }

    protected function siteVerifyUrl(): string
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
        if (empty($this->token())) {
            return static::CAPTCHA_EMPTY; // fail early
        }
        $data = $this->data();
        $response = $this->makeRequest($data);
        if (empty($response)) {
            return static::CAPTCHA_FAILED;
        }
        if ($this->isTokenValid($response)) {
            return static::CAPTCHA_VALID;
        }
        if (!empty($response['errors'])) {
            $data['secret'] = Str::mask($this->siteSecret(), 4, 4, 20);
            $data['sitekey'] = Str::mask($this->siteKey(), 4, 4, 20);
            glsr_log()->error($response)->debug($data);
        }
        if (empty($data['secret']) || empty($data['sitekey'])) {
            return static::CAPTCHA_FAILED;
        }
        return static::CAPTCHA_INVALID;
    }
}
