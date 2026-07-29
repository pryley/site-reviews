<?php

namespace GeminiLabs\SiteReviews\Defaults;

/**
 * ReviewTagsDefaults holds the tags; every declaration it collects is
 * restricted to this shape, so a descriptor written by hand cannot say
 * more than the shape allows.
 */
class ReviewTagDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'display' => 'bool',
        'insert' => 'bool',
    ];

    /**
     * The values that should be restricted to a set.
     * A value outside the set falls back to the default.
     * This is done after $casts and $sanitize.
     */
    public array $enums = [
        'group' => ['default', 'other'],
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'group' => 'text',
        'label' => 'text',
    ];

    protected function defaults(): array
    {
        return [
            'display' => false, // If the tag can be used in a review builder (i.e. Review Themes)
            'group' => 'other',
            'insert' => false, // If the tag can be used in review template editor (i.e. Review Forms)
            'label' => '',
        ];
    }
}
