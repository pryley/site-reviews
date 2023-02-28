<?php

namespace GeminiLabs\SiteReviews\Defaults;

class ValidateReviewDefaults extends DefaultsAbstract
{
    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'assign_to' => '',
            'category' => '',
            'content' => '',
            'email' => '',
            'form_id' => '',
            'ip_address' => '',
            'name' => '',
            'rating' => '0',
            'terms' => '',
            'title' => '',
        ];
    }
}
