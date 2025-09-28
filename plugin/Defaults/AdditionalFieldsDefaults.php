<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Arr;

class AdditionalFieldsDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'verified' => 'bool',
        'verified_requested' => 'bool',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'geolocation' => 'array-consolidate',
        'response' => 'text-html',
        'response_by' => 'user-id:0',
        'verified_on' => 'timestamp',
    ];

    protected function defaults(): array
    {
        return [
            'geolocation' => '', // geolocation of review
            'response' => '', // review response
            'response_by' => '', // ID of User who wrote the review response
            'verified' => '', // this is the meta_key used by WooCommerce for verified owner
            'verified_on' => '', // timestamp of when review was verified
            'verified_requested' => '',
        ];
    }
}
