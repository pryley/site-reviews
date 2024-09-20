<?php

namespace GeminiLabs\SiteReviews\Modules\Validator;

class PermissionValidator extends ValidatorAbstract
{
    public function isValid(): bool
    {
        if (glsr_get_option('general.require.login', false, 'bool')) {
            return is_user_logged_in();
        }
        return true;
    }

    public function performValidation(): void
    {
        if (!$this->isValid()) {
            $this->fail(__('You must be logged in to submit a review.', 'site-reviews'));
        }
    }
}
