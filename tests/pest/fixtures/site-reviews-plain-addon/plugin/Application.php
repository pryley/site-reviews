<?php

namespace GeminiLabs\SiteReviews\PlainAddon;

use GeminiLabs\SiteReviews\Addons\Addon;

/**
 * The plainest addon there is: no post type (about half the real addons have
 * none) and no plugin/Integrations directory. It exists to exercise the addon
 * framework's "this addon does not do that" branches, which the test addon —
 * which does everything — can never reach.
 */
class Application extends Addon
{
    public const ID = 'site-reviews-plain-addon';
    public const NAME = 'Plain Addon';
    public const SLUG = 'plain-addon';
}
