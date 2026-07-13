<?php

namespace GeminiLabs\SiteReviews\TestAddon;

use GeminiLabs\SiteReviews\Addons\Addon;

class Application extends Addon
{
    public const ID = 'site-reviews-test-addon';
    public const LICENSED = true;
    public const NAME = 'Test Addon';
    public const POST_TYPE = 'test-addon-thing';
    public const SLUG = 'test-addon';
}
