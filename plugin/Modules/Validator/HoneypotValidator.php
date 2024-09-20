<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Modules\Honeypot;

class HoneypotValidator extends ValidatorAbstract
{
    public function isValid(): bool
    {
        $hash = glsr(Honeypot::class)->hash($this->request->form_id);
        $isValid = isset($this->request[$hash]) && empty($this->request[$hash]);
        return glsr()->filterBool('validate/honeypot', $isValid, $this->request);
    }

    public function performValidation(): void
    {
        if (!$this->isValid()) {
            $this->fail(
                __('This review has been flagged as possible spam and cannot be submitted.', 'site-reviews'),
                'The Honeypot caught a bad submission.'
            );
        }
    }
}
