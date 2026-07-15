<?php

namespace GeminiLabs\SiteReviews\TestAddon\Integrations\Example;

use GeminiLabs\SiteReviews\Hooks\AbstractHooks;

/**
 * A minimal integration shipped by the test addon.
 *
 * It exists so Addons\Hooks::runIntegrations() has a real integration to find, reflect over,
 * make a singleton and hang on plugins_loaded. run() leaves a marker a test can assert on; the
 * levelInit()/levelPluginsLoaded() defaults (null) mean runDeferred() registers nothing.
 */
class Hooks extends AbstractHooks
{
    public function run(): void
    {
        add_filter('site-reviews-test-addon/example/loaded', '__return_true');
    }
}
