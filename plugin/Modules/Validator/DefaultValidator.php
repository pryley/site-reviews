<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Database\DefaultsManager;
use GeminiLabs\SiteReviews\Defaults\ValidateReviewDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Rating;
use GeminiLabs\SiteReviews\Modules\Validator;
use GeminiLabs\SiteReviews\Request;

class DefaultValidator extends ValidatorAbstract
{
    const VALIDATION_RULES = [
        'content' => 'required',
        'email' => 'required|email',
        'name' => 'required',
        'rating' => 'required|between:0,5',
        'terms' => 'accepted',
        'title' => 'required',
    ];

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
     * This only validates the provided values in the Request
     * @return bool
     */
    public function isValidRequest()
    {
        $options = glsr(DefaultsManager::class)->pluck('settings.submissions.required.options');
        $excludedKeys = array_keys(array_diff_key($options, $this->request->toArray()));
        $this->request->excluded = $excludedKeys;
        if ($this->isValid()) {
            return true;
        }
        glsr_log()->warning($this->errors);
        return false;
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
    protected function normalizedRules()
    {
        $rules = static::VALIDATION_RULES;
        $maxRating = max(1, Cast::toInt(glsr()->constant('MAX_RATING', Rating::class)));
        $rules['rating'] = str_replace('between:1,5', 'between:1,'.$maxRating, $rules['rating']);
        return glsr()->filterArray('validation/rules', $rules, $this->request);
    }

    /**
     * @return array
     */
    protected function rules()
    {
        $rules = $this->normalizedRules();
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
