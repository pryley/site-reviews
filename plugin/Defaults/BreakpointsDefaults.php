<?php

namespace GeminiLabs\SiteReviews\Defaults;

/**
 * The responsive scale the addons share: each value is the width, in
 * pixels, at which that device STARTS. Core's theme.json
 * settings.viewport values are the opposite (the width a device ends
 * at), so they cannot be copied across.
 *
 * Unused by the plugin itself; the addons read it, so that one filter
 * sets the scale for all of them.
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
     * A filter may add keys, drop keys, or return the widths out of
     * order; the scale is repaired rather than rejected: the three keys
     * in device order, the smallest device starting at 0, and the
     * widths ascending.
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
