<?php

/*
 * The hosted-addon fixture again, but in a namespace OUTSIDE the premium prefix
 * (GeminiLabs\SiteReviews\Premium\). Addon::hostedFile() maps a module's
 * plugin/ paths from its namespace, and a module that is not under the premium
 * prefix falls back to its last namespace segment — this class exists to give
 * that fallback a caller.
 */

namespace GeminiLabs\OutsideVendor\HostedThing;

class Application extends \GeminiLabs\SiteReviews\Premium\HostedThing\Application
{
}
