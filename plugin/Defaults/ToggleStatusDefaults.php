<?php

namespace GeminiLabs\SiteReviews\Defaults;

class ToggleStatusDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     * @var array
     */
    public $casts = [
        'post_id' => 'int',
        'status' => 'string',
    ];

    /**
     * The values that should be constrained after sanitization is run.
     * This is done after $casts and $sanitize.
     * @var array
     */
    public $enums = [
        'status' => ['approve', 'pending', 'publish', 'unapprove'],
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'post_id' => 0,
            'status' => '',
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     * @return array
     */
    protected function finalize(array $values = [])
    {
        $values['status'] = in_array($values['status'], ['approve', 'publish'])
            ? 'publish'
            : 'pending';
        return $values;
    }
}
