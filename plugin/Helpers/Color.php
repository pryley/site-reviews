<?php

namespace GeminiLabs\SiteReviews\Helpers;

use GeminiLabs\Spatie\Color\Exceptions\InvalidColorValue;
use GeminiLabs\Spatie\Color\Factory;

class Color
{
    /**
     * @return \GeminiLabs\Spatie\Color\Color|\WP_Error
     */
    public static function new(string $color)
    {
        try {
            return Factory::fromString($color);
        } catch (InvalidColorValue $e) {
            return new \WP_Error('invalid_color', $e->getMessage(), $color);
        }
    }
}
