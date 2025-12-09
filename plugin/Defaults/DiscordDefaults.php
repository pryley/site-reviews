<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Color;

class DiscordDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     */
    public array $casts = [
        'assigned_links' => 'string',
        'color' => 'string',
        'header' => 'string',
    ];

    protected function defaults(): array
    {
        return [
            'assigned_links' => '',
            'color' => '#FAF089',
            'header' => '',
        ];
    }

    protected function finalize(array $values = []): array
    {
        if ($color = Color::new($values['color'])) {
            $hex = preg_replace('/[^0-9A-Fa-f]/', '', $color->toHex());
            $values['color'] = hexdec($hex);
        } else {
            $values['color'] = '';
        }
        return $values;
    }
}
