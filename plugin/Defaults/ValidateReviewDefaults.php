<?php

namespace GeminiLabs\SiteReviews\Defaults;

class ValidateReviewDefaults extends DefaultsAbstract
{
    protected function defaults(): array
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
