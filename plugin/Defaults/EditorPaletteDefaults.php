<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Cast;

/**
 * The colour groups the addons' editors offer beside the theme's palette,
 * each {name, colors: [{color, name, slug}]}.
 *
 * Unused by the plugin itself; the addons read it, so that one filter
 * reaches every addon's editors.
 */
class EditorPaletteDefaults extends DefaultsAbstract
{
    protected function defaults(): array
    {
        return [];
    }

    /**
     * A group needs a name and at least one colour; a colour needs a value,
     * a name and a slug.
     */
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
