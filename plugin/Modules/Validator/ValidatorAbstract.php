<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Request;

abstract class ValidatorAbstract
{
    protected array $errors = [];
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function alreadyFailed(): bool
    {
        return is_array(glsr()->sessionGet('form_errors'));
    }

    abstract public function isValid(): bool;

    public function request(): Request
    {
        return $this->request;
    }

    abstract public function performValidation(): void;

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

    protected function setErrors(string $message, ?string $loggedMessage = null): void
    {
        glsr()->sessionSet('form_errors', $this->errors);
        glsr()->sessionSet('form_message', $message);
        glsr()->sessionSet('form_values', $this->request->toArray());
        if (!empty($loggedMessage)) {
            glsr_log()->info($loggedMessage)->debug($this->request->toArray());
        }
    }
}
