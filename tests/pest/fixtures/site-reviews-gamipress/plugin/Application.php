<?php
/*
 * FIXTURE for Addons\Compat: a retired addon. Compat::register() identifies an
 * addon by its ID constant and the main plugin file beside the class's parent
 * directory; site-reviews-gamipress is on the hard-coded retired list.
 */

namespace GeminiLabs\SiteReviews\Tests\Fixtures\Gamipress;

class Application
{
    public const ID = 'site-reviews-gamipress';
}
