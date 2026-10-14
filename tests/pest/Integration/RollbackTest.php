<?php

use GeminiLabs\SiteReviews\Addons\Compat;
use GeminiLabs\SiteReviews\Rollback;
use GeminiLabs\SiteReviews\TestAddon\Application as TestAddon;

use function GeminiLabs\SiteReviews\Tests\createUser;
use function GeminiLabs\SiteReviews\Tests\resetPluginState;

/*
 * The rollback, and knowing which addons are installed.
 *
 * A rollback stores the version to go back to and hands WordPress's own updater what it needs; the
 * download and the unpacking are WordPress's, so what is asserted here is what the plugin stores
 * and returns.
 *
 * Addons\Compat is the other half: from a class name alone it works out which addons are installed,
 * licensed, retired, or too old to update themselves. Wrong, and a paid addon silently stops
 * receiving updates.
 */

beforeEach(function () {
    resetPluginState();
    wp_set_current_user(createUser(['role' => 'administrator']));
});

afterEach(function () {
    // `licensed`, `compat` and `retired` live in memory on the Application SINGLETON — they are
    // not options and they do not roll back. A test addon left in any of them would follow the
    // suite around: the addons page would list it, the update check would look for it, and the
    // licence notices would ask somebody to activate it.
    glsr()->store('compat', []);
    glsr()->store('licensed', []);
    glsr()->store('retired', []);
});

/*
 * The rollback.
 */

test('the rollback remembers which version was asked for, and where to go afterwards', function () {
    // The transient is how the page that comes back knows what happened. A minute is long enough
    // for a download and short enough that a stale one cannot confuse the next attempt.
    $data = glsr(Rollback::class)->rollbackData('8.0.0');

    expect(get_transient(glsr()->prefix.'rollback_version'))->toBe('8.0.0');
    expect($data['data']['action'])->toBe('update-plugin')
        ->and($data['data']['plugin'])->toBe(glsr()->basename)
        ->and($data['data']['slug'])->toBe('site-reviews')
        ->and(wp_verify_nonce($data['data']['_ajax_nonce'], 'updates'))->not->toBeFalse();
    expect($data['url'])->toContain('welcome');
});

/*
 * Which addons are installed, and what the plugin knows about them.
 */

test('an installed addon is recognised, and its licence is registered', function () {
    // From a class name alone: Compat reflects it, finds the plugin file two directories up, and
    // reads its header. The test addon declares LICENSED = true.
    glsr()->store('licensed', []);

    glsr(Compat::class)->register(TestAddon::class);

    expect(glsr()->retrieveAs('array', 'licensed'))
        ->toHaveKey('site-reviews-test-addon');
});

test('an addon with an Update URI updates itself, and is not put in compatibility mode', function () {
    // `compat` is the list of addons too old to know where their own updates come from — the
    // plugin has to fetch them on their behalf. An addon that declares an Update URI is not one
    // of them, and adding it to the list would mean two things asking for the same update.
    glsr()->store('compat', []);

    glsr(Compat::class)->register(TestAddon::class);

    expect(glsr()->retrieveAs('array', 'compat'))->toBe([]);
});

test('a class that does not exist is not an addon', function () {
    // register() is handed class names by anything that thinks it is an addon. A missing class
    // must be a shrug, not a fatal — an addon half-deleted from the plugins directory is a
    // situation, not a crash.
    glsr()->store('licensed', []);

    glsr(Compat::class)->register('This\Class\Does\Not\Exist');

    expect(glsr()->retrieveAs('array', 'licensed'))->toBe([]);
});

test('a retired addon is remembered as retired, and nothing else', function () {
    // Two addons were folded into the plugin. They are still installed on people's sites, and
    // they must not be treated as licensed or as needing updates — they need REMOVING, and the
    // list is what the notice that says so is built from. The fixture is a licensed addon laid
    // out on disk as Compat::register() expects, with no Update URI.
    require_once glsr()->path('tests/pest/fixtures/site-reviews-gamipress/plugin/Application.php');
    $retired = \GeminiLabs\SiteReviews\Tests\Fixtures\Gamipress\Application::class;
    glsr()->store('compat', []);
    glsr()->store('licensed', []);
    glsr()->store('retired', []);

    glsr(Compat::class)->register($retired);

    expect(glsr()->retrieveAs('array', 'retired'))->toBe([$retired])
        ->and(glsr()->retrieveAs('array', 'compat'))->toBe([])
        ->and(glsr()->retrieveAs('array', 'licensed'))->toBe([]);
});
