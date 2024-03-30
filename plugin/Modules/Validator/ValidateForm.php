<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Request;

class ValidateForm
{
    /**
     * @var bool
     */
    public $blacklisted;

    /**
     * @var array|false
     */
    public $errors;

    /**
     * @var string
     */
    public $message;

    /**
     * @var Request
     */
    public $request;

    /**
     * @return static
     */
    public function validate(Request $request, array $validators = [])
    {
        $this->request = $request;
        if (empty($validators)) {
            $validators = $this->validators();
        }
        foreach ($validators as $validatorClass) {
            if (class_exists($validatorClass)) {
                $validator = glsr($validatorClass, ['request' => $this->request])->validate();
                $this->request = $validator->request();
            } else {
                glsr_log()->warning("Class [$validatorClass] not found.");
            }
        }
        $this->blacklisted = Cast::toBool($this->request->blacklisted);
        $this->errors = glsr()->sessionPluck('form_errors', false);
        $this->message = Cast::toString(glsr()->sessionPluck('form_message'));
        return $this;
    }

    public function validators(): array
    {
        return glsr()->filterArray('validators', [ // order is intentional
            DefaultValidator::class,
            CustomValidator::class,
            PermissionValidator::class,
            DuplicateValidator::class,
            HoneypotValidator::class,
            ReviewLimitsValidator::class,
            BlacklistValidator::class,
            AkismetValidator::class,
            FriendlyCaptchaValidator::class,
            HcaptchaValidator::class,
            Recaptcha2Validator::class,
            Recaptcha3Validator::class,
            TurnstileValidator::class,
        ]);
    }

    public function isValid(): bool
    {
        return false === $this->errors;
    }
}
