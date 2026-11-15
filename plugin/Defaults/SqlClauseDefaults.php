<?php

namespace GeminiLabs\SiteReviews\Defaults;

class SqlClauseDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'clauses' => 'array',
        'replace' => 'bool',
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
        $clauses = array_map('trim', array_filter($values['clauses'], 'is_string'));
        $values['clauses'] = array_values(array_unique($clauses));
        return $values;
    }
}
