<?php

namespace GeminiLabs\SiteReviews\Defaults;

class ToggleStatusDefaults extends DefaultsAbstract
{
    /**
     * The values that should be constrained after sanitization is run.
     * This is done after $casts and $sanitize.
     * @var array
     */
    public $enums = [
        'status' => ['approve', 'pending', 'publish', 'unapprove'],
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     * @var array
     */
    public $sanitize = [
        'post_id' => 'int',
        'status' => 'name',
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
