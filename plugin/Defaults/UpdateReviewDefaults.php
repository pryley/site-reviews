<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

/**
 * This is only used when updating the Review Post
 */
class UpdateReviewDefaults extends Defaults
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $enums and $sanitize.
     * @var array
     */
    public $casts = [
        'status' => 'string',
    ];

    /**
     * The values that should be constrained before sanitization is run.
     * This is done after $casts and before $sanitize.
     * @var array
     */
    public $enums = [
        'status' => ['approved', 'pending', 'publish', 'unapproved'],
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and $enums.
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
    protected function finalize(array $values = [])
    {
        $mapped = [
            'approved' => 'publish',
            'unapproved' => 'pending',
        ];
        if (array_key_exists($values['status'], $mapped)) {
            $values['status'] = $mapped[$values['status']];
        }
        return $values;
    }
}
