<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Contracts\ValidatorContract;
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
    public function validate(Request $request, array $validatorClasses = [])
    {
        $this->request = $request;
        $validators = $this->validators($validatorClasses);
        foreach ($validators as $validatorClass) {
            $validator = glsr($validatorClass, ['request' => $this->request])->validate();
            $this->request = $validator->request();
        }
        $this->blacklisted = Cast::toBool($this->request->blacklisted);
        $this->errors = glsr()->sessionPluck('form_errors', false);
        $this->message = Cast::toString(glsr()->sessionPluck('form_message'));
        return $this;
    }

    public function validators(array $validatorClasses = []): array
    {
        $defaults = [ // order is intentional
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
        ];
        if (empty($validatorClasses)) {
            $validatorClasses = $defaults;
        }
        $validatorClasses = glsr()->filterArray('validators', $validatorClasses);
        $validators = [];
        foreach ($validatorClasses as $validatorClass) {
            try {
                $validator = new \ReflectionClass($validatorClass);
            } catch (\ReflectionException $e) {
                glsr_log()->error("Validator not found [$validatorClass]");
                continue;
            }
            if (!$validator->implementsInterface(ValidatorContract::class)) {
                glsr_log()->error("Validator implementation invalid [$validatorClass]");
                continue;
            }
            $validators[] = $validatorClass;
        }
        return $validators;
    }

    public function isValid(): bool
    {
        return false === $this->errors;
    }
}
