<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Defaults\ValidateReviewDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Modules\Html\ReviewForm;
use GeminiLabs\SiteReviews\Modules\Validator;
use GeminiLabs\SiteReviews\Request;

class DefaultValidator extends ValidatorAbstract
{
    public function isValid(): bool
    {
        $this->errors = glsr(Validator::class)->validate(
            $this->request->toArray(),
            $this->rules(),
        );
        return empty($this->errors);
    }

    public function performValidation(): void
    {
        if ($this->isValid()) {
            return;
        }
        $this->fail(__('Please fix the form errors.', 'site-reviews'));
    }

    public function request(): Request
    {
        $values = glsr(ValidateReviewDefaults::class)->merge($this->request->toArray());
        return new Request($values);
    }

    public function rules(): array
    {
        $form = new ReviewForm([], $this->request->toArray());
        // skip fields which are conditionally hidden
        $fields = array_filter($form->visible(), fn ($field) => !$field->is_hidden);
        $rules = array_filter(wp_list_pluck($fields, 'validation', 'original_name'));
        // exclude fields omitted with the hide option
        $excluded = Arr::convertFromString($this->request->cast('excluded', 'string'));
        $rules = array_diff_key($rules, array_flip($excluded));
        return glsr()->filterArray('validation/rules', $rules, $this->request);
    }
}
