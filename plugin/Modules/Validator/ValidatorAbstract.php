<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Contracts\ValidatorContract;
use GeminiLabs\SiteReviews\Request;

abstract class ValidatorAbstract implements ValidatorContract
{
    protected array $errors = [];
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @compat < v7.1
     * @todo remove in v8
     */
    public function __call(string $method, array $args = [])
    {
        if ('setErrors' === $method) {
            call_user_func_array([$this, 'fail'], $args);
            return;
        }
        throw new \BadMethodCallException("Method [$method] does not exist.");
    }

    public function alreadyFailed(): bool
    {
        return glsr()->session()->cast('form_invalid', 'bool');
    }

    public function fail(string $message, ?string $loggedMessage = null): void
    {
        glsr()->sessionSet('form_errors', $this->errors);
        glsr()->sessionSet('form_invalid', true);
        glsr()->sessionSet('form_message', $message);
        glsr()->sessionSet('form_values', $this->request->toArray());
        if (!empty($loggedMessage)) {
            glsr_log()->info($loggedMessage)->debug($this->request->toArray());
        }
    }

    public function request(): Request
    {
        return $this->request;
    }

    /**
     * @return static
     */
    public function validate()
    {
        if (!$this->alreadyFailed()) {
            $this->performValidation();
        }
        return $this;
    }
}
