<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Arguments;
use GeminiLabs\SiteReviews\Contracts\ValidatorContract;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Request;

class ValidateForm
{
    protected Request $request;

    public function __construct()
    {
        $this->request = new Request();
        glsr()->sessionPluck('form_blacklisted');
        glsr()->sessionPluck('form_errors');
        glsr()->sessionPluck('form_invalid');
        glsr()->sessionPluck('form_message');
    }

    /**
     * @compat
     * @todo remove in v8
     */
    public function __get($property)
    {
        $result = $this->result();
        if ('message' === $property) {
            return $result->$property;
        }
        if ('errors' !== $property) {
            return;
        }
        if (!empty($result->errors)) {
            return $result->errors;
        }
        if (!$result->failed) {
            return false; // validation success
        }
        return []; // validation fail
    }

    public function result(): Arguments
    {
        return glsr()->args([
            'blacklisted' => glsr()->session()->cast('form_blacklisted', 'bool'),
            'errors' => glsr()->session()->array('form_errors'),
            'failed' => glsr()->session()->cast('form_invalid', 'bool'),
            'message' => glsr()->session()->cast('form_message', 'string'),
        ]);
    }

    public function isValid(): bool
    {
        if (false === $this->result()->failed) {
            glsr()->sessionClear();
            return true;
        }
        return false;
    }

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
        return $this;
    }

    public function validators(array $validatorClasses = []): array
    {
        $defaults = [ // order is intentional
            DefaultValidator::class,
            CustomValidator::class,
            SignatureValidator::class,
            PermissionValidator::class,
            DuplicateValidator::class,
            HoneypotValidator::class,
            ReviewLimitsValidator::class,
            BlacklistValidator::class,
            AkismetValidator::class,
            FriendlycaptchaValidator::class,
            HcaptchaValidator::class,
            ProcaptchaValidator::class,
            RecaptchaV2InvisibleValidator::class,
            RecaptchaV3Validator::class,
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
}
