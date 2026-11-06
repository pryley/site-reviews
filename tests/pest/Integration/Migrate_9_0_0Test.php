<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Migrations\Migrate_9_0_0;

use function GeminiLabs\SiteReviews\Tests\licenseServer;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

uses()->group('plugin');

beforeEach(fn () => resetPluginState());

/*
 * The multisite suite covers the mapped sub-site the migration exists for; on a single
 * site the licence URL did not change.
 */

test('the migration deletes the list of cached api responses', function () {
    update_site_option(glsr()->prefix.'api_transients', ['glsr_api_request_abcdefgh_ijklmnop']);

    (new Migrate_9_0_0())->run();

    expect(get_site_option(glsr()->prefix.'api_transients'))->toBeFalse();
});

test('on a single site the migration asks the licence server nothing', function () {
    glsr(OptionManager::class)->set('settings.licenses.site-reviews-images', 'a-saved-key');
    $asked = licenseServer([]);

    expect((new Migrate_9_0_0())->run())->toBeTrue()
        ->and($asked)->toHaveCount(0)
        ->and(glsr_get_option('licenses.site-reviews-images'))->toBe('a-saved-key');
});
