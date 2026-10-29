<?php

/*
 * Per-test isolation, applied to the four main suites (tests/pest/Pest.php) and the WooCommerce
 * suite (tests/woocommerce/Pest.php): pryley/wp-plugin-tooling's Isolation with core's reset and
 * purge. It returns the beforeEach and afterEach closures each Pest.php hands to uses().
 *
 * resetPluginState() (helpers.php) restores a known-good baseline of plugin settings from a
 * beforeEach in the files that need one — not globally, since replacing the whole options array
 * is not free and most tests skip it.
 */

use Pryley\WpPluginTooling\Pest\Isolation;

use function GeminiLabs\SiteReviews\Tests\forgetDerivedOptions;
use function GeminiLabs\SiteReviews\Tests\purgeCommittedRows;
use function GeminiLabs\SiteReviews\Tests\resetGlobalState;

$isolation = new Isolation(
    // a declared test can add or remove these options, so every test starts without them
    prelude: fn () => forgetDerivedOptions(),
    reset: fn () => resetGlobalState(),
    purge: fn () => purgeCommittedRows(),
);

return [
    'afterEach' => $isolation->afterEach(),
    'beforeEach' => $isolation->beforeEach(),
];
