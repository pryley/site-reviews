<?php

namespace GeminiLabs\SiteReviews\Defaults;

class RatingDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     * @var array
     */
    public $casts = [
        'ID' => 'int',
        'is_approved' => 'bool',
        'is_pinned' => 'bool',
        'is_verified' => 'bool',
        'review_id' => 'int',
        'terms' => 'bool',
    ];

    /**
     * The values that should be guarded.
     * @var string[]
     */
    public $guarded = [
        'ID',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     * @var array
     */
    public $sanitize = [
        'avatar' => 'url',
        'email' => 'email',
        'ip_address' => 'text',
        'name' => 'text',
        'rating' => 'rating',
        'score' => 'min:0',
        'type' => 'slug',
        'url' => 'url',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'avatar' => '',
            'email' => '',
            'ID' => '',
            'ip_address' => '',
            'is_approved' => false,
            'is_pinned' => false,
            'is_verified' => false,
            'name' => '',
            'rating' => '',
            'review_id' => '',
            'score' => '',
            'terms' => true,
            'type' => '',
            'url' => '',
        ];
    }
}
