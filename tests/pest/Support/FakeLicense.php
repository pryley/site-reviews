<?php

namespace GeminiLabs\SiteReviews\Tests;

use GeminiLabs\SiteReviews\License;

/**
 * A licence that answers without asking anybody.
 *
 * License::isPremium() reads License::status(), which asks the licence server. Any test whose
 * subject merely BRANCHES on whether the site is premium — the flyout menu, the addons page,
 * the settings tabs — does not want to stand up a licence to find that out.
 *
 * Bound through the container, the same way NullQueue is (see bootstrap.php). It is a named
 * class rather than an anonymous one because Container::bind() takes a class name and
 * reflects on it; hand it an object and it will try to use the object as an array key.
 */
class FakeLicense extends License
{
    public static bool $isPremium = false;

    public function isPremium(): bool
    {
        return static::$isPremium;
    }
}
