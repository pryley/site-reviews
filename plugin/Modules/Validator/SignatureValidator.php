<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

use GeminiLabs\SiteReviews\Helpers\Cast;

class SignatureValidator extends ValidatorAbstract
{
    public function isValid(): bool
    {
        $isValid = true;
        $signature = $this->request->decrypt('form_signature');
        $values = maybe_unserialize($signature);
        $values = wp_parse_args($values, ['form_id' => '']);
        foreach ($values as $key => $value) {
            if (Cast::toString($value) !== $this->request->cast($key, 'string')) {
                $isValid = false;
                glsr_log()->debug($values);
                break;
            }
        }
        return glsr()->filterBool('validate/signature', $isValid, $values, $this->request);
    }

    public function performValidation(): void
    {
        if (!$this->isValid()) {
            $this->fail(
                __('This review cannot be submitted, please refresh the page and try again.', 'site-reviews'),
                'The form signature could not be verified.'
            );
        }
    }
}
