<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Helpers\Cast;

class CustomValidator extends ValidatorAbstract
{
    public function isValid(): bool
    {
        return Cast::toBool(glsr()->filter('validate/custom', true, $this->request));
    }

    public function performValidation(): void
    {
        $validated = glsr()->filter('validate/custom', true, $this->request); // value may be a string
        if (true === Cast::toBool($validated)) {
            return;
        }
        if (!is_string($validated)) {
            $validated = __('The review submission failed. Please notify the site administrator.', 'site-reviews');
        }
        $this->fail($validated);
    }
}
