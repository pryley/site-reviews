<?php

namespace GeminiLabs\SiteReviews\ReplacingAddon;

use GeminiLabs\SiteReviews\Addons\Addon;

class Application extends Addon
{
    public const ID = 'site-reviews-replacing-addon';
    public const NAME = 'Replacing Addon';
    public const REPLACES = ['site-reviews-plain-addon', 'site-reviews-test-addon'];
    public const SLUG = 'replacing-addon';
}
