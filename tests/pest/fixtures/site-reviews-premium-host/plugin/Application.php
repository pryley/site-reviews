<?php

namespace GeminiLabs\SiteReviews\Premium\Host;

use GeminiLabs\SiteReviews\Addons\Addon;

class Application extends Addon
{
    public const ID = 'site-reviews-premium-host';
    public const NAME = 'Premium Host';
    /**
     * As premium declares the standalones its modules replace: the hosted fixture registers under
     * the first id with this fixture as its host, and the replacing fixture is a standalone this one shelves.
     */
    public const REPLACES = ['site-reviews-hosted-addon', 'site-reviews-replacing-addon'];
    public const SLUG = 'premium-host';
}
