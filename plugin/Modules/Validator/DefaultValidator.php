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
    /**
     * @return bool
     */
    public function isValid()
    {
        $this->errors = glsr(Validator::class)->validate(
            $this->request->toArray(),
            $this->rules()
        );
        return empty($this->errors);
    }

    /**
     * @return void
     */
    public function performValidation()
    {
        if (!$this->isValid()) {
            glsr_log()->debug($this->errors);
            $this->setErrors(__('Please fix the submission errors.', 'site-reviews'));
            return;
        }
        $values = glsr(ValidateReviewDefaults::class)->merge($this->request->toArray());
        $this->request = new Request($values);
    }

    /**
     * @return array
     */
    protected function defaultRules()
    {
        $maxRating = max(1, (int) glsr()->constant('MAX_RATING', Rating::class));
        $rules = [
            'content' => 'required',
            'email' => 'required|email',
            'name' => 'required',
            'rating' => 'required|between:0,'.$maxRating,
            'terms' => 'accepted',
            'title' => 'required',
        ];
        return glsr()->filterArray('validation/rules', $rules, $this->request);
    }

    /**
     * @return array
     */
    protected function normalizedRules()
    {
        $rules = $this->defaultRules();
        $required = glsr_get_option('forms.required', []);
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

    /**
     * @return array
     */
    protected function rules()
    {
        $defaultRules = $this->normalizedRules();
        $customRules = array_diff_key($defaultRules,
            glsr(DefaultsManager::class)->pluck('settings.forms.required.options')
        );
        $excluded = Arr::convertFromString($this->request->excluded); // these fields were ommited with the hide option
        $rules = array_merge($defaultRules, $customRules);
        $rules = array_diff_key($rules, array_flip($excluded));
        return glsr()->filterArray('validation/rules/normalized', $rules, $this->request, $defaultRules);
    }
}
