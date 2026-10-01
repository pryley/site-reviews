<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Cast;

/**
 * The colour groups the addons' editors offer beside the theme's palette
 * {name, colors: [{color, name, slug}]}.
 */
class EditorPaletteDefaults extends DefaultsAbstract
{
    protected function defaults(): array
    {
        return [];
    }

    protected function finalize(array $values = []): array
    {
        $groups = [];
        foreach ($values as $group) {
            if (!is_array($group) || '' === ($name = trim(Cast::toString($group['name'] ?? '')))) {
                continue;
            }
            $colors = [];
            foreach ((array) ($group['colors'] ?? []) as $color) {
                $entry = [
                    'color' => trim(Cast::toString(is_array($color) ? ($color['color'] ?? '') : '')),
                    'name' => trim(Cast::toString(is_array($color) ? ($color['name'] ?? '') : '')),
                    'slug' => sanitize_key(Cast::toString(is_array($color) ? ($color['slug'] ?? '') : '')),
                ];
                if (!in_array('', $entry, true)) {
                    $colors[] = $entry;
                }
            }
            if (!empty($colors)) {
                $groups[] = compact('colors', 'name');
            }
        }
        return $groups;
    }
}
