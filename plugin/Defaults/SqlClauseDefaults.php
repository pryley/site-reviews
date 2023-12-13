<?php

namespace GeminiLabs\SiteReviews\Defaults;

class SqlClauseDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'replace' => 'bool',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     */
    public array $sanitize = [
        'clauses' => 'array-string',
    ];

    protected function defaults(): array
    {
        return [
            'clauses' => [],
            'replace' => true,
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     */
    protected function finalize(array $values = []): array
    {
        $values['clauses'] = array_values(array_unique($values['clauses']));
        return $values;
    }
}
