<?php

use GeminiLabs\SiteReviews\Database\OptionManager;
use GeminiLabs\SiteReviews\Migrations\Migrate_9_0_0;
use GeminiLabs\SiteReviews\Modules\Console;

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

test('the migration deletes the per-addon rows 8.3.6 kept for the last licence server answer', function () {
    update_option(glsr()->prefix.'license_answer_site-reviews-images', ['check' => [], 'key' => 'abcdefgh', 'time' => time()]);
    update_option(glsr()->prefix.'license_answer_site-reviews-forms', ['check' => [], 'key' => 'abcdefgh', 'time' => time()]);

    (new Migrate_9_0_0())->run();

    expect(get_option(glsr()->prefix.'license_answer_site-reviews-images'))->toBeFalse()
        ->and(get_option(glsr()->prefix.'license_answer_site-reviews-forms'))->toBeFalse();
});

test('on a single site the migration asks the licence server nothing', function () {
    glsr(OptionManager::class)->set('settings.licenses.site-reviews-images', 'a-saved-key');
    $asked = licenseServer([]);

    expect((new Migrate_9_0_0())->run())->toBeTrue()
        ->and($asked)->toHaveCount(0)
        ->and(glsr_get_option('licenses.site-reviews-images'))->toBe('a-saved-key');
});

test('the migration moves a Console saved at level 0 to level 1, and leaves any other level alone', function () {
    update_option(Console::LOG_LEVEL_KEY, '0'); // as the 8.x form saved it
    (new Migrate_9_0_0())->run();
    expect((int) get_option(Console::LOG_LEVEL_KEY))->toBe(Console::INFO);

    update_option(Console::LOG_LEVEL_KEY, Console::WARNING);
    (new Migrate_9_0_0())->run();
    expect((int) get_option(Console::LOG_LEVEL_KEY))->toBe(Console::WARNING);

    delete_option(Console::LOG_LEVEL_KEY);
    (new Migrate_9_0_0())->run();
    expect(get_option(Console::LOG_LEVEL_KEY))->toBeFalse();
});
