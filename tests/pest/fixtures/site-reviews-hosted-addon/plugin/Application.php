<?php

namespace GeminiLabs\SiteReviews\Premium\HostedThing;

use GeminiLabs\SiteReviews\Addons\Addon;

/**
 * A FIXTURE for hosted-addon mode: unlike site-reviews-test-addon there is
 * deliberately NO site-reviews-hosted-addon.php main file beside plugin/ —
 * the missing derived main file is exactly what switches Addon::__construct()
 * into the hosted shape when a $host is passed (and what makes registration
 * fail when none is). The namespace sits under GeminiLabs\SiteReviews\Premium
 * because that is the namespace Application::register()'s suppression guard
 * admits, so this fixture can also prove the guard passes hosted modules.
 *
 * Not autoloaded via composer; the tests require this file directly.
 */
class Application extends Addon
{
    public const ID = 'site-reviews-hosted-addon';
    public const NAME = 'Hosted Addon';
    public const SLUG = 'hosted-thing';
}
