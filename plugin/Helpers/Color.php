<?php

namespace GeminiLabs\SiteReviews\Helpers;

use GeminiLabs\Spatie\Color\Color as ColorInterface;
use GeminiLabs\Spatie\Color\Exceptions\InvalidColorValue;
use GeminiLabs\Spatie\Color\Factory;

class Color
{
    public static function new(string $color): ?ColorInterface
    {
        try {
            return Factory::fromString($color);
        } catch (InvalidColorValue $e) {
            glsr_log()->error("invalid color [{$color}] {$e->getMessage()}");
            return null;
        }
    }
}
