<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Request;

abstract class ValidatorAbstract
{
    /**
     * @var array
     */
    protected $errors;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->errors = [];
        $this->request = $request;
    }

    /**
     * @return void
     */
    abstract public function performValidation();

    /**
     * @return Request
     */
    public function validate()
    {
        if (!$this->alreadyFailed()) {
            $this->performValidation();
        }
        return $this->request;
    }

    /**
     * @return bool
     */
    protected function alreadyFailed()
    {
        return is_array(glsr()->sessionGet('form_errors'));
    }

    /**
     * @param string $message
     * @param string $loggedMessage
     * @return void
     */
    protected function setErrors($message, $loggedMessage = null)
    {
        glsr()->sessionSet('form_errors', $this->errors);
        glsr()->sessionSet('form_message', $message);
        glsr()->sessionSet('form_values', $this->request->toArray());
        if (!empty($loggedMessage)) {
            glsr_log()->warning($loggedMessage)->debug($this->request->toArray());
        }
    }
}
