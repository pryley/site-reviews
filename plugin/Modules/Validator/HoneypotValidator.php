<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Defaults\ValidateReviewDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;

class HoneyPotValidator
{
    /**
     * @return bool
     */
    public function isValid(array $review)
    {
        return wp_validate_boolean(
            apply_filters('site-reviews/validate/honeypot', $this->validate($review), $review)
        );
    }

    /**
     * @return array
     */
    protected function getExcludedKeys()
    {
        return Arr::unique(array_merge(
            array_keys(glsr(ValidateReviewDefaults::class)->defaults()),
            array_keys(glsr()->config('forms/submission-form')),
            ['blacklisted', 'excluded']
        ));
    }

    /**
     * @param array $request
     * @return bool
     */
    protected function validate($request)
    {
        $excludedKeys = $this->getExcludedKeys();
        foreach ($request as $key => $value) {
            if (Str::startsWith('_', $key) || in_array($key, $excludedKeys)) {
                continue;
            }
            if (!empty($value)) {
                return false;
            }
        }
        return true;
    }
}
