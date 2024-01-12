<?php

namespace GeminiLabs\SiteReviews\Defaults;

class ValidationStringsDefaults extends DefaultsAbstract
{
    protected function defaults(): array
    {
        return [
            'accepted' => __('This field must be accepted.', 'site-reviews'),
            'between' => __('This field value must be between %s and %s.', 'site-reviews'),
            'betweenlength' => __('This field must have between %s and %s characters.', 'site-reviews'),
            'email' => __('This field requires a valid e-mail address.', 'site-reviews'),
            'errors' => __('Please fix the submission errors.', 'site-reviews'),
            'max' => __('Maximum value for this field is %s.', 'site-reviews'),
            'maxfiles' => __('This field allows a maximum of %s files.', 'site-reviews'),
            'maxlength' => __('This field allows a maximum of %s characters.', 'site-reviews'),
            'min' => __('Minimum value for this field is %s.', 'site-reviews'),
            'minfiles' => __('This field requires a minimum of %s files.', 'site-reviews'),
            'minlength' => __('This field requires a minimum of %s characters.', 'site-reviews'),
            'number' => __('This field requires a number.', 'site-reviews'),
            'pattern' => __('Please match the requested format.', 'site-reviews'),
            'regex' => __('Please match the requested format.', 'site-reviews'),
            'required' => __('This field is required.', 'site-reviews'),
            'tel' => __('This field requires a valid telephone number.', 'site-reviews'),
            'url' => __('This field requires a valid website URL (make sure it starts with http or https).', 'site-reviews'),
            'unsupported' => __('The review could not be submitted because this browser is too old. Please try again with a modern browser.', 'site-reviews'),
        ];
    }
}
