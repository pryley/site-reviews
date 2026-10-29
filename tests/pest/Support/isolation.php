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

use function GeminiLabs\SiteReviews\Tests\forgetTableEngines;
use function GeminiLabs\SiteReviews\Tests\purgeCommittedRows;
use function GeminiLabs\SiteReviews\Tests\resetGlobalState;

$isolation = new Isolation(
    // Tables::createTables() deletes the engine cache and tableEngine() writes it, so a declared
    // test moves these options. Every test starts without them, and the purge removes them again.
    prelude: fn () => forgetTableEngines(),
    reset: fn () => resetGlobalState(),
    purge: fn () => purgeCommittedRows(),
);

return [
    'afterEach' => $isolation->afterEach(),
    'beforeEach' => $isolation->beforeEach(),
];
