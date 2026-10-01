<?php

namespace GeminiLabs\SiteReviews\Defaults;

/**
 * The responsive scale for the addons
 */
class BreakpointsDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'desktop' => 'int',
        'mobile' => 'int',
        'tablet' => 'int',
    ];

    protected function defaults(): array
    {
        return [
            'mobile' => 0,
            'tablet' => 600,
            'desktop' => 960,
        ];
    }

    /**
     * Finalize provided values, this always runs last.
     * A custom field name becomes a meta key. A name that sanitize_key() changes is refused.
     */
    protected function finalize(array $values = []): array
    {
        $scale = $this->defaults();
        $widths = [];
        foreach ($scale as $key => $default) {
            $widths[$key] = max(0, (int) ($values[$key] ?? $default));
        }
        $widths['mobile'] = 0;
        sort($widths);
        return array_combine(array_keys($scale), $widths);
    }
}
