<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Database\DefaultsManager;
use GeminiLabs\SiteReviews\Defaults\ValidateReviewDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Modules\Validator;
use GeminiLabs\SiteReviews\Request;

class DefaultValidator extends ValidatorAbstract
{
    public function isValid(): bool
    {
        $this->errors = glsr(Validator::class)->validate(
            $this->request->toArray(),
            $this->rules()
        );
        return empty($this->errors);
    }

    public function performValidation(): void
    {
        if ($this->isValid()) {
            return;
        }
        $this->setErrors(__('Please fix the form errors.', 'site-reviews'));
    }

    public function request(): Request
    {
        $values = glsr(ValidateReviewDefaults::class)->merge($this->request->toArray());
        return new Request($values);
    }

    public function rules(): array
    {
        $defaultRules = $this->normalizedRules();
        $customRules = array_diff_key($defaultRules,
            glsr(DefaultsManager::class)->pluck('settings.forms.required.options')
        );
        $rules = array_merge($defaultRules, $customRules);
        // exclude fields omitted with the hide option
        $excluded = Cast::toArray($this->request->decrypt('excluded'));
        $rules = array_diff_key($rules, array_flip($excluded));
        return glsr()->filterArray('validation/rules/normalized', $rules, $this->request, $defaultRules);
    }

    protected function defaultRequired(): array
    {
        return glsr_get_option('forms.required', []);
    }

    protected function defaultRules(): array
    {
        $maxRating = max(1, (int) glsr()->constant('MAX_RATING', Rating::class));
        $rules = [
            'content' => 'required',
            'email' => 'required|email',
            'name' => 'required',
            'rating' => "required|between:0,{$maxRating}",
            'terms' => 'accepted',
            'title' => 'required',
        ];
        return glsr()->filterArray('validation/rules', $rules, $this->request);
    }

    protected function normalizedRules(): array
    {
        $rules = $this->defaultRules();
        $required = $this->defaultRequired();
        array_walk($rules, function (&$value, $key) use ($required) {
            if (!in_array($key, $required)) {
                // remove the accepted and required rules from validation
                // since they are not required in the settings
                $values = explode('|', $value);
                $values = array_diff($values, ['accepted', 'required']);
                $value = implode('|', $values);
            }
        });
        return $rules;
    }
}
