<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Request;

class ValidateReview
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
        foreach ($validators as $validator) {
            if (class_exists($validator)) {
                $this->request = glsr($validator, ['request' => $this->request])->validate();
            } else {
                glsr_log()->warning("Class [$validator] not found.");
            }
        }
        $this->blacklisted = Cast::toBool($this->request->blacklisted);
        $this->errors = glsr()->sessionPluck('form_errors', false);
        $this->message = glsr()->sessionPluck('form_message');
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

    /**
     * @return bool
     */
    public function isValid()
    {
        return false === $this->errors;
    }
}
