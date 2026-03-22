<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Api;
use GeminiLabs\SiteReviews\Helper;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Response;

abstract class CaptchaValidatorAbstract extends ValidatorAbstract
{
    public const API_URL = '';
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

    protected function isTokenValid(array $responseBody): bool
    {
        return $responseBody['success'];
    }

    protected function requestArgs(array $body): array
    {
        return [
            'body' => $body,
            'force' => true,
        ];
    }

    protected function requestBody(): array
    {
        return [];
    }

    protected function responseBody(Response $response): array
    {
        $body = $response->body();
        $errors = Arr::consolidate($body['error-codes'] ?? $body['errors'] ?? []);
        return [
            'action' => $body['action'] ?? '',
            'errors' => $this->errors($errors),
            'score' => $body['score'] ?? 0,
            'success' => wp_validate_boolean($body['success'] ?? false),
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

    protected function token(): string
    {
        return $this->request['_captcha'] ?? '';
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
        $body = $this->requestBody();
        $response = glsr(Api::class, ['url' => static::API_URL])->post('', $this->requestArgs($body));
        if ($response->failed()) {
            return static::CAPTCHA_FAILED;
        }
        $responseBody = $this->responseBody($response);
        if ($this->isTokenValid($responseBody)) {
            return static::CAPTCHA_VALID;
        }
        if (!empty($responseBody['errors'])) {
            $body['secret'] = Str::mask($this->siteSecret(), 4, 4, 20);
            $body['sitekey'] = Str::mask($this->siteKey(), 4, 4, 20);
            glsr_log()->error($responseBody)->debug($body);
        }
        if (empty($this->siteSecret()) || empty($this->siteKey())) {
            return static::CAPTCHA_FAILED;
        }
        return static::CAPTCHA_INVALID;
    }
}
