<?php

namespace GeminiLabs\SiteReviews\Tests;

/**
 * Records that it was constructed. A route that resolves a class named in the request
 * through the container builds it by reflection, and this shows whether that happened.
 */
class ConstructorProbe
{
    public static bool $constructed = false;

    public function __construct()
    {
        self::$constructed = true;
    }

    public static function reset(): void
    {
        self::$constructed = false;
    }

    public function dismiss(): void
    {
    }
}
