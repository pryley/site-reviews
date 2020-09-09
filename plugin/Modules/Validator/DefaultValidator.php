<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Database\DefaultsManager;
use GeminiLabs\SiteReviews\Defaults\ValidateReviewDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Modules\Validator;
use GeminiLabs\SiteReviews\Request;

class DefaultValidator extends ValidatorAbstract
{
    const VALIDATION_RULES = [
        'content' => 'required',
        'email' => 'required|email',
        'name' => 'required',
        'rating' => 'required|number|between:1,5',
        'terms' => 'accepted',
        'title' => 'required',
    ];

    /**
     * @return void
     */
    public function performValidation()
    {
        if (!$this->isValid()) {
            $this->setErrors(__('Please fix the submission errors.', 'site-reviews'));
        }
    }

    /**
     * @return bool
     */
    protected function isValid()
    {
        $this->errors = glsr(Validator::class)->validate(
            $this->request->toArray(),
            $this->rules()
        );
        if (empty($this->errors)) {
            $values = glsr(ValidateReviewDefaults::class)->merge($this->request->toArray());
            $this->request = new Request($values);
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    protected function rules()
    {
        $rules = glsr()->filterArray('validation/rules', static::VALIDATION_RULES, $this->request);
        $customRules = array_diff_key($rules,
            glsr(DefaultsManager::class)->pluck('settings.submissions.required.options')
        );
        $requiredRules = array_intersect_key($rules, 
            array_flip(glsr_get_option('submissions.required', []))
        );
        $rules = array_merge($requiredRules, $customRules);
        $excluded = Arr::convertFromString($this->request->excluded); // these fields were ommited with the hide option
        return array_diff_key($rules, array_flip($excluded));
    }
}
