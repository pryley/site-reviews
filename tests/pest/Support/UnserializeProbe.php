<?php

namespace GeminiLabs\SiteReviews\Tests;

/**
 * A POP gadget, for the deserialization tests. It records that it woke up, which is
 * what unserialize() does to an object before any code gets to inspect it — the reason
 * a value that arrived with the request must never be restored as an object.
 *
 * Action Scheduler, which the plugin bundles and loads on every request, supplies real
 * examples: eight __wakeup() methods that parse a property the payload controls.
 */
class UnserializeProbe
{
    public static bool $awoken = false;

    public string $payload = '';

    public static function reset(): void
    {
        self::$awoken = false;
    }

    public function __wakeup(): void
    {
        self::$awoken = true;
    }
}
