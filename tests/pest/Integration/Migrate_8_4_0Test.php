<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Migrations\Migrate_8_4_0;

use function GeminiLabs\SiteReviews\Tests\licenseServer;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

uses()->group('plugin');

beforeEach(fn () => resetPluginState());

/*
 * The multisite suite covers the mapped sub-site the migration exists for; on a single
 * site the licence URL did not change.
 */

test('on a single site the migration asks the licence server nothing', function () {
    glsr(OptionManager::class)->set('settings.licenses.site-reviews-images', 'a-saved-key');
    $asked = licenseServer([]);

    expect((new Migrate_8_4_0())->run())->toBeTrue()
        ->and($asked)->toHaveCount(0)
        ->and(glsr_get_option('licenses.site-reviews-images'))->toBe('a-saved-key');
});
