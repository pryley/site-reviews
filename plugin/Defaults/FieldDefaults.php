<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Cast;
use GeminiLabs\SiteReviews\Helpers\Str;

/**
 * This is used by Builder  to generate HTML elements.
 */
class FieldDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     * @var array
     */
    public $casts = [
        'after' => 'string',
        'description' => 'string',
        'id' => 'string',
        'label' => 'string',
        'name' => 'string',
        'text' => 'string',
        'type' => 'string',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     * @var array
     */
    public $sanitize = [
        'class' => 'attr-class',
        'options' => 'array-consolidate',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'class' => '',
            'description' => '',
            'id' => '',
            'label' => '',  // this value is likely a generated HTML string
            'name' => '',
            'options' => [],
            'text' => '', // this value is likely a generated HTML string
            'type' => '',
            'value' => '', // this value can also be an array
        ];
    }

    /**
     * @return bool
     */
    protected function isMultiField(array $args)
    {
        $args = glsr()->args($args);
        if ('checkbox' === $args->type && count($args->cast('options', 'array')) > 1) {
            return true;
        }
        return Cast::toBool($args->multiple ?? false);
    }

    /**
     * Normalize provided values, this always runs first.
     * @return array
     */
    protected function normalize(array $values = [])
    {
        if ($this->isMultiField($values) && !empty($values['name'])) {
            $values['name'] = Str::suffix($values['name'], '[]');
        }
        return $values;
    }
}
