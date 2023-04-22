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
        'color' => 'string',
        'content' => 'string',
        'edit_url' => 'string',
    ];

    /**
     * The values that should be sanitized.
     * This is done after $casts and before $enums.
     * @var array
     */
    public $sanitize = [
        'avatar_url' => 'url',
        'edit_url' => 'url',
    ];

    /**
     * @return array
     */
    protected function defaults()
    {
        return [
            'avatar_url' => glsr()->url('assets/images/icon.png'),
            'color' => '#FAF089',
            'content' => '',
            'edit_url' => '',
            'username' => glsr()->name,
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
