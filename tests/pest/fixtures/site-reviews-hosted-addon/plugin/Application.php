<?php

namespace GeminiLabs\SiteReviews\Premium\HostedThing;

use GeminiLabs\SiteReviews\Addons\Addon;

/**
 * A FIXTURE for hosted-addon mode: unlike site-reviews-test-addon there is
 * deliberately NO site-reviews-hosted-addon.php main file beside plugin/ —
 * the missing derived main file is exactly what switches Addon::__construct()
 * into the hosted shape when a $host is passed (and what makes registration
 * fail when none is). The host fixture's REPLACES names this id, as premium
 * names the standalone each of its modules replaces, so this fixture can also
 * prove that a module passes under an id its own host declared.
 *
 * Not autoloaded via composer; the tests require this file directly.
 */
class Application extends Addon
{
    public const ID = 'site-reviews-hosted-addon';
    public const NAME = 'Hosted Addon';
    public const SLUG = 'hosted-thing';
}
