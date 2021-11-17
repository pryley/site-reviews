<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Defaults\DefaultsAbstract as Defaults;

class ToggleStatusDefaults extends Defaults
{
    /**
     * @var array
     */
    public $sanitize = [
        'post_id' => 'int',
        'status' => 'text',
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
     * Normalize provided values, this always runs first.
     * @return array
     */
    protected function normalize(array $values = [])
    {
        $values['status'] = 'approve' === glsr_get($values, 'status')
            ? 'publish'
            : 'pending';
        return $values;
    }
}
