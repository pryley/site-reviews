<?php

namespace GeminiLabs\SiteReviews\Defaults;

use GeminiLabs\SiteReviews\Helpers\Color;

class DiscordDefaults extends DefaultsAbstract
{
    /**
     * The values that should be cast before sanitization is run.
     * This is done before $sanitize and $enums.
     * @var array
     */
    public $casts = [
        'assigned_links' => 'string',
        'color' => 'string',
        'header' => 'string',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'assigned_links' => '',
            'color' => '#FAF089',
            'header' => '',
        ];
    }

    /**
     * @return array
     */
    protected function finalize(array $values = [])
    {
        $color = Color::new($values['color']);
        if (is_wp_error($color)) {
            $values['color'] = '';
        } else {
            $hex = preg_replace('/[^0-9A-Fa-f]/', '', $color->toHex());
            $values['color'] = hexdec($hex);
        }
        return $values;
    }
}
