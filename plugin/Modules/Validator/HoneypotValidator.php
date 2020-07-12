<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Defaults\ValidateReviewDefaults;
use GeminiLabs\SiteReviews\Helpers\Arr;
use GeminiLabs\SiteReviews\Helpers\Str;
use GeminiLabs\SiteReviews\Modules\Honeypot;

class HoneyPotValidator
{
    /**
     * @return bool
     */
    public function isValid(array $review)
    {
        return glsr()->filterBool('validate/honeypot', $this->validate($review), $review);
    }

    /**
     * @param array $request
     * @return bool
     */
    protected function validate($request)
    {
        $hash = glsr(Honeypot::class)->hash(Arr::get($request, 'form_id'));
        if (array_key_exists($hash, $request)) {
            return empty($request[$hash]);
        }
        return false;
    }
}
