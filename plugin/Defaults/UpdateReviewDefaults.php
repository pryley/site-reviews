<?php

namespace GeminiLabs\SiteReviews\Defaults;

/**
 * This is only used when updating the Review Post.
 */
class UpdateReviewDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     * @var array
     */
    public $casts = [
        'status' => 'string',
    ];

    /**
     * The values that should be constrained after sanitization is run.
     * This is done after $casts and $sanitize.
     * @var array
     */
    public $enums = [
        'status' => ['pending', 'publish'],
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     * @var array
     */
    public $sanitize = [
        'content' => 'text-multiline',
        'date' => 'date',
        'date_gmt' => 'date',
        'title' => 'text',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'content' => '',
            'date' => '',
            'date_gmt' => '',
            'status' => '',
            'title' => '',
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     * @return array
     */
    protected function normalize(array $values = [])
    {
        if (isset($values['is_approved'])) {
            $values['status'] = wp_validate_boolean($values['is_approved']) ? 'publish' : 'pending';
        } else {
            $values['status'] = '';
        }
        return $values;
    }
}
